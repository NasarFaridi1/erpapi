<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class PowerBiController extends Controller
{
    /**
     * Helper to return clean array of associative objects for Table.FromRecords compatibility.
     */
    private function formatForTable($data)
    {
        if (is_null($data)) {
            return [];
        }

        $array = json_decode(json_encode($data), true);

        if (empty($array)) {
            return [];
        }

        // If single associative object (like contactInformation), wrap in array
        if (array_keys($array) !== range(0, count($array) - 1)) {
            return [$array];
        }

        return array_values($array);
    }

    /**
     * All Contracts Master with Supplier, Customer, Countries, Operating Companies, and Payment Terms
     */
    public function contracts()
    {
        try {
            $contracts = DB::table('contracts as c')
                ->leftJoin('deal as pd', 'pd.id', '=', 'c.purchase_id')
                ->leftJoin('deal as sd', 'sd.id', '=', 'c.sale_id')
                ->leftJoin('companies as p_cmp', 'p_cmp.id', '=', 'pd.meta_company_id')
                ->leftJoin('companies as s_cmp', 's_cmp.id', '=', 'sd.meta_company_id')
                ->leftJoin('contacts as p_ct', 'p_ct.id', '=', 'pd.contact_id')
                ->leftJoin('contacts as s_ct', 's_ct.id', '=', 'sd.contact_id')
                ->leftJoin('countries as p_co', 'p_co.id', '=', 'p_ct.country_id')
                ->leftJoin('countries as s_co', 's_co.id', '=', 's_ct.country_id')
                ->leftJoin('companies as p_client_cmp', 'p_client_cmp.id', '=', 'p_ct.company_id')
                ->leftJoin('companies as s_client_cmp', 's_client_cmp.id', '=', 's_ct.company_id')
                ->leftJoin('payment_type as p_pt', 'p_pt.id', '=', 'pd.payment_type_id')
                ->leftJoin('payment_terms_type as p_ptt', 'p_ptt.id', '=', 'pd.payment_terms_type_id')
                ->leftJoin('payment_type as s_pt', 's_pt.id', '=', 'sd.payment_type_id')
                ->leftJoin('payment_terms_type as s_ptt', 's_ptt.id', '=', 'sd.payment_terms_type_id')
                ->select(
                    'c.id as contract_id',
                    'c.order_code',
                    'c.sales_invoice_number',
                    'p_ct.name as supplier_name',
                    'p_ct.code_meta as supplier_code',
                    'p_co.name as supplier_country',
                    'p_client_cmp.name as supplier_company',
                    'p_cmp.name as purchase_meta_company',
                    'p_pt.description as purchase_payment_type',
                    'p_ptt.description as purchase_payment_terms',
                    's_ct.name as customer_name',
                    's_ct.code_meta as customer_code',
                    's_co.name as customer_country',
                    's_client_cmp.name as customer_company',
                    's_cmp.name as sales_meta_company',
                    's_pt.description as sales_payment_type',
                    's_ptt.description as sales_payment_terms'
                )
                ->orderBy('c.id', 'DESC')
                ->get();

            return response()->json($this->formatForTable($contracts), 200);

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to fetch contracts',
                'details' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * All Contacts with full relationships resolved (Country Name, Company Name)
     */
    public function contacts()
    {
        try {
            $contacts = DB::table('contacts as c')
                ->leftJoin('countries as co', 'co.id', '=', 'c.country_id')
                ->leftJoin('companies as cp', 'cp.id', '=', 'c.company_id')
                ->select(
                    'c.id as contact_id',
                    'c.code_meta as contact_code',
                    'c.name as contact_name',
                    'co.name as country',
                    'cp.name as company_name',
                    'c.registration',
                    'c.vat',
                    DB::raw("COALESCE(NULLIF(c.currency, ''), 'USD') as currency"),
                    'c.website'
                )
                ->orderBy('c.id', 'ASC')
                ->get();

            return response()->json($this->formatForTable($contacts));

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to fetch contacts',
                'details' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Single Contact Information with Country & Company Names resolved
     */
    public function contactInformation($contactId)
    {
        try {
            $contact = DB::table('contacts as c')
                ->leftJoin('countries as co', 'co.id', '=', 'c.country_id')
                ->leftJoin('companies as cp', 'cp.id', '=', 'c.company_id')
                ->select(
                    'c.id as contact_id',
                    'c.code_meta as contact_code',
                    'c.name as contact_name',
                    'co.name as country',
                    'cp.name as company_name',
                    'c.registration',
                    'c.vat',
                    DB::raw("COALESCE(NULLIF(c.currency, ''), 'USD') as currency"),
                    'c.website'
                )
                ->where('c.id', $contactId)
                ->first();

            if (!$contact) {
                return response()->json([], 404);
            }

            return response()->json($this->formatForTable($contact));

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Something went wrong',
                'details' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Purchases by Contact ID with Real Product Line Data (deal_product)
     */
    public function purchases($contactId)
    {
        try {
            $data = DB::table('contracts as c')
                ->whereNotNull('c.purchase_id')
                ->join('deal as d', 'd.id', '=', 'c.purchase_id')
                ->leftJoin('deal_products as d_rel', 'd_rel.deal_id', '=', 'd.id')
                ->leftJoin('deal_product as dp', function ($join) {
                    $join->on('dp.id', '=', 'd_rel.products_id')
                         ->orOn('dp.associated_contract_id', '=', 'c.id')
                         ->orOn('dp.contract_order_code', '=', 'c.order_code');
                })
                ->leftJoin('products as p', 'p.id', '=', 'dp.product_id')
                ->leftJoin('contacts as ct', 'ct.id', '=', 'd.contact_id')
                ->leftJoin('countries as co', 'co.id', '=', 'ct.country_id')
                ->leftJoin('companies as supplier_cmp', 'supplier_cmp.id', '=', 'ct.company_id')
                ->leftJoin('companies as cmp', 'cmp.id', '=', 'd.meta_company_id')
                ->leftJoin('payment_type as pt', 'pt.id', '=', 'd.payment_type_id')
                ->leftJoin('payment_terms_type as ptt', 'ptt.id', '=', 'd.payment_terms_type_id')
                ->where('d.contact_id', $contactId)
                ->select(
                    'c.id as contract_id',
                    'c.order_code',
                    'c.sales_invoice_number',
                    'ct.name as supplier_name',
                    'ct.code_meta as supplier_code',
                    'co.name as country',
                    'supplier_cmp.name as supplier_company',
                    'cmp.name as meta_company',
                    'p.name as product_name',
                    'dp.quantity',
                    'dp.premium',
                    'dp.rate',
                    'dp.total_price',
                    DB::raw("COALESCE(NULLIF(ct.currency, ''), 'USD') as currency"),
                    'pt.description as payment_type',
                    'ptt.description as payment_terms',
                    'dp.start_date',
                    'dp.end_date',
                    'ct.registration as supplier_registration',
                    'ct.vat as supplier_vat',
                    'ct.website as supplier_website'
                )
                ->orderBy('c.id', 'DESC')
                ->get();

            return response()->json($this->formatForTable($data));

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to fetch purchases',
                'details' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Sales by Contact ID with Real Product Line Data (deal_product)
     */
    public function sales($contactId)
    {
        try {
            $data = DB::table('contracts as c')
                ->whereNotNull('c.sale_id')
                ->join('deal as d', 'd.id', '=', 'c.sale_id')
                ->leftJoin('deal_products as d_rel', 'd_rel.deal_id', '=', 'd.id')
                ->leftJoin('deal_product as dp', function ($join) {
                    $join->on('dp.id', '=', 'd_rel.products_id')
                         ->orOn('dp.associated_contract_id', '=', 'c.id')
                         ->orOn('dp.contract_order_code', '=', 'c.order_code');
                })
                ->leftJoin('products as p', 'p.id', '=', 'dp.product_id')
                ->leftJoin('contacts as ct', 'ct.id', '=', 'd.contact_id')
                ->leftJoin('countries as co', 'co.id', '=', 'ct.country_id')
                ->leftJoin('companies as client_cmp', 'client_cmp.id', '=', 'ct.company_id')
                ->leftJoin('companies as cmp', 'cmp.id', '=', 'd.meta_company_id')
                ->leftJoin('payment_type as pt', 'pt.id', '=', 'd.payment_type_id')
                ->leftJoin('payment_terms_type as ptt', 'ptt.id', '=', 'd.payment_terms_type_id')
                ->where('d.contact_id', $contactId)
                ->select(
                    'c.id as contract_id',
                    'c.order_code',
                    'c.sales_invoice_number',
                    'ct.name as customer_name',
                    'ct.code_meta as customer_code',
                    'co.name as country',
                    'client_cmp.name as customer_company',
                    'cmp.name as meta_company',
                    'p.name as product_name',
                    'dp.quantity',
                    'dp.premium',
                    'dp.rate',
                    'dp.total_price',
                    DB::raw("COALESCE(NULLIF(ct.currency, ''), 'USD') as currency"),
                    'pt.description as payment_type',
                    'ptt.description as payment_terms',
                    'dp.start_date',
                    'dp.end_date',
                    'ct.registration as customer_registration',
                    'ct.vat as customer_vat',
                    'ct.website as customer_website'
                )
                ->orderBy('c.id', 'DESC')
                ->get();

            return response()->json($this->formatForTable($data));

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to fetch sales',
                'details' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Buying Payment Terms with Payment Type & Terms Descriptions
     */
    public function buyingPaymentTerms($contactId)
    {
        try {
            $data = DB::table('contracts as c')
                ->join('deal as d', 'd.id', '=', 'c.purchase_id')
                ->leftJoin('deal_products as d_rel', 'd_rel.deal_id', '=', 'd.id')
                ->leftJoin('deal_product as dp', function ($join) {
                    $join->on('dp.id', '=', 'd_rel.products_id')
                         ->orOn('dp.associated_contract_id', '=', 'c.id')
                         ->orOn('dp.contract_order_code', '=', 'c.order_code');
                })
                ->leftJoin('payment_type as pt', 'pt.id', '=', 'd.payment_type_id')
                ->leftJoin('payment_terms_type as ptt', 'ptt.id', '=', 'd.payment_terms_type_id')
                ->select(
                    'pt.description as payment_type',
                    'ptt.description as payment_terms',
                    DB::raw('COUNT(DISTINCT c.id) as total_contracts'),
                    DB::raw('COALESCE(SUM(dp.quantity), 0) as total_quantity'),
                    DB::raw('COALESCE(SUM(dp.total_price), 0) as total_value')
                )
                ->where('d.contact_id', $contactId)
                ->groupBy('pt.description', 'ptt.description')
                ->orderBy('total_value', 'DESC')
                ->get();

            return response()->json($this->formatForTable($data));

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Unable to fetch buying payment terms.',
                'details' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Selling Payment Terms with Payment Type & Terms Descriptions
     */
    public function sellingPaymentTerms($contactId)
    {
        try {
            $data = DB::table('contracts as c')
                ->join('deal as d', 'd.id', '=', 'c.sale_id')
                ->leftJoin('deal_products as d_rel', 'd_rel.deal_id', '=', 'd.id')
                ->leftJoin('deal_product as dp', function ($join) {
                    $join->on('dp.id', '=', 'd_rel.products_id')
                         ->orOn('dp.associated_contract_id', '=', 'c.id')
                         ->orOn('dp.contract_order_code', '=', 'c.order_code');
                })
                ->leftJoin('payment_type as pt', 'pt.id', '=', 'd.payment_type_id')
                ->leftJoin('payment_terms_type as ptt', 'ptt.id', '=', 'd.payment_terms_type_id')
                ->select(
                    'pt.description as payment_type',
                    'ptt.description as payment_terms',
                    DB::raw('COUNT(DISTINCT c.id) as total_contracts'),
                    DB::raw('COALESCE(SUM(dp.quantity), 0) as total_quantity'),
                    DB::raw('COALESCE(SUM(dp.total_price), 0) as total_value')
                )
                ->where('d.contact_id', $contactId)
                ->groupBy('pt.description', 'ptt.description')
                ->orderByDesc('total_value')
                ->get();

            return response()->json($this->formatForTable($data));

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to fetch selling payment terms',
                'details' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Product Buying Country with Country Name & Product Name
     */
    public function productBuyingCountry($contactId)
    {
        try {
            $data = DB::table('contracts as c')
                ->join('deal as d', 'd.id', '=', 'c.purchase_id')
                ->leftJoin('deal_products as d_rel', 'd_rel.deal_id', '=', 'd.id')
                ->leftJoin('deal_product as dp', function ($join) {
                    $join->on('dp.id', '=', 'd_rel.products_id')
                         ->orOn('dp.associated_contract_id', '=', 'c.id')
                         ->orOn('dp.contract_order_code', '=', 'c.order_code');
                })
                ->leftJoin('products as p', 'p.id', '=', 'dp.product_id')
                ->leftJoin('contacts as ct', 'ct.id', '=', 'd.contact_id')
                ->leftJoin('countries as country', 'country.id', '=', 'ct.country_id')
                ->select(
                    'country.name as country',
                    'p.name as product_name',
                    DB::raw('COUNT(DISTINCT c.id) as total_contracts'),
                    DB::raw('COALESCE(SUM(dp.quantity), 0) as total_quantity'),
                    DB::raw('COALESCE(SUM(dp.total_price), 0) as total_value')
                )
                ->where('d.contact_id', $contactId)
                ->whereNotNull('country.name')
                ->whereNotNull('p.name')
                ->groupBy('country.name', 'p.name')
                ->orderByDesc('total_quantity')
                ->get();

            return response()->json($this->formatForTable($data));

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to fetch product buying by country',
                'details' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Product Selling Country with Country Name, Meta Company Name & Product Name
     */
    public function productSellingCountry($contactId)
    {
        try {
            $data = DB::table('contracts as c')
                ->join('deal as d', 'd.id', '=', 'c.sale_id')
                ->leftJoin('deal_products as d_rel', 'd_rel.deal_id', '=', 'd.id')
                ->leftJoin('deal_product as dp', function ($join) {
                    $join->on('dp.id', '=', 'd_rel.products_id')
                         ->orOn('dp.associated_contract_id', '=', 'c.id')
                         ->orOn('dp.contract_order_code', '=', 'c.order_code');
                })
                ->leftJoin('products as p', 'p.id', '=', 'dp.product_id')
                ->leftJoin('contacts as ct', 'ct.id', '=', 'd.contact_id')
                ->leftJoin('countries as country', 'country.id', '=', 'ct.country_id')
                ->leftJoin('companies as cmp', 'cmp.id', '=', 'd.meta_company_id')
                ->select(
                    'country.name as country',
                    'cmp.name as meta_company',
                    'p.name as product_name',
                    DB::raw('COUNT(DISTINCT c.id) as total_contracts'),
                    DB::raw('COALESCE(SUM(dp.quantity), 0) as total_quantity'),
                    DB::raw('COALESCE(SUM(dp.total_price), 0) as total_value')
                )
                ->where('d.contact_id', $contactId)
                ->whereNotNull('country.name')
                ->whereNotNull('p.name')
                ->groupBy('country.name', 'cmp.name', 'p.name')
                ->orderByDesc('total_quantity')
                ->get();

            return response()->json($this->formatForTable($data));

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to fetch product selling by country',
                'details' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * All Countries (Clean master list)
     */
    public function countries()
    {
        try {
            $countries = DB::table('countries')->get();

            return response()->json($this->formatForTable($countries));

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to fetch countries',
                'details' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Credit/Debit Notes by Contact ID with Country, Company, Product & Currency Names resolved
     */
    public function creditDebitNotes($contactId)
    {
        try {
            $data = DB::table('deal as d')
                ->leftJoin('contracts as c', function ($join) {
                    $join->on('c.sale_id', '=', 'd.id')
                         ->orOn('c.purchase_id', '=', 'd.id');
                })
                ->leftJoin('contacts as ct', 'ct.id', '=', 'd.contact_id')
                ->leftJoin('countries as co', 'co.id', '=', 'ct.country_id')
                ->leftJoin('companies as cmp', 'cmp.id', '=', 'd.meta_company_id')
                ->where('d.contact_id', $contactId)
                ->where(function ($q) {
                    $q->whereNotNull('d.credit_note_text')
                      ->orWhereNotNull('d.debit_note_text')
                      ->orWhereNotNull('d.notes');
                })
                ->select(
                    'd.id as note_id',
                    DB::raw("COALESCE(c.order_code, CONCAT('DEAL-', d.id)) as note_number"),
                    DB::raw("CASE WHEN d.credit_note_text IS NOT NULL THEN 'Credit Note' WHEN d.debit_note_text IS NOT NULL THEN 'Debit Note' ELSE 'Contract Note' END as note_type"),
                    'd.payment_date as note_date',
                    'd.payment_status as status',
                    'c.order_code',
                    'ct.name as contact_name',
                    'ct.code_meta as contact_code',
                    'co.name as country',
                    'cmp.name as company_name',
                    DB::raw("COALESCE(d.credit_note_text, d.debit_note_text, d.notes) as note_description"),
                    DB::raw("COALESCE(NULLIF(ct.currency, ''), 'USD') as currency")
                )
                ->orderBy('d.id', 'DESC')
                ->get();

            return response()->json($this->formatForTable($data));

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to fetch Credit/Debit Notes',
                'details' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * All Credit/Debit Notes across all contacts with fully resolved relationships
     */
    public function allCreditDebitNotes(Request $request)
    {
        try {
            $query = DB::table('deal as d')
                ->leftJoin('contracts as c', function ($join) {
                    $join->on('c.sale_id', '=', 'd.id')
                         ->orOn('c.purchase_id', '=', 'd.id');
                })
                ->leftJoin('contacts as ct', 'ct.id', '=', 'd.contact_id')
                ->leftJoin('countries as co', 'co.id', '=', 'ct.country_id')
                ->leftJoin('companies as cmp', 'cmp.id', '=', 'd.meta_company_id')
                ->where(function ($q) {
                    $q->whereNotNull('d.credit_note_text')
                      ->orWhereNotNull('d.debit_note_text')
                      ->orWhereNotNull('d.notes');
                });

            if ($request->filled('contact_id')) {
                $query->where('d.contact_id', $request->input('contact_id'));
            }

            $data = $query->select(
                'd.id as note_id',
                DB::raw("COALESCE(c.order_code, CONCAT('DEAL-', d.id)) as note_number"),
                DB::raw("CASE WHEN d.credit_note_text IS NOT NULL THEN 'Credit Note' WHEN d.debit_note_text IS NOT NULL THEN 'Debit Note' ELSE 'Contract Note' END as note_type"),
                'd.payment_date as note_date',
                'd.payment_status as status',
                'c.order_code',
                'ct.name as contact_name',
                'ct.code_meta as contact_code',
                'co.name as country',
                'cmp.name as company_name',
                DB::raw("COALESCE(d.credit_note_text, d.debit_note_text, d.notes) as note_description"),
                DB::raw("COALESCE(NULLIF(ct.currency, ''), 'USD') as currency")
            )
            ->orderBy('d.id', 'DESC')
            ->get();

            return response()->json($this->formatForTable($data));

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to fetch all Credit/Debit Notes',
                'details' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Dashboard Summary for a Contact
     */
    public function dashboardSummary($contactId)
    {
        try {
            $buying = DB::table('contracts as c')
                ->join('deal as d', 'd.id', '=', 'c.purchase_id')
                ->leftJoin('deal_products as d_rel', 'd_rel.deal_id', '=', 'd.id')
                ->leftJoin('deal_product as dp', function ($join) {
                    $join->on('dp.id', '=', 'd_rel.products_id')
                         ->orOn('dp.associated_contract_id', '=', 'c.id')
                         ->orOn('dp.contract_order_code', '=', 'c.order_code');
                })
                ->where('d.contact_id', $contactId)
                ->selectRaw("
                    COUNT(DISTINCT c.id) as total_buy_contracts,
                    COALESCE(SUM(dp.quantity), 0) as total_buy_quantity,
                    COALESCE(SUM(dp.total_price), 0) as total_buy_value
                ")
                ->first();

            $selling = DB::table('contracts as c')
                ->join('deal as d', 'd.id', '=', 'c.sale_id')
                ->leftJoin('deal_products as d_rel', 'd_rel.deal_id', '=', 'd.id')
                ->leftJoin('deal_product as dp', function ($join) {
                    $join->on('dp.id', '=', 'd_rel.products_id')
                         ->orOn('dp.associated_contract_id', '=', 'c.id')
                         ->orOn('dp.contract_order_code', '=', 'c.order_code');
                })
                ->where('d.contact_id', $contactId)
                ->selectRaw("
                    COUNT(DISTINCT c.id) as total_sell_contracts,
                    COALESCE(SUM(dp.quantity), 0) as total_sell_quantity,
                    COALESCE(SUM(dp.total_price), 0) as total_sell_value
                ")
                ->first();

            $creditNotes = 0;
            $debitNotes = 0;
            if (Schema::hasTable('detached_note')) {
                $creditNotes = DB::table('detached_note')
                    ->where('contact_id', $contactId)
                    ->where('note_type', 'Credit')
                    ->count();

                $debitNotes = DB::table('detached_note')
                    ->where('contact_id', $contactId)
                    ->where('note_type', 'Debit')
                    ->count();
            }

            $topProduct = DB::table('deal as d')
                ->join('deal_products as d_rel', 'd_rel.deal_id', '=', 'd.id')
                ->join('deal_product as dp', 'dp.id', '=', 'd_rel.products_id')
                ->join('products as p', 'p.id', '=', 'dp.product_id')
                ->where('d.contact_id', $contactId)
                ->selectRaw("
                    p.id,
                    p.name as product_name,
                    SUM(dp.quantity) as total_quantity
                ")
                ->groupBy('p.id', 'p.name')
                ->orderByDesc('total_quantity')
                ->first();

            $contact = DB::table('contacts as ct')
                ->leftJoin('countries as co', 'co.id', '=', 'ct.country_id')
                ->where('ct.id', $contactId)
                ->select('co.name as country_name')
                ->first();

            $revenue = ($selling->total_sell_value ?? 0) - ($buying->total_buy_value ?? 0);

            $summaryData = [
                [
                    'buying_contracts' => (int) ($buying->total_buy_contracts ?? 0),
                    'buying_quantity' => (double) ($buying->total_buy_quantity ?? 0),
                    'buying_value' => (double) ($buying->total_buy_value ?? 0),
                    'selling_contracts' => (int) ($selling->total_sell_contracts ?? 0),
                    'selling_quantity' => (double) ($selling->total_sell_quantity ?? 0),
                    'selling_value' => (double) ($selling->total_sell_value ?? 0),
                    'credit_notes' => $creditNotes,
                    'debit_notes' => $debitNotes,
                    'revenue' => $revenue,
                    'top_product' => $topProduct->product_name ?? '',
                    'country' => $contact->country_name ?? ''
                ]
            ];

            return response()->json($summaryData);

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to fetch dashboard summary',
                'details' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Combined Dashboard for a Contact
     */
    public function dashboard($contactId)
    {
        try {
            $contact = DB::table('contacts as c')
                ->leftJoin('countries as co', 'co.id', '=', 'c.country_id')
                ->leftJoin('companies as cp', 'cp.id', '=', 'c.company_id')
                ->select(
                    'c.id as contact_id',
                    'c.code_meta as contact_code',
                    'c.name as contact_name',
                    'co.name as country',
                    'cp.name as company_name',
                    'c.registration',
                    'c.vat',
                    'c.currency',
                    'c.website'
                )
                ->where('c.id', $contactId)
                ->first();

            $dashboardData = [
                [
                    'contact_information' => $contact,
                    'purchases_endpoint' => url("/api/powerbi/contact/{$contactId}/purchases"),
                    'sales_endpoint' => url("/api/powerbi/contact/{$contactId}/sales"),
                    'buying_terms_endpoint' => url("/api/powerbi/contact/{$contactId}/buying-payment-terms"),
                    'selling_terms_endpoint' => url("/api/powerbi/contact/{$contactId}/selling-payment-terms"),
                    'product_buying_country_endpoint' => url("/api/powerbi/contact/{$contactId}/product-buying-country"),
                    'product_selling_country_endpoint' => url("/api/powerbi/contact/{$contactId}/product-selling-country"),
                    'credit_debit_notes_endpoint' => url("/api/powerbi/contact/{$contactId}/credit-debit-notes"),
                    'summary_endpoint' => url("/api/powerbi/contact/{$contactId}/dashboard-summary")
                ]
            ];

            return response()->json($dashboardData);

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Dashboard Error',
                'details' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * All Products
     */
    public function products()
    {
        try {
            $products = DB::table('products')->get();

            return response()->json($this->formatForTable($products));

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to fetch products',
                'details' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * All Companies
     */
    public function companies()
    {
        try {
            $companies = DB::table('companies')->get();

            return response()->json($this->formatForTable($companies));

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to fetch companies',
                'details' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Comprehensive Sales API: Fetches all sales contracts with real product line details from deal_product
     */
    public function allSales(Request $request)
    {
        try {
            $query = DB::table('contracts as c')
                ->whereNotNull('c.sale_id')
                ->join('deal as d', 'd.id', '=', 'c.sale_id')
                ->leftJoin('deal_products as d_rel', 'd_rel.deal_id', '=', 'd.id')
                ->leftJoin('deal_product as dp', function ($join) {
                    $join->on('dp.id', '=', 'd_rel.products_id')
                         ->orOn('dp.associated_contract_id', '=', 'c.id')
                         ->orOn('dp.contract_order_code', '=', 'c.order_code');
                })
                ->leftJoin('products as p', 'p.id', '=', 'dp.product_id')
                ->leftJoin('contacts as ct', 'ct.id', '=', 'd.contact_id')
                ->leftJoin('countries as co', 'co.id', '=', 'ct.country_id')
                ->leftJoin('companies as client_cmp', 'client_cmp.id', '=', 'ct.company_id')
                ->leftJoin('companies as cmp', 'cmp.id', '=', 'd.meta_company_id')
                ->leftJoin('payment_type as pt', 'pt.id', '=', 'd.payment_type_id')
                ->leftJoin('payment_terms_type as ptt', 'ptt.id', '=', 'd.payment_terms_type_id')
                ->select(
                    'c.id as contract_id',
                    'c.order_code',
                    'c.sales_invoice_number',
                    'ct.name as customer_name',
                    'ct.code_meta as customer_code',
                    'co.name as country',
                    'client_cmp.name as customer_company',
                    'cmp.name as meta_company',
                    'p.name as product_name',
                    'dp.quantity',
                    'dp.premium',
                    'dp.rate',
                    'dp.total_price',
                    DB::raw("COALESCE(NULLIF(ct.currency, ''), 'USD') as currency"),
                    'pt.description as payment_type',
                    'ptt.description as payment_terms',
                    'dp.start_date',
                    'dp.end_date',
                    'ct.registration as customer_registration',
                    'ct.vat as customer_vat',
                    'ct.website as customer_website'
                );

            if ($request->filled('contact_id')) {
                $query->where('d.contact_id', $request->input('contact_id'));
            }

            $data = $query->orderBy('c.id', 'DESC')->get();

            return response()->json($this->formatForTable($data));

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to fetch sales report',
                'details' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Comprehensive Purchases API: Fetches all purchase contracts with real product line details from deal_product
     */
    public function allPurchases(Request $request)
    {
        try {
            $query = DB::table('contracts as c')
                ->whereNotNull('c.purchase_id')
                ->join('deal as d', 'd.id', '=', 'c.purchase_id')
                ->leftJoin('deal_products as d_rel', 'd_rel.deal_id', '=', 'd.id')
                ->leftJoin('deal_product as dp', function ($join) {
                    $join->on('dp.id', '=', 'd_rel.products_id')
                         ->orOn('dp.associated_contract_id', '=', 'c.id')
                         ->orOn('dp.contract_order_code', '=', 'c.order_code');
                })
                ->leftJoin('products as p', 'p.id', '=', 'dp.product_id')
                ->leftJoin('contacts as ct', 'ct.id', '=', 'd.contact_id')
                ->leftJoin('countries as co', 'co.id', '=', 'ct.country_id')
                ->leftJoin('companies as supplier_cmp', 'supplier_cmp.id', '=', 'ct.company_id')
                ->leftJoin('companies as cmp', 'cmp.id', '=', 'd.meta_company_id')
                ->leftJoin('payment_type as pt', 'pt.id', '=', 'd.payment_type_id')
                ->leftJoin('payment_terms_type as ptt', 'ptt.id', '=', 'd.payment_terms_type_id')
                ->select(
                    'c.id as contract_id',
                    'c.order_code',
                    'c.sales_invoice_number',
                    'ct.name as supplier_name',
                    'ct.code_meta as supplier_code',
                    'co.name as country',
                    'supplier_cmp.name as supplier_company',
                    'cmp.name as meta_company',
                    'p.name as product_name',
                    'dp.quantity',
                    'dp.premium',
                    'dp.rate',
                    'dp.total_price',
                    DB::raw("COALESCE(NULLIF(ct.currency, ''), 'USD') as currency"),
                    'pt.description as payment_type',
                    'ptt.description as payment_terms',
                    'dp.start_date',
                    'dp.end_date',
                    'ct.registration as supplier_registration',
                    'ct.vat as supplier_vat',
                    'ct.website as supplier_website'
                );

            if ($request->filled('contact_id')) {
                $query->where('d.contact_id', $request->input('contact_id'));
            }

            $data = $query->orderBy('c.id', 'DESC')->get();

            return response()->json($this->formatForTable($data));

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to fetch purchases report',
                'details' => $e->getMessage()
            ], 500);
        }
    }
}