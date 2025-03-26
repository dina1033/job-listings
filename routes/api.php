<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\JobController;

Route::get('/jobs',[JobController::class , 'index'])->name('jobs');
