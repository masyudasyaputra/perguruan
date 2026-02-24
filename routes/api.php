<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ProductController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// Mengelompokkan route produk
Route::apiResource('products', ProductController::class);

// Jika ingin cek manual, padanannya adalah:
// GET    /api/products          -> index
// POST   /api/products          -> store
// GET    /api/products/{id}     -> show
// PUT    /api/products/{id}     -> update
// DELETE /api/products/{id}     -> destroy