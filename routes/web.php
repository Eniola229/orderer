<?php

use Illuminate\Support\Facades\Route;

// -------------------------------------------------------
// STOREFRONT — Buyer
// -------------------------------------------------------
Route::get('/', fn() => view('storefront.home'))->name('home');

// Buyer auth + dashboard routes added in Phase 3

// -------------------------------------------------------
// SELLER portal
// -------------------------------------------------------
require __DIR__.'/seller.php';

// -------------------------------------------------------
// ADMIN portal
// -------------------------------------------------------
require __DIR__.'/admin.php';