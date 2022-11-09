<?php

use App\Events\TestEvent;
use App\Mail\TestMail;
use App\Models\User;
use App\Notifications\ExampleNotification;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Mail;

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

Route::get('/', function () {
    return view('welcome');
});

Route::get('/event', function() {
    event(new TestEvent('test message'));
    return 'test event sent';
});


