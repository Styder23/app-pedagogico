<?php

use Illuminate\Support\Facades\Route;
use App\Livewire\Login;
use App\Livewire\Dashboard;
use App\Http\Controllers\DataUploadController;

Route::get('/login', Login::class)->name('login');
Route::get('/dashboard', Dashboard::class)->name('dashboard')->middleware('auth.session');
Route::get('/usuarios', function () {
    return view('pages.usuarios');
})->name('usuarios')->middleware(['auth.session', 'role:admin,director']);
Route::get('/instituciones', function () {
    return view('pages.instituciones');
})->name('instituciones')->middleware(['auth.session', 'role:admin']);
Route::get('/inscripciones', function () {
    return view('pages.inscripciones');
})->name('inscripciones')->middleware('auth.session');
Route::get('/asistencia', function () {
    return view('pages.asistencia');
})->name('asistencia')->middleware('auth.session');
Route::get('/notas', function () {
    return view('pages.notas');
})->name('notas')->middleware('auth.session');
Route::get('/prediccion', function () {
    return view('pages.prediccion');
})->name('prediccion')->middleware('auth.session');

Route::get('/', function () {
    if (auth()->check()) {
        return redirect()->route('dashboard');
    }
    return redirect()->route('login');
});

Route::post('/uploads/{tipo}', [DataUploadController::class, 'store'])
    ->name('uploads.store')
    ->middleware('auth.session');
Route::get('/uploads/template/{tipo}', [DataUploadController::class, 'template'])
    ->name('uploads.template')
    ->middleware('auth.session');
