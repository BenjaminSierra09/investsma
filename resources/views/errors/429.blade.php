@extends('errors.minimal')

@section('title', __('Demasiadas solicitudes'))
@section('code', '429')
@section('message', __('Recibimos muchas solicitudes en poco tiempo y necesitamos hacer una pausa breve.'))
@section('detail', __('Espera un momento antes de intentar de nuevo. Esto ayuda a mantener estable la experiencia para todos los visitantes.'))
@section('action', __('Pausar y retomar con calma.'))
