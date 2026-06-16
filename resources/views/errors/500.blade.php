@extends('errors.minimal')

@section('title', __('Algo no salió como esperábamos'))
@section('code', '500')
@section('message', __('Tuvimos un problema técnico al preparar esta página o procesar la solicitud.'))
@section('detail', __('Nuestro equipo puede revisar el caso. Mientras tanto, puedes volver al inicio o explorar propiedades disponibles.'))
@section('action', __('Retomar desde una página estable.'))
