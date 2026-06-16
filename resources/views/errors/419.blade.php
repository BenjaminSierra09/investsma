@extends('errors.minimal')

@section('title', __('La sesión expiró'))
@section('code', '419')
@section('message', __('Por seguridad, el formulario o la sesión que estabas usando ya no está activo.'))
@section('detail', __('Vuelve a cargar la página e intenta enviar la información otra vez. Si estabas consultando una propiedad, tu búsqueda puede continuar desde el inicio.'))
@section('action', __('Recuperar el ritmo de la consulta.'))
