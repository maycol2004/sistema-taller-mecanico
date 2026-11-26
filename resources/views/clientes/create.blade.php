@extends('layouts.app')

@section('title', 'Nuevo cliente')
@section('eyebrow', 'Módulo de clientes')
@section('subtitle', 'Registra los datos del cliente. El documento debe ser un DNI de 8 dígitos o un RUC de 11 dígitos.')

@section('actions')
    <a href="{{ route('clientes.index') }}" class="btn btn-soft btn-sm">
        <i class="bi bi-arrow-left me-1"></i>Volver al listado
    </a>
@endsection

@section('content')
    <div class="row g-4">
        <div class="col-xl-8">
            <div class="card form-card">
                <div class="card-body">
                    <div class="form-intro">
                        <span class="section-icon"><i class="bi bi-person-plus-fill"></i></span>
                        <div>
                            <h2 class="section-title">Datos del cliente</h2>
                            <p class="section-subtitle">Los campos marcados con <span class="required-mark">*</span> son obligatorios.</p>
                        </div>
                    </div>
                    @include('clientes._form', ['cliente' => null])
                </div>
            </div>
        </div>
        <div class="col-xl-4">
            <div class="card">
                <div class="card-header">
                    <h2 class="section-title"><i class="bi bi-lightbulb me-2 text-warning"></i>Recomendaciones</h2>
                </div>
                <div class="card-body">
                    <div class="d-flex gap-3 mb-3">
                        <span class="badge-soft badge-soft-blue flex-shrink-0">01</span>
                        <p class="mb-0 small text-secondary">Verifica el DNI o RUC antes de guardar. El documento no puede repetirse.</p>
                    </div>
                    <div class="d-flex gap-3 mb-3">
                        <span class="badge-soft badge-soft-blue flex-shrink-0">02</span>
                        <p class="mb-0 small text-secondary">El correo y el teléfono permiten contactar al cliente ante cualquier avance de la orden.</p>
                    </div>
                    <div class="d-flex gap-3">
                        <span class="badge-soft badge-soft-blue flex-shrink-0">03</span>
                        <p class="mb-0 small text-secondary">Después de registrar al cliente podrás agregar sus vehículos desde su ficha.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
