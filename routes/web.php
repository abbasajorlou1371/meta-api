<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\FileUploadController;

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

Route::permanentRedirect('/', 'https://metarang.com');

Route::view('/upload', 'upload');

Route::post('upload', [FileUploadController::class, 'upload'])->name('upload');
