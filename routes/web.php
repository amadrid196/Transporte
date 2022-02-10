<?php

use App\Http\Controllers\BrokerController;
use App\Http\Controllers\CustomersController;
use App\Http\Controllers\DriverController;
use App\Http\Controllers\GoogleApiController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\LoadController;
use App\Http\Controllers\LoadControllerB;
use App\Http\Controllers\TractorController;
use App\Http\Controllers\TrailerController;
use App\Http\Controllers\SMSController;
use App\Http\Controllers\ListSettingController;
use App\Http\Controllers\SmsTemplateController;
use App\Http\Controllers\SearchLoadController;
use App\Http\Controllers\PaymanagementController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\WebhookController;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

//clear_all
Route::group(['middleware' => ["lang"]], function () {
    Route::get('/clear_all', function () {
        $exitCode = Artisan::call('route:clear');
        $exitCode = Artisan::call('config:clear');
        $exitCode = Artisan::call('cache:clear');
        $exitCode = Artisan::call('view:clear');
        return 'All cache cleared';
    });

    //    Route::get('/route-cache', function () {
    //        $exitCode = Artisan::call('route:clear');
    //        return 'Routes cache cleared';
    //    });
    //
    //    //Clear config cache:
    //    Route::get('/config-cache', function () {
    //        $exitCode = Artisan::call('config:clear');
    //        return 'Config cache cleared';
    //    });
    //
    //    // Clear application cache:
    //    Route::get('/clear-cache', function () {
    //        $exitCode = Artisan::call('cache:clear');
    //        return 'Application cache cleared';
    //    });
    //
    //    // Clear view cache:
    //    Route::get('/view-clear', function () {
    //        $exitCode = Artisan::call('view:clear');
    //        return 'View cache cleared';
    //    });

});

Route::group(['middleware' => ["lang"]], function () {
    Route::get('/', function () {
        if (\auth()->user()) {
            return redirect()->route("home");
        }
        return view('auth.custom_login');
    })->name("app_url");
});


Auth::routes([
    'logout' => false,
    "register" => false]);

Route::get("logout", function () {
    auth()->logout();
    return redirect()->route("login");
})->name("logout");

Route::get('/', function () {
    if (\auth()->user()) {
        return redirect()->route("home");
    }
    return view('auth.custom_login');
})->name("app_url");

Route::group(['middleware' => ["auth", "lang", "admin"]], function () {
    Route::get('/dashboard/load_summary', [DashboardController::class, 'loadSummary'])->name("load-summary");
    Route::get('/dashboard/daily_gross_chart', [DashboardController::class, 'daliyGrossChart'])->name("daily-gross-chart");
    Route::get('/dashboard/pick_up_cities', [DashboardController::class, 'pickUpCities'])->name("pick-up-cities");
    Route::get('/dashboard/webhook_list', [WebhookController::class, "webHookList"])->name("web-hook-list");
    Route::get('/home', [HomeController::class, 'index'])->name('home');

    Route::get('/profile', [HomeController::class, 'profile'])->name('profile');

    Route::post('/profile_update', [HomeController::class, 'profile_update'])->name('profile_update');
    
    Route::post('/send_document_email', [HomeController::class, 'send_document_email'])->name('send_document_email');
    
    Route::get('/remove_document/{id}/{query}', [HomeController::class, 'remove_document'])->name('remove_document');
    //customers
    Route::get('/customers-edit/{id}', [CustomersController::class, 'edit'])->name('customers-edit');

    Route::get('/customers-set-contact-address-ajax/{id}', [CustomersController::class, 'set_contact_address_ajax'])->name('customers-set-contact-address-ajax');

    Route::post('/customers-update/{id}', [CustomersController::class, 'update'])->name('customers-update');

    Route::get('/customers-create', [CustomersController::class, 'create'])->name('customers-create');

    Route::post('/customers-store', [CustomersController::class, 'store'])->name('customers-store');

    Route::get('/customers-ajax', [CustomersController::class, 'customers_ajax'])->name('customers-ajax');

    Route::get('/customers-index', [CustomersController::class, 'index'])->name('customers-index');

    Route::get('/customers-delete/{id}', [CustomersController::class, 'softDelete'])->name('customers-delete');
    //drivers
    Route::get('/drivers-edit/{id}', [DriverController::class, 'edit'])->name('drivers-edit');

    Route::post('/drivers-update/{id}', [DriverController::class, 'update'])->name('drivers-update');

    Route::get('/drivers-create', [DriverController::class, 'create'])->name('drivers-create');

    Route::post('/drivers-store', [DriverController::class, 'store'])->name('drivers-store');

    Route::get('/drivers-ajax', [DriverController::class, 'drivers_ajax'])->name('drivers-ajax');

    Route::get('/drivers-index', [DriverController::class, 'index'])->name('drivers-index');

    Route::get('/drivers-rate/{id}', [DriverController::class, 'rate'])->name('drivers-rate');

    Route::get('/drivers-delete/{id}', [DriverController::class, 'softDelete'])->name('drivers-delete');

    Route::get('/drivers-paymanagement/', [DriverController::class, 'paymanagement_index'])->name('drivers-paymanagement-index');

    Route::get('/drivers-paymanagement-ajax', [DriverController::class, 'paymanagement_index_ajax'])->name('drivers-paymanagement-index-ajax');   
    Route::get('/drivers-paymanagement-ajax/{category}', [DriverController::class, 'paymanagement_index_ajax']);

    Route::get('/drivers-paymanagement-date-range-ajax/', [DriverController::class, 'dateRangePayAjax'])->name('drivers_paymanagement_date_range_ajax');

    Route::post('/drivers/storenote/{id}', [DriverController::class, 'storeNotes']);
    Route::get('/drivers/editnote/{id}', [DriverController::class, 'editNotes']);
    Route::get('/drivers/deletenote/{id}', [DriverController::class, 'deleteNotes']);
    //cron job for notification
    Route::get('/license-medical-card-expiration-notification', [DriverController::class, 'license_medical_card_expiration_notification']);
    //tractor
    Route::get('/tractors-edit/{id}', [TractorController::class, 'edit'])->name('tractors-edit');

    Route::post('/tractors-update/{id}', [TractorController::class, 'update'])->name('tractors-update');

    Route::get('/tractors-create', [TractorController::class, 'create'])->name('tractors-create');

    Route::post('/tractors-store', [TractorController::class, 'store'])->name('tractors-store');

    Route::get('/tractors-ajax', [TractorController::class, 'tractors_ajax'])->name('tractors-ajax');

    Route::get('/tractors-index', [TractorController::class, 'index'])->name('tractors-index');

    Route::get('/tractors-delete/{id}', [TractorController::class, 'softDelete'])->name('tractors-delete');
    //trailer
    Route::get('/trailers-edit/{id}', [TrailerController::class, 'edit'])->name('trailers-edit');

    Route::post('/trailers-update/{id}', [TrailerController::class, 'update'])->name('trailers-update');

    Route::get('/trailers-create', [TrailerController::class, 'create'])->name('trailers-create');

    Route::post('/trailers-store', [TrailerController::class, 'store'])->name('trailers-store');

    Route::get('/trailers-ajax', [TrailerController::class, 'trailers_ajax'])->name('trailers-ajax');

    Route::get('/trailers-index', [TrailerController::class, 'index'])->name('trailers-index');

    Route::get('/trailers-delete/{id}', [TrailerController::class, 'softDelete'])->name('trailers-delete');
    //loads
    Route::get('/loads-edit/{id}', [LoadController::class, 'edit'])->name('loads-edit');

    Route::get('/payment-status/', [LoadController::class, 'swap_payment_status'])->name('loads-swap-payment-status');

    Route::post('/loads-update/{id}', [LoadController::class, 'update'])->name('loads-update');

    Route::get('/loads-create', [LoadController::class, 'create'])->name('loads-create');

    Route::post('/loads-store', [LoadController::class, 'store'])->name('loads-store');

    Route::post('/loads-deductions', [LoadController::class, 'add_deductions'])->name('loads-deduction');

    Route::get('/loads-deductions', [LoadController::class, 'add_deductions'])->name('loads-deduction');

    Route::get('/loads-deduction-single/{load_id}', [LoadController::class, 'add_deduction_single_load'])->name('loads-deduction-single');

    Route::post('/loads-deduction-process', [LoadController::class, 'deduction_process'])->name('loads-deduction-process');

    Route::get('/loads-ajax', [LoadController::class, 'loads_ajax'])->name('loads-ajax');
    Route::get('/loads-ajax/{categoryId}', [LoadController::class, 'loads_ajax']);

    Route::get('/loads-index', [LoadController::class, 'index'])->name('loads-index');

    Route::get('/loads-delete', [LoadController::class, 'softDelete'])->name('loads-delete');

    Route::post('/loads-change-status-select', [LoadController::class, 'change_status_multi_view'])->name('loads-change-status-select');

    Route::post('/loads-change-status-process', [LoadController::class, 'change_status_multi_process'])->name('loads-change-status-process');
   
    Route::get('/change_status_complete', [LoadController::class,'change_status_complete'])->name('change_status_complete');

    Route::get('/change_status_paid', [LoadController::class,'change_status_paid'])->name('change_status_paid');
    # ___ Search Load Controller ___
    Route::get('/search-load', [SearchLoadController::class, 'index'])->name('search-load-index');
    
    Route::get('/search-load/{id}', [SearchLoadController::class, 'index']);
    
    Route::post('/search-load-ajax', [SearchLoadController::class, 'searchLoadAjax'])->name('search-load-ajax');

    Route::post('/search-load-store', [SearchLoadController::class, 'store'])->name('search-load-store');
    
    Route::get('/search-load-delete/{id}', [SearchLoadController::class, 'delete'])->name('search-load-delete');

    Route::post('/search-load-export', [SearchLoadController::class, 'export'])->name('search-load-export');
    
    Route::post('/search-load-export-summary', [SearchLoadController::class, 'exportSummary'])->name('search-load-export-summary');
    //    options
    Route::get('options/{model}/{selected?}', [HomeController::class, 'options'])->name('options');
    //    invoice
    Route::get('driver/send/invoice/{id}', [DriverController::class, 'send_invoice'])->name('driver-send-invoice');

    Route::post('driver/send/invoices_multiple/', [DriverController::class, 'send_invoice_multiple'])->name('driver-send-invoice-multiple');

    Route::get('driver/send/invoices_multiple/', [DriverController::class, 'send_invoice_multiple'])->name('driver-send-invoice-multiple');

    Route::post('driver/invoice/process', [DriverController::class, 'invoice_process'])->name('driver-invoice-process');

    Route::get('home/load/invoice/{id}', [HomeController::Class, 'load_invoice']);
    Route::get('driver/driverowner/{id}', [DriverController::class, 'driverOwner'])->name('driver-owner');
    Route::get('send_mail', [HomeController::class, 'send_mail'])->name('send_mail');

    //Route::get('pdf/{id}', [HomeController::class, 'pdf'])->name('pdf');

    Route::get('html/{id}', [HomeController::class, 'html'])->name('html');
    
    Route::get('pdf/show/{id}', [HomeController::class, 'showInvoice'])->name('pdf_show');
    Route::post('pdf/multishow', [HomeController::class, 'showInvoiceMultiple'])->name('multi_pdf_show');

    Route::get('pdf/multishow', [HomeController::class, 'showInvoiceMultiple'])->name('multi_pdf_show');
    //stats
    Route::get("stats", [HomeController::class, "stats_index"])->name("stats-index");

    Route::post("stats/process", [HomeController::class, "stats_process"])->name("stats-process");

    Route::post("loads/calculateDistance", [GoogleApiController::class, "calculateDistance"])->name("loads-calculateDistance");

    Route::post("googleApi/distance_between_multiple_points", [GoogleApiController::class, "distance_between_multiple_points"])->name("distance-between-multiple-points");

    Route::post("googleApi/road_data", [GoogleApiController::class, "roadData"])->name("road_data");
    //    brokers
    Route::get('/brokers-edit/{id}', [BrokerController::class, 'edit'])->name('brokers-edit');

    Route::post('/brokers-update/{id}', [BrokerController::class, 'update'])->name('brokers-update');

    Route::get('/brokers-create', [BrokerController::class, 'create'])->name('brokers-create');

    Route::post('/brokers-store', [BrokerController::class, 'store'])->name('brokers-store');

    Route::get('/brokers-ajax', [BrokerController::class, 'brokers_ajax'])->name('brokers-ajax');

    Route::get('/brokers-index', [BrokerController::class, 'index'])->name('brokers-index');

    Route::get('/brokers-delete/{id}', [BrokerController::class, 'softDelete'])->name('brokers-delete');

    Route::get("test", [HomeController::class, "test"])->name("test");

    Route::get('SMS/sendsms', [SMSController::class, "sendSms"])->name("send-sms");


    //List Settings
    Route::get('/listsettings-edit/{id}', [ListSettingController::class, 'edit'])->name('listsettings-edit');

    Route::post('/listsettings-update/{id}', [ListSettingController::class, 'update'])->name('listsettings-update');

    Route::get('/listsettings-create', [ListSettingController::class, 'create'])->name('listsettings-create');

    Route::post('/listsettings-store', [ListSettingController::class, 'store'])->name('listsettings-store');

    Route::get('/listsettings-ajax', [ListSettingController::class, 'listsettings_ajax'])->name('listsettings-ajax');

    Route::get('/listsettings-index', [ListSettingController::class, 'index'])->name('listsettings-index');

    Route::get('/listsettings-delete/{id}', [ListSettingController::class, 'softDelete'])->name('listsettings-delete');

    Route::get('/smstemplate/{id}', [SmsTemplateController::class, 'edit'])->name('smstemplate-edit');

    Route::post('/smstemplate/store', [SmsTemplateController::class, 'store'])->name('smstemplate-store');

    Route::get('/sendbulksms', [SMSController::class, 'sendBulkSms'])->name('sendbulksms');

    Route::get('testload', [LoadControllerB::class, 'index']);
    Route::get('testload-ajax/{category}', [LoadControllerB::class, 'loads_ajax']);

    Route::get('testpayment', [PaymanagementController::class, 'index']);
   
    Route::get('testpayment-daterange-ajax/{category}', [PaymanagementController::class, 'dateRangePayAjax']);

    Route::get("setting/factoragentsetting", [SettingsController::class, 'factorAgentSetting'])->name("factor-agent-setting");
    Route::post("setting/factoragentsetting/store", [SettingsController::class, 'factorAgentSetting_store'])->name("factor-agent-setting-store");
});
Route::get('testemail/{id}', [SMSController::class, "sendExpirationEmail"])->name("test-email");
Route::get("display_hook", [WebhookController::class, 'getWebhookData']);
#ccf