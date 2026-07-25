<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Artisan;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BankPaymentController;
use App\Http\Controllers\ChargeController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\ClientDataController;
use App\Http\Controllers\CostController;
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\IncomeController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ReservationController;
use App\Http\Controllers\SaleController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\TrainerController;
use App\Http\Controllers\WebController;
use App\Http\Controllers\PaymentMethodController;

Route::get('optimize', function(){
	Artisan::call('optimize:clear');
});

Route::get('login', [AuthController::class, 'login'])->name('auth.login');
Route::post('login', [AuthController::class, 'check'])->name('auth.check');
Route::post('logout', [AuthController::class, 'logout'])->name('auth.logout');

Route::middleware('auth')->group(function(){

	Route::get('/',[WebController::class, 'index']);
	Route::get('index_client',[WebController::class, 'index_client'])->name('index_client');

	Route::get('services/excel', [ServiceController::class, 'excel'])->name('services.excel');
	Route::resource('services', ServiceController::class);
	
	Route::resource('products', ProductController::class);

	Route::post('clients/login', [ClientController::class, 'login'])->name('clients.login');
	Route::get('clients/logout', [ClientController::class, 'logout'])->name('clients.logout');
	Route::get('clients/sessions/excel', [ClientController::class, 'sessionsExcel'])->name('clients.sessionsExcel');
	Route::get('clients/sessions', [ClientController::class, 'sessions'])->name('clients.sessions');
	Route::get('clients/api', [ClientController::class, 'api'])->name('clients.api');
	Route::post('clients/{client}/renew', [ClientController::class, 'renew'])->name('clients.renew');
	Route::get('clients/{client}/services', [ClientController::class, 'services'])->name('clients.services');
	Route::get('client_services/{client_service}/attendances', [ClientController::class, 'serviceAttendances'])->name('client_services.attendances');
	Route::get('clients/{client}/data', [ClientController::class, 'data'])->name('clients.data');
	Route::post('clients/{client}/data', [ClientController::class, 'storeData'])->name('clients.storeData');
	Route::delete('clients/{client}/data', [ClientController::class, 'destroyData'])->name('clients.destroyData');
	Route::get('clients/{client}/reset', [ClientController::class, 'reset'])->name('clients.reset');
	Route::get('clients/excel', [ClientController::class, 'excel'])->name('clients.excel');
	Route::resource('clients', ClientController::class);

	Route::get('client_data/excel', [ClientDataController::class, 'excel'])->name('client_data.excel');
	
	Route::resource('sales', SaleController::class);

	Route::get('trainers/api', [TrainerController::class, 'api'])->name('trainers.api');
	Route::resource('trainers', TrainerController::class);

	Route::get('costs/excel', [CostController::class, 'excel'])->name('costs.excel');
	Route::resource('costs', CostController::class);
	
	Route::resource('payment_methods', PaymentMethodController::class);
	
	Route::get('charges/excel', [ChargeController::class, 'excel'])->name('charges.excel');
	Route::resource('charges', ChargeController::class);
	
	Route::get('attendances/excel', [AttendanceController::class, 'excel'])->name('attendances.excel');
	Route::get('attendances/search', [AttendanceController::class, 'search'])->name('attendances.search');
	Route::resource('attendances', AttendanceController::class)->except(['show']);
	
	Route::get('reservations/excel', [ReservationController::class, 'excel'])->name('reservations.excel');
	Route::get('reservations/search', [ReservationController::class, 'search'])->name('reservations.search');
	Route::resource('reservations', ReservationController::class);
	
	Route::get('expenses/excel', [ExpenseController::class, 'excel'])->name('expenses.excel');
	Route::resource('expenses', ExpenseController::class);
	
	Route::get('incomes/excel', [IncomeController::class, 'excel'])->name('incomes.excel');
	Route::resource('incomes', IncomeController::class);
	
	Route::get('payments/excel', [PaymentController::class, 'excel'])->name('payments.excel');
	Route::resource('payments', PaymentController::class);
	
	Route::resource('bank-payments', BankPaymentController::class);

	Route::get('settings', [SettingController::class, 'index'])->name('settings.index');
	Route::post('settings', [SettingController::class, 'update'])->name('settings.update');

	Route::get('settings_client', [SettingController::class, 'index_client'])->name('settings_client.index');
	Route::post('settings_client', [SettingController::class, 'update_client'])->name('settings_client.update');

	Route::get('cash-flow', [WebController::class, 'cashFlow'])->name('cash-flow');

});

