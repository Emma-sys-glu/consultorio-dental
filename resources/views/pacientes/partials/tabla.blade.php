<div class="card-header bg-white d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-2">
    <strong>Listado de pacientes</strong>
    <span class="text-muted small">
        {{ $pacientes->total() }} registros
    </span>
</div>

<div class="card-body p-0">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th>Paciente</th>
                    <th>Contacto</th>
                    <th>Fecha nacimiento</th>
                    <th>Tipo sangre</th>
                    <th>Alergias</th>
                    <th class="text-end">Acciones</th>
                </tr>
            </thead>

            <tbody>
                @forelse($pacientes as $paciente)
                    <tr>
                        <td>
                            <div class="fw-semibold">
                                {{ $paciente->nombre }}
                                {{ $paciente->apellido_paterno }}
                                {{ $paciente->apellido_materno }}
                            </div>
                            <div class="small text-muted">
                                ID {{ $paciente->id }}
                                @if($paciente->curp)
                                    · CURP {{ $paciente->curp }}
                                @endif
                            </div>
                        </td>

                        <td>
                            <div>{{ $paciente->telefono }}</div>
                            <div class="small text-muted">{{ $paciente->correo }}</div>
                        </td>

                        <td>{{ $paciente->fecha_nacimiento }}</td>

                        <td>
                            @if($paciente->tipo_sangre)
                                <span class="badge text-bg-light border">{{ $paciente->tipo_sangre }}</span>
                            @else
                                <span class="text-muted small">Sin dato</span>
                            @endif
                        </td>

                        <td>
                            @if($paciente->alergias)
                                <span class="text-danger">{{ $paciente->alergias }}</span>
                            @else
                                <span class="text-muted small">No registradas</span>
                            @endif
                        </td>

                        <td class="text-end">
                            <div class="d-inline-flex gap-1 flex-wrap">
                                <a href="{{ route('pacientes.detalle', $paciente) }}" class="btn btn-info btn-sm">
                                    Ver detalles
                                </a>

                                <a href="{{ route('pacientes.editar', $paciente) }}" class="btn btn-primary btn-sm">
                                    Editar
                                </a>

                                @if(in_array(auth()->user()->rol, ['administrador', 'recepcionista']))
                                <form action="{{ route('pacientes.eliminar', $paciente) }}" method="POST">
                                    @csrf
                                    @method('DELETE')

                                    <button class="btn btn-danger btn-sm" onclick="return confirm('¿Seguro que deseas eliminar este paciente?')">
                                        Eliminar
                                    </button>
                                </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center text-muted py-4">
                            No se encontraron pacientes.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@if($pacientes->hasPages())
    <div class="card-footer bg-white">
        {{ $pacientes->links() }}
    </div>
@endif
