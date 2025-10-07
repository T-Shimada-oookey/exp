<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\trial\InterfaceController;

Route::get('/', function () {
    return view('welcome');
});

Route::group(['prefix'=> 'trial', 'as' => 'trial.'], function () {
    Route::resource('interface', InterfaceController::class);
});