<?php

use Symfony\Component\HttpKernel\Exception\HttpException;

it('renders the customized error views', function (string $status, string $title, string $message): void {
    $this->withoutVite();

    $view = $this->view("errors.{$status}", [
        'exception' => new HttpException((int) $status),
    ]);

    $view
        ->assertSee($title)
        ->assertSee($message)
        ->assertSee('investsma')
        ->assertSee('Volver al inicio')
        ->assertSee('Ver propiedades')
        ->assertSee(route('home'), false)
        ->assertSee(route('properties.index'), false)
        ->assertSee(route('contact'), false);
})->with([
    '401' => ['401', 'Necesitas iniciar sesión', 'Esta sección está protegida'],
    '402' => ['402', 'Pago requerido', 'requiere una confirmación de pago'],
    '403' => ['403', 'Acceso restringido', 'No tienes permisos para ver esta página'],
    '404' => ['404', 'No encontramos esa página', 'pudo cambiar de dirección'],
    '419' => ['419', 'La sesión expiró', 'el formulario o la sesión'],
    '429' => ['429', 'Demasiadas solicitudes', 'muchas solicitudes en poco tiempo'],
    '500' => ['500', 'Algo no salió como esperábamos', 'un problema técnico'],
    '503' => ['503', 'Estamos dando mantenimiento', 'temporalmente fuera de servicio'],
]);

it('uses the customized 404 page for missing routes', function (): void {
    $this->withoutVite();

    $response = $this->get('/propiedad-que-no-existe');

    $response
        ->assertNotFound()
        ->assertSee('No encontramos esa página')
        ->assertSee('Volver al inicio');
});
