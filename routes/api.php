<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\MoviesController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::get('movies', [MoviesController::class, 'getList']);
Route::get('movies/{id}', [MoviesController::class, 'detailsMovie']);
Route::post('movies', [MoviesController::class, 'store']);
Route::patch('movies/{id}', [MoviesController::class, 'update']);
Route::delete('movies/{id}', [MoviesController::class, 'destroy']);