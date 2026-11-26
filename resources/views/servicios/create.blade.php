@extends('layouts.app')

@section('title', 'Nuevo servicio')
@section('eyebrow', 'Catálogo de mano de obra')
@section('subtitle', 'Registra un servicio con su precio base y duración estimada. Podrás asignarlo a cualquier orden.')

@section('actions')
    <a href="{{ route('servicios.index') }}" class="btn btn-soft btn-sm">
        <i class="bi bi-arrow-left me-1"></i>Volver al catálogo
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
                            <h2 class="section-title">Datos del servicio</h2>
                            <p class="section-subtitle">Los campos marcados con <span class="required-mark">*</span> son obligatorios.</p>
                        </div>
                    </div>
                    @include('servicios._form', ['servicio' => null])
                </div>
            </div>
        </div>
        <div class="col-xl-4">
            <div class="card">
                <div class="card-header">
                    <h2 class="section-title"><i class="bi bi-lightbulb me-2 text-warning"></i>Buenas prácticas</h2>
                </div>
                <div class="card-body">
                    <div class="stock-item">
                        <div>
                            <div class="stock-name">Precio base</div>
                            <div class="stock-code">Mano de obra sin repuestos</div>
                        </div>
                        <span class="badge-soft badge-soft-orange">S/</span>
                    </div>
                    <div class="stock-item">
                        <div>
                            <div class="stock-name">Duración</div>
                            <div class="stock-code">Ayuda a planificar el taller</div>
                        </div>
                        <i class="bi bi-clock-history fs-5 text-secondary"></i>
                    </div>
                    <div class="alert alert-light border small mt-3 mb-0">
                        <i class="bi bi-info-circle text-primary me-1"></i>
                        Los precios de las órdenes quedan congelados al momento de guardarlas, aunque luego cambies el catálogo.
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
