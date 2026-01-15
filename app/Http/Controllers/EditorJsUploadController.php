<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

class EditorJsUploadController extends Controller
{
    public function upload(Request $request)
    {
        // EditorJS image tool envía el campo como "image"; aceptamos ambos nombres.
        $request->merge(['file' => $request->file('file') ?? $request->file('image')]);

        $data = $request->validate([
            'file' => ['required', 'image', 'max:5120'], // 5MB
        ]);

        $path = $data['file']->store('editorjs', 'public');
        $url = Storage::disk('public')->url($path);

        return response()->json([
            'success' => 1,
            'file' => ['url' => $url],
        ]);
    }

    public function fetch(Request $request)
    {
        $validated = $request->validate([
            'url' => ['required', 'url'],
        ]);

        $response = Http::timeout(10)->get($validated['url']);

        if (! $response->successful()) {
            return response()->json(['success' => 0, 'message' => 'No se pudo descargar la imagen'], Response::HTTP_BAD_REQUEST);
        }

        $mime = $response->header('Content-Type');
        if (! $mime || ! str_starts_with($mime, 'image/')) {
            return response()->json(['success' => 0, 'message' => 'El recurso no es una imagen'], Response::HTTP_BAD_REQUEST);
        }

        $extension = explode('/', $mime)[1] ?? 'jpg';
        $filename = Str::uuid().'.'.$extension;
        $path = 'editorjs/'.$filename;

        Storage::disk('public')->put($path, $response->body());
        $url = Storage::disk('public')->url($path);

        return response()->json([
            'success' => 1,
            'file' => ['url' => $url],
        ]);
    }
}
