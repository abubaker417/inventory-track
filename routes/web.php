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
// Route::get('/', function () {
//     return view('welcome');
// });
