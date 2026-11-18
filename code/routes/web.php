<?php

use App\Modules\Activities\Http\Controllers\ActivityController;
use App\Modules\Companies\Http\Controllers\CompanyController;
use App\Modules\Contacts\Http\Controllers\ContactController;
use App\Modules\Dashboard\Http\Controllers\DashboardController;
use App\Modules\Deals\Http\Controllers\DealController;
use Illuminate\Support\Facades\Route;

Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard.index');

Route::get('/companies', [CompanyController::class, 'index'])->name('companies.index');
Route::post('/companies', [CompanyController::class, 'store'])->name('companies.store');

Route::get('/contacts', [ContactController::class, 'index'])->name('contacts.index');
Route::post('/contacts', [ContactController::class, 'store'])->name('contacts.store');

Route::get('/deals', [DealController::class, 'index'])->name('deals.index');
Route::post('/deals', [DealController::class, 'store'])->name('deals.store');
Route::patch('/deals/{deal}/stage', [DealController::class, 'updateStage'])->name('deals.stage.update');

Route::get('/activities', [ActivityController::class, 'index'])->name('activities.index');
Route::post('/activities', [ActivityController::class, 'store'])->name('activities.store');
Route::patch('/activities/{activity}/toggle', [ActivityController::class, 'toggle'])->name('activities.toggle');
