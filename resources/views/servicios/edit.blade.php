@extends('layouts.app')

@section('title', 'Editar servicio')
@section('eyebrow', 'Catálogo de mano de obra')
@section('subtitle', 'Actualiza el precio o la duración del servicio. Las órdenes anteriores conservarán el precio con el que se registraron.')

@section('actions')
    <a href="{{ route('servicios.show', $servicio) }}" class="btn btn-soft btn-sm">
        <i class="bi bi-arrow-left me-1"></i>Volver al detalle
    </a>
@endsection

@section('content')
    <div class="row g-4">
        <div class="col-xl-8">
            <div class="card form-card">
                <div class="card-body">
                    <div class="form-intro">
                        <span class="section-icon section-icon-blue"><i class="bi bi-wrench-adjustable"></i></span>
                        <div>
                            <h2 class="section-title">{{ $servicio->nombre }}</h2>
                            <p class="section-subtitle">S/ {{ number_format((float) $servicio->precio_base, 2) }} · {{ $servicio->duracion_estimada ?: 'Duración no definida' }}</p>
                        </div>
                    </div>
                    @include('servicios._form', ['servicio' => $servicio])
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
                        <dt>Precio base</dt>
                        <dd class="money">S/ {{ number_format((float) $servicio->precio_base, 2) }}</dd>
                        <dt>Duración estimada</dt>
                        <dd>{{ $servicio->duracion_estimada ?: 'No definida' }}</dd>
                        <dt>Órdenes que lo usan</dt>
                        <dd>{{ $servicio->ordenes()->count() }}</dd>
                        <dt>Registrado</dt>
                        <dd>{{ $servicio->created_at->format('d/m/Y H:i') }}</dd>
                    </dl>
                    <div class="alert alert-warning small mt-4 mb-0">
                        <i class="bi bi-exclamation-triangle me-1"></i>
                        Si el servicio está asignado a órdenes, no podrá eliminarse del catálogo.
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
