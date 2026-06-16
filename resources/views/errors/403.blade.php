@extends('errors.minimal')

@section('title', __('Acceso restringido'))
@section('code', '403')
@section('message', __($exception->getMessage() ?: 'No tienes permisos para ver esta página.'))
@section('detail', __('Si esperabas encontrar información de una propiedad o una herramienta interna, revisa tu sesión o escríbenos para ayudarte.'))
@section('action', __('Encontrar la ruta correcta.'))
