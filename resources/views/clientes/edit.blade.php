@extends('layouts.app')

@section('title', 'Editar cliente')
@section('eyebrow', 'Módulo de clientes')
@section('subtitle', 'Actualiza los datos de contacto del cliente sin modificar su historial de vehículos y órdenes.')

@section('actions')
    <a href="{{ route('clientes.show', $cliente) }}" class="btn btn-soft btn-sm">
        <i class="bi bi-arrow-left me-1"></i>Volver al detalle
    </a>
@endsection

@section('content')
    <div class="row g-4">
        <div class="col-xl-8">
            <div class="card form-card">
                <div class="card-body">
                    <div class="form-intro">
                        <span class="section-icon"><i class="bi bi-pencil-square"></i></span>
                        <div>
                            <h2 class="section-title">{{ $cliente->nombre_completo }}</h2>
                            <p class="section-subtitle">{{ $cliente->tipo_documento }} {{ $cliente->documento }} · {{ $cliente->vehiculos()->count() }} vehículo(s)</p>
                        </div>
                    </div>
                    @include('clientes._form', ['cliente' => $cliente])
                </div>
            </div>
        </div>
        <div class="col-xl-4">
            <div class="card">
                <div class="card-header">
                    <h2 class="section-title">Información del registro</h2>
                </div>
                <div class="card-body">
                    <dl class="detail-list">
                        <dt>Documento</dt>
                        <dd>{{ $cliente->tipo_documento }} · {{ $cliente->documento }}</dd>
                        <dt>Registrado</dt>
                        <dd>{{ $cliente->created_at->format('d/m/Y H:i') }}</dd>
                        <dt>Última actualización</dt>
                        <dd>{{ $cliente->updated_at->format('d/m/Y H:i') }}</dd>
                    </dl>
                    <div class="alert alert-warning small mt-4 mb-0">
                        <i class="bi bi-exclamation-triangle me-1"></i>
                        Si el cliente tiene vehículos u órdenes, no podrá eliminarse hasta reasignarlos.
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
