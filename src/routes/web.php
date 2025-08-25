<?php

use Illuminate\Support\Facades\Route;
use admin\product_reports\Controllers\ReportManagerController;

Route::name('admin.')->middleware(['web', 'admin.auth'])->group(function () {
    Route::get('reports', [ReportManagerController::class, 'index'])->name('reports.index');
});
