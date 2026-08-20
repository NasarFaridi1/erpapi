<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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

    public function contracts()
    {
        try {
            $contracts = DB::table('contracts')->get();

            return response()->json($this->formatForTable($contracts), 200);

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to fetch contracts',
                'details' => $e->getMessage()
            ], 500);
        }
    }

    public function contacts()
    {
        try {
            $contacts = DB::table('contacts')->get();

            return response()->json($this->formatForTable($contacts));

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to fetch contacts',
                'details' => $e->getMessage()
            ], 500);
        }
    }

    public function contactInformation($contactId)
    {
        try {
            $contact = DB::table('contacts as c')
                ->leftJoin('countries as co', 'co.id', '=', 'c.country_id')
                ->leftJoin('companies as cp', 'cp.id', '=', 'c.company_id')
                ->select(
                    'c.id as contact_id',
                    'c.name as contact_name',
                    'c.code_meta',
                    'co.name as country',
                    'cp.name as company_name',
                    'c.registration',
                    'c.vat',
                    'c.currency',
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

    public function purchases($contactId)
    {
        try {
            $data = DB::table('contracts as c')
                ->join('buyercontracts as bc', 'bc.contract_id', '=', 'c.id')
                ->join('productcontracts as pc', 'pc.buyercontract_id', '=', 'bc.id')
                ->join('products as p', 'p.id', '=', 'pc.product_id')
                ->join('deal as d', 'd.id', '=', 'c.purchase_id')
                ->join('companies as cmp', 'cmp.id', '=', 'd.meta_company_id')
                ->join('contacts as ct', 'ct.id', '=', 'bc.contact_id')
                ->leftJoin('payment_type as pt', 'pt.id', '=', 'd.payment_type_id')
                ->leftJoin('payment_terms_type as ptt', 'ptt.id', '=', 'd.payment_terms_type_id')
                ->select(
                    'c.id',
                    'c.order_code',
                    'c.sales_invoice_number',
                    'ct.id as contact_id',
                    'ct.name as contact_name',
                    'cmp.name as meta_company',
                    'p.name as product',
                    'pc.quantity',
                    'pc.premium',
                    'pc.rate',
                    'pc.total_price',
                    'pt.description as payment_type',
                    'ptt.description as payment_terms',
                    'pc.start_date',
                    'pc.end_date'
                )
                ->where('d.contact_id', $contactId)
                ->get();

            return response()->json($this->formatForTable($data));

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Error',
                'details' => $e->getMessage()
            ], 500);
        }
    }

    public function sales($contactId)
    {
        try {
            $data = DB::table('contracts as c')
                ->join('sellercontracts as sc', 'sc.contract_id', '=', 'c.id')
                ->join('productcontracts as pc', 'pc.sellercontract_id', '=', 'sc.id')
                ->join('products as p', 'p.id', '=', 'pc.product_id')
                ->join('deal as d', 'd.id', '=', 'c.sale_id')
                ->join('companies as cmp', 'cmp.id', '=', 'd.meta_company_id')
                ->join('contacts as ct', 'ct.id', '=', 'sc.contact_id')
                ->leftJoin('payment_type as pt', 'pt.id', '=', 'd.payment_type_id')
                ->leftJoin('payment_terms_type as ptt', 'ptt.id', '=', 'd.payment_terms_type_id')
                ->select(
                    'c.id',
                    'c.order_code',
                    'c.sales_invoice_number',
                    'ct.id as contact_id',
                    'ct.name as contact_name',
                    'cmp.name as meta_company',
                    'p.name as product',
                    'pc.quantity',
                    'pc.premium',
                    'pc.rate',
                    'pc.total_price',
                    'pt.description as payment_type',
                    'ptt.description as payment_terms',
                    'pc.start_date',
                    'pc.end_date'
                )
                ->where('d.contact_id', $contactId)
                ->get();

            return response()->json($this->formatForTable($data));

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Error',
                'details' => $e->getMessage()
            ], 500);
        }
    }

    public function buyingPaymentTerms($contactId)
    {
        try {
            $data = DB::table('contracts as c')
                ->join('deal as d', 'd.id', '=', 'c.purchase_id')
                ->leftJoin('payment_type as pt', 'pt.id', '=', 'd.payment_type_id')
                ->leftJoin('payment_terms_type as ptt', 'ptt.id', '=', 'd.payment_terms_type_id')
                ->join('buyercontracts as bc', 'bc.contract_id', '=', 'c.id')
                ->join('productcontracts as pc', 'pc.buyercontract_id', '=', 'bc.id')
                ->join('products as p', 'p.id', '=', 'pc.product_id')
                ->select(
                    'pt.description as payment_type',
                    'ptt.description as payment_terms',
                    DB::raw('COUNT(DISTINCT c.id) as total_contracts'),
                    DB::raw('SUM(pc.quantity) as total_quantity'),
                    DB::raw('SUM(pc.total_price) as total_value')
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

    public function sellingPaymentTerms($contactId)
    {
        try {
            $data = DB::table('contracts as c')
                ->join('deal as d', 'd.id', '=', 'c.sale_id')
                ->leftJoin('payment_type as pt', 'pt.id', '=', 'd.payment_type_id')
                ->leftJoin('payment_terms_type as ptt', 'ptt.id', '=', 'd.payment_terms_type_id')
                ->join('sellercontracts as sc', 'sc.contract_id', '=', 'c.id')
                ->join('productcontracts as pc', 'pc.sellercontract_id', '=', 'sc.id')
                ->join('products as p', 'p.id', '=', 'pc.product_id')
                ->select(
                    'pt.description as payment_type',
                    'ptt.description as payment_terms',
                    DB::raw('COUNT(DISTINCT c.id) as total_contracts'),
                    DB::raw('SUM(pc.quantity) as total_quantity'),
                    DB::raw('SUM(pc.total_price) as total_value')
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

    public function productBuyingCountry($contactId)
    {
        try {
            $data = DB::table('contracts as c')
                ->join('deal as d', 'd.id', '=', 'c.purchase_id')
                ->join('buyercontracts as bc', 'bc.contract_id', '=', 'c.id')
                ->join('productcontracts as pc', 'pc.buyercontract_id', '=', 'bc.id')
                ->join('products as p', 'p.id', '=', 'pc.product_id')
                ->join('contacts as ct', 'ct.id', '=', 'd.contact_id')
                ->leftJoin('countries as country', 'country.id', '=', 'ct.country_id')
                ->select(
                    'country.name as country',
                    'p.id as product_id',
                    'p.name as product',
                    DB::raw('COUNT(DISTINCT c.id) as total_contracts'),
                    DB::raw('SUM(pc.quantity) as total_quantity'),
                    DB::raw('SUM(pc.total_price) as total_value')
                )
                ->where('d.contact_id', $contactId)
                ->groupBy('country.name', 'p.id', 'p.name')
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

    public function productSellingCountry($contactId)
    {
        try {
            $data = DB::table('contracts as c')
                ->join('deal as d', 'd.id', '=', 'c.sale_id')
                ->join('sellercontracts as sc', 'sc.contract_id', '=', 'c.id')
                ->join('productcontracts as pc', 'pc.sellercontract_id', '=', 'sc.id')
                ->join('products as p', 'p.id', '=', 'pc.product_id')
                ->join('contacts as ct', 'ct.id', '=', 'd.contact_id')
                ->leftJoin('countries as country', 'country.id', '=', 'ct.country_id')
                ->leftJoin('companies as cmp', 'cmp.id', '=', 'd.meta_company_id')
                ->select(
                    'country.name as country',
                    'cmp.name as meta_company',
                    'p.id as product_id',
                    'p.name as product',
                    DB::raw('COUNT(DISTINCT c.id) as total_contracts'),
                    DB::raw('SUM(pc.quantity) as total_quantity'),
                    DB::raw('SUM(pc.total_price) as total_value')
                )
                ->where('d.contact_id', $contactId)
                ->groupBy('country.name', 'cmp.name', 'p.id', 'p.name')
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

    public function creditDebitNotes($contactId)
    {
        try {
            $data = DB::table('detached_note as dn')
                ->join('detached_note_detail as dnd', 'dnd.detached_note_id', '=', 'dn.id')
                ->leftJoin('contracts as c', 'c.id', '=', 'dn.contract_id')
                ->leftJoin('contacts as ct', 'ct.id', '=', 'dn.contact_id')
                ->leftJoin('companies as cmp', 'cmp.id', '=', 'ct.company_id')
                ->leftJoin('products as p', 'p.id', '=', 'dnd.product_id')
                ->leftJoin('currency as cur', 'cur.id', '=', 'dn.currency_id')
                ->select(
                    'dn.id',
                    'dn.note_number',
                    'dn.note_type',
                    'dn.note_date',
                    'dn.status',
                    'c.order_code',
                    'ct.id as contact_id',
                    'ct.name as contact_name',
                    'cmp.name as company_name',
                    'p.name as product',
                    'dnd.quantity',
                    'dnd.rate',
                    'dnd.amount',
                    'cur.code as currency'
                )
                ->where('ct.id', $contactId)
                ->orderBy('dn.note_date', 'DESC')
                ->get();

            return response()->json($this->formatForTable($data));

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to fetch Credit/Debit Notes',
                'details' => $e->getMessage()
            ], 500);
        }
    }

    public function dashboardSummary($contactId)
    {
        try {
            $buying = DB::table('contracts as c')
                ->join('deal as d', 'd.id', '=', 'c.purchase_id')
                ->leftJoin('buyercontracts as bc', 'bc.contract_id', '=', 'c.id')
                ->leftJoin('productcontracts as pc', 'pc.buyercontract_id', '=', 'bc.id')
                ->where('d.contact_id', $contactId)
                ->selectRaw("
                    COUNT(DISTINCT c.id) as total_buy_contracts,
                    COALESCE(SUM(pc.quantity),0) as total_buy_quantity,
                    COALESCE(SUM(pc.total_price),0) as total_buy_value
                ")
                ->first();

            $selling = DB::table('contracts as c')
                ->join('deal as d', 'd.id', '=', 'c.sale_id')
                ->leftJoin('sellercontracts as sc', 'sc.contract_id', '=', 'c.id')
                ->leftJoin('productcontracts as pc', 'pc.sellercontract_id', '=', 'sc.id')
                ->where('d.contact_id', $contactId)
                ->selectRaw("
                    COUNT(DISTINCT c.id) as total_sell_contracts,
                    COALESCE(SUM(pc.quantity),0) as total_sell_quantity,
                    COALESCE(SUM(pc.total_price),0) as total_sell_value
                ")
                ->first();

            $creditNotes = DB::table('detached_note')
                ->where('contact_id', $contactId)
                ->where('note_type', 'Credit')
                ->count();

            $debitNotes = DB::table('detached_note')
                ->where('contact_id', $contactId)
                ->where('note_type', 'Debit')
                ->count();

            $topProduct = DB::table('productcontracts as pc')
                ->join('products as p', 'p.id', '=', 'pc.product_id')
                ->selectRaw("
                    p.id,
                    p.name,
                    SUM(pc.quantity) as total_quantity
                ")
                ->groupBy('p.id', 'p.name')
                ->orderByDesc('total_quantity')
                ->first();

            $topCountry = DB::table('contacts as ct')
                ->join('countries as c', 'c.id', '=', 'ct.country_id')
                ->selectRaw("
                    c.name,
                    COUNT(*) as total_contacts
                ")
                ->groupBy('c.name')
                ->orderByDesc('total_contacts')
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
                    'top_product' => $topProduct->name ?? '',
                    'top_country' => $topCountry->name ?? ''
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

    private function contactInformationData($contactId)
    {
        return DB::table('contacts')
            ->leftJoin('companies', 'companies.id', '=', 'contacts.company_id')
            ->leftJoin('countries', 'countries.id', '=', 'contacts.country_id')
            ->select(
                'contacts.id as contact_id',
                'contacts.name',
                'contacts.code_meta',
                'companies.name as company',
                'countries.name as country'
            )
            ->where('contacts.id', $contactId)
            ->first();
    }

    public function dashboard($contactId)
    {
        try {
            $contactInformation = $this->contactInformationData($contactId);
            $dashboardSummary = $this->dashboardSummaryData($contactId);
            $purchases = $this->purchaseData($contactId);
            $sales = $this->salesData($contactId);
            $buyingPaymentTerms = $this->buyingPaymentTermsData($contactId);
            $sellingPaymentTerms = $this->sellingPaymentTermsData($contactId);
            $buyingCountry = $this->productBuyingCountryData($contactId);
            $sellingCountry = $this->productSellingCountryData($contactId);
            $creditDebit = $this->creditDebitData($contactId);

            $dashboardData = [
                [
                    'contact_information' => $contactInformation,
                    'dashboard_summary' => $dashboardSummary,
                    'purchasing_side' => $purchases,
                    'sales_side' => $sales,
                    'buying_payment_terms' => $buyingPaymentTerms,
                    'selling_payment_terms' => $sellingPaymentTerms,
                    'product_buying_country' => $buyingCountry,
                    'product_selling_country' => $sellingCountry,
                    'credit_debit_notes' => $creditDebit
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
     * Comprehensive Sales API: Fetches all sales contracts with all relationships resolved to readable names.
     */
    public function allSales(Request $request)
    {
        try {
            $query = DB::table('contracts as c')
                ->whereNotNull('c.sale_id')
                ->leftJoin('deal as d', 'd.id', '=', 'c.sale_id')
                ->leftJoin('sellercontracts as sc', 'sc.contract_id', '=', 'c.id')
                ->leftJoin('buyercontracts as bc', 'bc.contract_id', '=', 'c.id')
                ->leftJoin('productcontracts as pc', function ($join) {
                    $join->on('pc.sellercontract_id', '=', 'sc.id')
                         ->orOn('pc.buyercontract_id', '=', 'bc.id');
                })
                ->leftJoin('products as p', 'p.id', '=', 'pc.product_id')
                ->leftJoin('companies as cmp', 'cmp.id', '=', 'd.meta_company_id')
                ->leftJoin('contacts as ct', function ($join) {
                    $join->on('ct.id', '=', 'sc.contact_id')
                         ->orOn('ct.id', '=', 'bc.contact_id')
                         ->orOn('ct.id', '=', 'd.contact_id');
                })
                ->leftJoin('countries as co', 'co.id', '=', 'ct.country_id')
                ->leftJoin('companies as client_cmp', 'client_cmp.id', '=', 'ct.company_id')
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
                    'pc.quantity',
                    'pc.premium',
                    'pc.rate',
                    'pc.total_price',
                    DB::raw("COALESCE(NULLIF(ct.currency, ''), 'USD') as currency"),
                    'pt.description as payment_type',
                    'ptt.description as payment_terms',
                    'pc.start_date',
                    'pc.end_date',
                    'ct.registration as customer_registration',
                    'ct.vat as customer_vat',
                    'ct.website as customer_website'
                );

            if ($request->filled('contact_id')) {
                $query->where(function ($q) use ($request) {
                    $q->where('sc.contact_id', $request->input('contact_id'))
                      ->orWhere('bc.contact_id', $request->input('contact_id'))
                      ->orWhere('d.contact_id', $request->input('contact_id'));
                });
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
     * Comprehensive Purchases API: Fetches all purchase contracts with all relationships resolved to readable names.
     */
    public function allPurchases(Request $request)
    {
        try {
            $query = DB::table('contracts as c')
                ->whereNotNull('c.purchase_id')
                ->leftJoin('deal as d', 'd.id', '=', 'c.purchase_id')
                ->leftJoin('buyercontracts as bc', 'bc.contract_id', '=', 'c.id')
                ->leftJoin('sellercontracts as sc', 'sc.contract_id', '=', 'c.id')
                ->leftJoin('productcontracts as pc', function ($join) {
                    $join->on('pc.buyercontract_id', '=', 'bc.id')
                         ->orOn('pc.sellercontract_id', '=', 'sc.id');
                })
                ->leftJoin('products as p', 'p.id', '=', 'pc.product_id')
                ->leftJoin('companies as cmp', 'cmp.id', '=', 'd.meta_company_id')
                ->leftJoin('contacts as ct', function ($join) {
                    $join->on('ct.id', '=', 'bc.contact_id')
                         ->orOn('ct.id', '=', 'sc.contact_id')
                         ->orOn('ct.id', '=', 'd.contact_id');
                })
                ->leftJoin('countries as co', 'co.id', '=', 'ct.country_id')
                ->leftJoin('companies as supplier_cmp', 'supplier_cmp.id', '=', 'ct.company_id')
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
                    'pc.quantity',
                    'pc.premium',
                    'pc.rate',
                    'pc.total_price',
                    DB::raw("COALESCE(NULLIF(ct.currency, ''), 'USD') as currency"),
                    'pt.description as payment_type',
                    'ptt.description as payment_terms',
                    'pc.start_date',
                    'pc.end_date',
                    'ct.registration as supplier_registration',
                    'ct.vat as supplier_vat',
                    'ct.website as supplier_website'
                );

            if ($request->filled('contact_id')) {
                $query->where(function ($q) use ($request) {
                    $q->where('bc.contact_id', $request->input('contact_id'))
                      ->orWhere('sc.contact_id', $request->input('contact_id'))
                      ->orWhere('d.contact_id', $request->input('contact_id'));
                });
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