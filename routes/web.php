<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\InventoryRegisterController;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', [InventoryRegisterController::class, 'index'])->name('inventory.register');
Route::get('/inventory-register/export/pdf', [InventoryRegisterController::class, 'exportPdf'])->name('inventory.register.export.pdf');
Route::get('/inventory-register/export/excel', [InventoryRegisterController::class, 'exportExcel'])->name('inventory.register.export.excel');
Route::get('/inventory-register/export/csv', [\App\Http\Controllers\InventoryRegisterController::class, 'exportCsv'])->name('inventory.register.export.csv');
// Route::get('/', function () {
//     return view('welcome');
// });
