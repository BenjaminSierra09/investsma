<h2>Nuevo contacto sobre un listado propio</h2>

<p><strong>Propiedad:</strong> {{ $listing->title }}</p>
<p><strong>Enlace:</strong> {{ route('listings.show', $listing) }}</p>
<p><strong>Nombre:</strong> {{ $data['nombre'] ?? 'N/D' }}</p>
<p><strong>Correo:</strong> {{ $data['email'] ?? 'N/D' }}</p>
<p><strong>Teléfono:</strong> {{ $data['telefono'] ?? 'N/D' }}</p>

<p><strong>Mensaje:</strong></p>
<p>{{ $data['mensaje'] ?? 'N/D' }}</p>
