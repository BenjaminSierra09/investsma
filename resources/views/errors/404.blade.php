@extends('errors.minimal')

@section('title', __('No encontramos esa página'))
@section('code', '404')
@section('message', __('La propiedad, listado o página que buscabas pudo cambiar de dirección o dejar de estar disponible.'))
@section('detail', __('Puedes volver al inicio, revisar el inventario activo o contactarnos si tienes una referencia específica.'))
@section('action', __('Seguir buscando buenas oportunidades.'))
