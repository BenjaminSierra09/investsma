<h2>Nuevo contacto desde investsma.com</h2>

<p><strong>Nombre:</strong> {{ $data['nombre'] ?? 'N/D' }}</p>
<p><strong>Correo:</strong> {{ $data['email'] ?? 'N/D' }}</p>
<p><strong>Teléfono:</strong> {{ $data['telefono'] ?? 'N/D' }}</p>
<p><strong>Objetivo:</strong> {{ $data['objetivo'] ?? 'N/D' }}</p>

@if (!empty($data['mensaje']))
    <p><strong>Mensaje:</strong></p>
    <p>{{ $data['mensaje'] }}</p>
@endif
