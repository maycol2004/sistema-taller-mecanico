@extends('layouts.app')

@section('title', 'Editar vehículo')
@section('eyebrow', 'Módulo de vehículos')
@section('subtitle', 'Actualiza los datos del vehículo o reasigna su propietario sin perder el historial de órdenes.')

@section('actions')
    <a href="{{ route('vehiculos.show', $vehiculo) }}" class="btn btn-soft btn-sm">
        <i class="bi bi-arrow-left me-1"></i>Volver al detalle
    </a>
@endsection

@section('content')
    <div class="row g-4">
        <div class="col-xl-8">
            <div class="card form-card">
                <div class="card-body">
                    <div class="form-intro">
                        <span class="section-icon section-icon-green"><i class="bi bi-car-front-fill"></i></span>
                        <div>
                            <h2 class="section-title">{{ $vehiculo->placa }} · {{ $vehiculo->descripcion }}</h2>
                            <p class="section-subtitle">Propietario actual: {{ $vehiculo->cliente->nombre_completo }}</p>
                        </div>
                    </div>
                    @include('vehiculos._form', ['vehiculo' => $vehiculo, 'clienteSeleccionado' => null])
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
                        <dt>Placa</dt>
                        <dd>{{ $vehiculo->placa }}</dd>
                        <dt>Propietario</dt>
                        <dd><a href="{{ route('clientes.show', $vehiculo->cliente) }}">{{ $vehiculo->cliente->nombre_completo }}</a></dd>
                        <dt>Órdenes asociadas</dt>
                        <dd>{{ $vehiculo->ordenes()->count() }}</dd>
                        <dt>Registrado</dt>
                        <dd>{{ $vehiculo->created_at->format('d/m/Y H:i') }}</dd>
                    </dl>
                    <div class="alert alert-warning small mt-4 mb-0">
                        <i class="bi bi-exclamation-triangle me-1"></i>
                        No se puede eliminar un vehículo con órdenes de servicio registradas.
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
