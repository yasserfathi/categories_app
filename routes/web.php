<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Artisan;
use App\Http\Controllers\CategoryController;


Route::get('/', [CategoryController::class, 'index'])->name('catgory/index');
Route::post('category', [CategoryController::class, 'store'])->name('catgory/create');
Route::post('create_subcategory', [CategoryController::class, 'create_subcategory'])->name('subcatgory/create');
