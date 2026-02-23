<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ErrorController;
use App\Http\Controllers\PersonController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AjaxController;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\MicroServiceController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\SaleController;
use App\Http\Controllers\WhatsappController;

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



Route::get('login', function () {
    return redirect('admin/login');
})->name('login');

Route::get('/', function () {
    return redirect('admin');
});

Route::get('/info/{id?}', [ErrorController::class , 'error'])->name('errors');
// Route::get('/development', [ErrorController::class , 'error503'])->name('development');

Route::group(['prefix' => 'admin', 'middleware' => ['loggin', 'system']], function () {
    Voyager::routes();

    Route::resource('sales', SaleController::class);
    Route::get('sales/ajax/list', [SaleController::class, 'list']);
    // Route::get('sales/item/stock/ajax', [AjaxController::class, 'itemStockList']);//Para obtener los item que hay disponible en el inventario
    Route::get('sales/{id}/prinf', [SaleController::class, 'prinf'])->name('sales.prinf');

    Route::get('people', [PersonController::class, 'index'])->name('voyager.people.index');
    Route::get('people/ajax/list', [PersonController::class, 'list']);
    Route::post('people', [PersonController::class, 'store'])->name('voyager.people.store');
    Route::put('people/{id}', [PersonController::class, 'update'])->name('voyager.people.update');
    Route::get('people/{id}', [PersonController::class, 'show'])->name('voyager.people.show');


    // Item
    Route::get('items', [ItemController::class, 'index'])->name('voyager.items.index');
    Route::get('items/ajax/list', [ItemController::class, 'list']);
    Route::post('items', [ItemController::class, 'store'])->name('voyager.items.store');
    Route::put('items/{id}', [ItemController::class, 'update'])->name('voyager.items.update');
    Route::get('items/{id}', [ItemController::class, 'show'])->name('voyager.items.show');

    Route::get('item/stock/ajax', [AjaxController::class, 'itemStockList']);//Para obtener los item que hay disponible en el inventario

    Route::get('items/{id}/stock/ajax/list', [ItemController::class, 'listStock']);//Para listar el historial de stock
    Route::post('items/{id}/stock', [ItemController::class, 'storeStock'])->name('items-stock.store');
    Route::delete('items/{id}/stock/{stock}', [ItemController::class, 'destroyStock'])->name('items-stock.destroy');

    
    Route::get('whatsapp', [MicroServiceController::class, 'message'])->name('whatsapp.message');

    // Users
    Route::get('users/ajax/list', [UserController::class, 'list']);
    Route::post('users/store', [UserController::class, 'store'])->name('voyager.users.store');
    Route::put('users/{id}', [UserController::class, 'update'])->name('voyager.users.update');
    Route::delete('users/{id}/deleted', [UserController::class, 'destroy'])->name('voyager.users.destroy');

    // Roles
    Route::get('roles/ajax/list', [RoleController::class, 'list']);


    Route::get('ajax/personList', [AjaxController::class, 'personList']);
    Route::post('ajax/person/store', [AjaxController::class, 'personStore']);

});


// Clear cache
Route::get('/admin/clear-cache', function() {
    Artisan::call('optimize:clear');

    // Artisan::call('db:seed', ['--class' => 'UpdateBreadSeeder']);
    // Artisan::call('db:seed', ['--class' => 'UpdatePermissionsSeeder']);
    
    return redirect('/admin/profile')->with(['message' => 'Cache eliminada.', 'alert-type' => 'success']);
})->name('clear.cache');