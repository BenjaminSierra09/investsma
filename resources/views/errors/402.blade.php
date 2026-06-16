@extends('errors.minimal')

@section('title', __('Pago requerido'))
@section('code', '402')
@section('message', __('La solicitud requiere una confirmación de pago antes de poder continuar.'))
@section('detail', __('Si estabas intentando acceder a un servicio o documento reservado, contáctanos y revisamos el siguiente paso contigo.'))
@section('action', __('Resolver el acceso con el equipo.'))
