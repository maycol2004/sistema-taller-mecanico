@extends('layouts.app')

@section('title', 'Nuevo vehículo')
@section('eyebrow', 'Módulo de vehículos')
@section('subtitle', 'Registra un vehículo y asígnalo al cliente propietario. La placa debe ser única en el sistema.')

@section('actions')
    <a href="{{ route('vehiculos.index') }}" class="btn btn-soft btn-sm">
        <i class="bi bi-arrow-left me-1"></i>Volver al listado
    </a>
@endsection

@section('content')
    @if ($clientes->isEmpty())
        <div class="card">
            <div class="card-body">
                <x-empty-state icon="people" title="Primero registra un cliente" text="Todo vehículo debe tener un propietario. Crea un cliente para poder registrar su unidad.">
                    <a href="{{ route('clientes.create') }}" class="btn btn-brand btn-sm">Registrar cliente</a>
                </x-empty-state>
            </div>
        </div>
    @else
        <div class="row g-4">
            <div class="col-xl-8">
                <div class="card form-card">
                    <div class="card-body">
                        <div class="form-intro">
                            <span class="section-icon section-icon-green"><i class="bi bi-car-front-fill"></i></span>
                            <div>
                                <h2 class="section-title">Datos del vehículo</h2>
                                <p class="section-subtitle">Los campos marcados con <span class="required-mark">*</span> son obligatorios.</p>
                            </div>
                        </div>
                        @include('vehiculos._form', ['vehiculo' => null, 'clienteSeleccionado' => $clienteSeleccionado])
                    </div>
                </div>
            </div>
            <div class="col-xl-4">
                <div class="card">
                    <div class="card-header">
                        <h2 class="section-title"><i class="bi bi-info-circle me-2 text-primary"></i>Datos útiles</h2>
                    </div>
                    <div class="card-body">
                        <div class="stock-item">
                            <div>
                                <div class="stock-name">Placa</div>
                                <div class="stock-code">Formato peruano</div>
                            </div>
                            <span class="plate-badge">ABC-123</span>
                        </div>
                        <div class="stock-item">
                            <div>
                                <div class="stock-name">Kilometraje</div>
                                <div class="stock-code">Se usa para programar mantenimientos</div>
                            </div>
                            <i class="bi bi-speedometer2 fs-5 text-secondary"></i>
                        </div>
                        <div class="alert alert-light border small mt-3 mb-0">
                            <i class="bi bi-lightbulb text-warning me-1"></i>
                            Si el vehículo ya existía, puedes buscarlo por placa antes de crear uno nuevo.
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
@endsection
