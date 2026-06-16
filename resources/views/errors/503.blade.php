@extends('errors.minimal')

@section('title', __('Estamos dando mantenimiento'))
@section('code', '503')
@section('message', __('La plataforma está temporalmente fuera de servicio mientras ajustamos detalles.'))
@section('detail', __('Vuelve a intentarlo en unos minutos. Las propiedades y listados estarán disponibles de nuevo en cuanto terminemos.'))
@section('action', __('Volver cuando la casa esté lista.'))
