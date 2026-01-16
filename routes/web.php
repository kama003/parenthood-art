<?php

use Illuminate\Support\Facades\Route;
use App\Livewire\Donors\Index as DonorsIndex;
use App\Livewire\Couples\Index as CouplesIndex;
use App\Livewire\Samples\Index as SamplesIndex;
use App\Livewire\HospitalOrders\Index as HospitalOrdersIndex;
use App\Livewire\Admin\Dashboard as AdminDashboard;

Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', \App\Livewire\Dashboard::class)->name('dashboard');
    Route::get('/donors', DonorsIndex::class)->name('donors.index');
    Route::get('/couples', CouplesIndex::class)->name('couples.index');
    Route::get('/samples', SamplesIndex::class)->name('samples.index');
    Route::get('/hospital-orders', HospitalOrdersIndex::class)->name('hospital-orders.index');
    Route::get('/admin', AdminDashboard::class)->name('admin.dashboard');
    Route::get('/admin/users', \App\Livewire\Admin\Users\Index::class)->name('admin.users.index');
});

require __DIR__.'/settings.php';
