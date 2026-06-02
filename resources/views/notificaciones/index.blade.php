@extends('layouts.app')

@section('title', 'Notificaciones')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="fw-bold">Notificaciones</h2>
        <p class="text-muted">Alertas automáticas generadas por el sistema.</p>
    </div>
</div>

<div class="card">
    <div class="card-body table-responsive">
        <table class="table table-hover align-middle">
            <thead class="table-light">
                <tr>
                    <th>ID</th>
                    <th>Tipo</th>
                    <th>Título</th>
                    <th>Mensaje</th>
                    <th>Estado</th>
                    <th>Fecha</th>
                </tr>
            </thead>
            <tbody>
                @forelse($notificaciones as $notificacion)
                    <tr>
                        <td>{{ $notificacion->id }}</td>
                        <td>
                            <span class="badge bg-info">
                                {{ ucfirst($notificacion->tipo) }}
                            </span>
                        </td>
                        <td>{{ $notificacion->titulo }}</td>
                        <td>{{ $notificacion->mensaje }}</td>
                        <td>
                            @if($notificacion->leida)
                                <span class="badge bg-secondary">Leída</span>
                            @else
                                <span class="badge bg-warning text-dark">Pendiente</span>
                            @endif
                        </td>
                        <td>{{ $notificacion->created_at->format('d/m/Y H:i') }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center text-muted">
                            No hay notificaciones registradas.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        {{ $notificaciones->links() }}
    </div>
</div>

@endsection