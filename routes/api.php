<?php

use App\Http\Controllers\EditorJsUploadController;
use Illuminate\Support\Facades\Route;

Route::post('/editorjs/upload', [EditorJsUploadController::class, 'upload']);
Route::post('/editorjs/fetch', [EditorJsUploadController::class, 'fetch']);
