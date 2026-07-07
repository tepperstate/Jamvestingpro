<?php

use App\Http\Controllers\Admin\OnboardingController;
use Illuminate\Support\Facades\Route;

Route::get('/onboarding', [OnboardingController::class, 'index'])->name('admin.onboarding.index');
Route::get('/onboarding/create', [OnboardingController::class, 'create'])->name('admin.onboarding.create');
Route::post('/onboarding/store', [OnboardingController::class, 'store'])->name('admin.onboarding.store');
Route::get('/onboarding/edit/{question}', [OnboardingController::class, 'edit'])->name('admin.onboarding.edit');
Route::post('/onboarding/update/{question}', [OnboardingController::class, 'update'])->name('admin.onboarding.update');
Route::get('/onboarding/delete/{question}', [OnboardingController::class, 'destroy'])->name('admin.onboarding.delete');
