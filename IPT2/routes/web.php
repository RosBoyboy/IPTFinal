<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;

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

// Login POST handled by web middleware (CSRF protection applies)
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout']);

// Serve SPA on root and any other path (allow client-side routing)
Route::get('/', [LoginController::class, 'showSpa']);
Route::get('/{any}', [LoginController::class, 'showSpa'])->where('any', '.*');
