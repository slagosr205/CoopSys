<?php

use Filament\Http\Controllers\RedirectToHomeController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect('admin');
});

Route::get('/home',function(){

    return view('welcome');
});