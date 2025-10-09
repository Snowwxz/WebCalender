<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('landing');
});

Route::get('/hari', function () {
    return view('landing_hari');
});

Route::get('/tahun', function () {
    return view('landing_tahun');
});
