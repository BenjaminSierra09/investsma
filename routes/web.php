<?php

use App\Http\Controllers\PageController;
use App\Http\Controllers\PropertiesController;
use App\Livewire\Public\PropertiesSearch;
use Illuminate\Support\Facades\Route;

Route::get('/', [PageController::class, 'home'])->name('home');
Route::get('/nosotros', [PageController::class, 'about'])->name('about');
Route::get('/contacto', [PageController::class, 'contact'])->name('contact');
Route::post('/contacto', [PageController::class, 'contactSubmit'])->name('contact.submit');

Route::get('/p/{page:slug}', [PageController::class, 'show'])->name('page.show');

Route::get('/propiedades', [PageController::class, 'properties'])->name('properties.index');
Route::get('/propiedades/{mlsId}/{slug?}', [PropertiesController::class, 'show'])->name('properties.show');

Route::get('dashboard', function () {
    return redirect()->route('cms.pages');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::livewire('/cms/paginas', 'pages::cms.pages.index')->name('cms.pages');
    Route::livewire('/cms/paginas/form/{pageId?}', 'pages::cms.pages.form')->name('cms.pages.form');

    Route::livewire('/cms/menu-principal', 'pages::cms.menus.index')->name('cms.menus');
    Route::livewire('/cms/menu-principal/form', 'pages::cms.menus.form')->name('cms.menus.form');
});

require __DIR__.'/settings.php';

Route::passkeys();