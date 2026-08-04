<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\PowerBiController;

/*
|--------------------------------------------------------------------------
| Super Admin Reports APIs
|--------------------------------------------------------------------------
*/
Route::middleware(['auth:sanctum'])->group(function () {

});

Route::prefix('powerbi')
    // ->middleware('powerbi.key')
    ->group(function () {

        Route::get('/contacts', [PowerBiController::class, 'contacts']);

        Route::get('/contact/{id}', [PowerBiController::class, 'contactInformation']);

        Route::get('/contact/{id}/purchases', [PowerBiController::class, 'purchases']);

        Route::get('/contact/{id}/sales', [PowerBiController::class, 'sales']);

        Route::get('/countries', [PowerBiController::class, 'countries']);

        Route::get('/products', [PowerBiController::class, 'products']);

        Route::get('/companies', [PowerBiController::class, 'companies']);

        Route::get('/contracts', [PowerBiController::class, 'contracts']);

        Route::get('/contact/{id}/buying-payment-terms', [PowerBiController::class, 'buyingPaymentTerms']);

        Route::get('/contact/{id}/selling-payment-terms', [PowerBiController::class, 'sellingPaymentTerms']);
        Route::get('/contact/{id}/product-buying-country', [PowerBiController::class, 'productBuyingCountry']);

        Route::get('/contact/{id}/product-selling-country', [PowerBiController::class, 'productSellingCountry']);

        Route::get('/contact/{id}/credit-debit-notes', [PowerBiController::class, 'creditDebitNotes']);

        Route::get('/contact/{id}/dashboard-summary', [PowerBiController::class, 'dashboardSummary']);

        Route::get('/powerbi/contact/{id}/dashboard', [PowerBiController::class, 'dashboard']);

    });