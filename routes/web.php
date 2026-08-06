<?php

use App\Livewire\HouseholdPlan;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/plan')->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::livewire('plan', HouseholdPlan::class)->name('plan');
    Route::redirect('dashboard', '/plan')->name('dashboard');
});

require __DIR__.'/settings.php';
