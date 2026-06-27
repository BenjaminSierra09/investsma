<?php

use App\Http\Controllers\EditorJsUploadController;
use App\Http\Controllers\ListingController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\PropertiesController;
use Illuminate\Support\Facades\Route;

Route::get('/', [PageController::class, 'home'])->name('home');
Route::get('/nosotros', [PageController::class, 'about'])->name('about');
Route::get('/agentes', [PageController::class, 'agents'])->name('agents.index');
Route::get('/contacto', [PageController::class, 'contact'])->name('contact');
Route::post('/contacto', [PageController::class, 'contactSubmit'])->name('contact.submit');

Route::get('/p/{page:slug}', [PageController::class, 'show'])->name('page.show');

Route::get('/propiedades', [PageController::class, 'properties'])->name('properties.index');
Route::get('/propiedades/mapa', [PropertiesController::class, 'map'])->name('properties.map');
Route::get('/propiedades/{mlsId}/{slug?}', [PropertiesController::class, 'show'])->name('properties.show');
Route::get('/listados', [ListingController::class, 'index'])->name('listings.index');
Route::get('/listados/venta', [ListingController::class, 'sales'])->name('listings.sales');
Route::get('/listados/renta', [ListingController::class, 'rentals'])->name('listings.rentals');
Route::get('/listados/{listing:slug}', [ListingController::class, 'show'])->name('listings.show');
Route::post('/listados/{listing:slug}/contacto', [ListingController::class, 'inquire'])->name('listings.inquire');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::prefix('dashboard')->group(function () {
        Route::get('/', function () {
            return redirect()->route('cms.pages');
        })->name('dashboard');

        Route::livewire('/paginas', 'pages::cms.pages.index')->name('cms.pages');
        Route::livewire('/paginas/form/{pageId?}', 'pages::cms.pages.form')->name('cms.pages.form');

        Route::livewire('/usuarios', 'pages::cms.users.index')->name('cms.users');
        Route::livewire('/menu-principal', 'pages::cms.menus.index')->name('cms.menus');
        Route::livewire('/menu-principal/form', 'pages::cms.menus.form')->name('cms.menus.form');
        Route::livewire('/agentes', 'pages::cms.agents.index')->name('cms.agents');
        Route::livewire('/agentes/form/{agentId?}', 'pages::cms.agents.form')->name('cms.agents.form');
        Route::livewire('/listados', 'pages::cms.listings.index')->name('cms.listings');
        Route::livewire('/listados/form/{listingId?}', 'pages::cms.listings.form')->name('cms.listings.form');
    });

    Route::redirect('/cms', '/dashboard');
    Route::get('/cms/{path}', function (string $path) {
        return redirect('/dashboard/'.$path);
    })->where('path', '.*');

    Route::post('/editorjs/upload', [EditorJsUploadController::class, 'upload'])->name('editorjs.upload');
    Route::post('/editorjs/fetch', [EditorJsUploadController::class, 'fetch'])->name('editorjs.fetch');

    Route::post('/upload/image', [EditorJsUploadController::class, 'upload']);
    Route::post('/upload/image-url', [EditorJsUploadController::class, 'fetch']);
});

require __DIR__.'/settings.php';

Route::passkeys();
