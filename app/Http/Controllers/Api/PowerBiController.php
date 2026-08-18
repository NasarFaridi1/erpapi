<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PowerBiController extends Controller
{
    /**
     * Helper to return response in raw format (for Power BI) or wrapped format (for legacy clients).
     */
    private function respond($data, string $message = 'Success')
    {
        if (request()->boolean('wrapped') || request()->boolean('legacy') || str_contains(request()->path(), 'legacy')) {
            return response()->json([
                'success' => true,
                'message' => $message,
                'data' => $data
            ]);
        }

        return response()->json($data);
    }

    public function contracts()
    {
        try {
            $contracts = DB::table('contracts')->get();

            return $this->respond($contracts, 'Contracts fetched successfully');

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch contracts',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function contacts()
    {
        try {
            $contacts = DB::table('contacts')->get();

            return $this->respond($contacts, 'Contacts fetched successfully');

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch contacts',
                'error' => $e->getMessage()
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
                return response()->json([
                    'success' => false,
                    'message' => 'Contact not found'
                ], 404);
            }

            return $this->respond($contact, 'Contact information fetched successfully');

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Something went wrong',
                'error' => $e->getMessage()
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

            return $this->respond($data, 'Purchasing Side');

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error',
                'error' => $e->getMessage()
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

            return $this->respond($data, 'Sales Side');

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error',
                'error' => $e->getMessage()
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

            return $this->respond($data, 'Buying contracts by payment terms fetched successfully');

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Unable to fetch buying payment terms.',
                'error' => $e->getMessage()
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

            return $this->respond($data, 'Selling contracts by payment terms fetched successfully');

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch selling payment terms',
                'error' => $e->getMessage()
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

            return $this->respond($data, 'Product buying by country fetched successfully');

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch product buying by country',
                'error' => $e->getMessage()
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

            return $this->respond($data, 'Product selling by country fetched successfully');

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch product selling by country',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function countries()
    {
        try {
            $countries = DB::table('countries')->get();

            return $this->respond($countries, 'Countries fetched successfully');

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch countries',
                'error' => $e->getMessage()
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

            return $this->respond($data, 'Credit/Debit Notes fetched successfully');

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch Credit/Debit Notes',
                'error' => $e->getMessage()
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
                'buying' => [
                    'contracts' => (int) ($buying->total_buy_contracts ?? 0),
                    'quantity' => (double) ($buying->total_buy_quantity ?? 0),
                    'value' => (double) ($buying->total_buy_value ?? 0),
                ],
                'selling' => [
                    'contracts' => (int) ($selling->total_sell_contracts ?? 0),
                    'quantity' => (double) ($selling->total_sell_quantity ?? 0),
                    'value' => (double) ($selling->total_sell_value ?? 0),
                ],
                'credit_notes' => $creditNotes,
                'debit_notes' => $debitNotes,
                'revenue' => $revenue,
                'top_product' => $topProduct,
                'top_country' => $topCountry
            ];

            return $this->respond($summaryData, 'Dashboard summary fetched successfully');

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch dashboard summary',
                'error' => $e->getMessage()
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
                'contact_information' => $contactInformation,
                'dashboard_summary' => $dashboardSummary,
                'purchasing_side' => $purchases,
                'sales_side' => $sales,
                'buying_payment_terms' => $buyingPaymentTerms,
                'selling_payment_terms' => $sellingPaymentTerms,
                'product_buying_country' => $buyingCountry,
                'product_selling_country' => $sellingCountry,
                'credit_debit_notes' => $creditDebit
            ];

            return $this->respond($dashboardData, 'Dashboard loaded successfully');

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Dashboard Error',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function products()
    {
        try {
            $products = DB::table('products')->get();

            return $this->respond($products, 'Products fetched successfully');

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch products',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function companies()
    {
        try {
            $companies = DB::table('companies')->get();

            return $this->respond($companies, 'Companies fetched successfully');

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch companies',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}