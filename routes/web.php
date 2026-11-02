<?php

use App\Http\Controllers\AgendamentoController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ClienteController;
use App\Http\Controllers\InscricaoController;
use App\Http\Controllers\SiteEventos;
use Illuminate\Support\Facades\Route;

// Páginas públicas
Route::get('/', [SiteEventos::class, 'index'])->name('home');
Route::get('/events/{evento}', [SiteEventos::class, 'show'])->whereNumber('evento')->name('events.show');
Route::post('/events/{evento}/inscricao', [InscricaoController::class, 'store'])
    ->whereNumber('evento')
    ->middleware('throttle:5,1')
    ->name('inscricoes.store');

// Autenticação
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'create'])->name('login');
    Route::post('/login', [AuthController::class, 'store'])->middleware('throttle:5,1');
});

Route::post('/logout', [AuthController::class, 'destroy'])->middleware('auth')->name('logout');

// Administrador e atendente
Route::middleware('auth')->group(function () {
    Route::resource('clientes', ClienteController::class)->except(['show', 'destroy']);
    Route::resource('agendamentos', AgendamentoController::class)->except(['show', 'destroy']);
});

// Somente administrador
Route::middleware(['auth', 'perfil:admin'])->group(function () {
    Route::get('/events/create', [SiteEventos::class, 'create'])->name('events.create');
    Route::post('/events', [SiteEventos::class, 'store'])->name('events.store');
    Route::get('/events/{evento}/edit', [SiteEventos::class, 'edit'])->whereNumber('evento')->name('events.edit');
    Route::put('/events/{evento}', [SiteEventos::class, 'update'])->whereNumber('evento')->name('events.update');
    Route::delete('/events/{evento}', [SiteEventos::class, 'destroy'])->whereNumber('evento')->name('events.destroy');

    Route::delete('/clientes/{cliente}', [ClienteController::class, 'destroy'])->name('clientes.destroy');
    Route::delete('/agendamentos/{agendamento}', [AgendamentoController::class, 'destroy'])->name('agendamentos.destroy');
});
