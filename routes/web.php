<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\iettController;
use App\Http\Resources\iettResource;
use App\Models\iettsefersaatleri;

Route::get('/', function () {
    return Inertia::render('Home');
});

Route::get('/iett-generate-html-start', [iettController::class, 'startQueueIETTProcess']);
Route::get('/iett-generate-html', [iettController::class, 'generateIETTSeferSaatleri']);
Route::get('/iett-generate-table', [iettController::class, 'generateIETTSeferSaatleriTable']);
Route::get('/iett-json-export/{id}', function (string $id) {
    return new iettResource(iettsefersaatleri::findOrFail($id));
});
Route::get('/iett-json-export', function () {
    return iettResource::collection(iettsefersaatleri::all());
});

Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
