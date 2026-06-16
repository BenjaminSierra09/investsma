@extends('errors.minimal')

@section('title', __('Necesitas iniciar sesión'))
@section('code', '401')
@section('message', __('Esta sección está protegida para mantener tus datos y consultas inmobiliarias seguras.'))
@section('detail', __('Inicia sesión nuevamente o vuelve al inicio para continuar explorando propiedades.'))
@section('action', __('Volver a una sesión segura.'))
