@extends('layouts.app')

@section('title', 'Nuevo repuesto')
@section('eyebrow', 'Inventario básico')
@section('subtitle', 'Registra un repuesto con su código único, precio unitario y stock inicial disponible.')

@section('actions')
    <a href="{{ route('repuestos.index') }}" class="btn btn-soft btn-sm">
        <i class="bi bi-arrow-left me-1"></i>Volver al inventario
    </a>
@endsection

@section('content')
    <div class="row g-4">
        <div class="col-xl-8">
            <div class="card form-card">
                <div class="card-body">
                    <div class="form-intro">
                        <span class="section-icon section-icon-purple"><i class="bi bi-box-seam-fill"></i></span>
                        <div>
                            <h2 class="section-title">Datos del repuesto</h2>
                            <p class="section-subtitle">Los campos marcados con <span class="required-mark">*</span> son obligatorios.</p>
                        </div>
                    </div>
                    @include('repuestos._form', ['repuesto' => null])
                </div>
            </div>
        </div>
        <div class="col-xl-4">
            <div class="card">
                <div class="card-header">
                    <h2 class="section-title"><i class="bi bi-info-circle me-2 text-primary"></i>Control de inventario</h2>
                </div>
                <div class="card-body">
                    <div class="stock-item">
                        <div>
                            <div class="stock-name">Código / SKU</div>
                            <div class="stock-code">Identificador único del repuesto</div>
                        </div>
                        <span class="badge-soft badge-soft-slate">REP-001</span>
                    </div>
                    <div class="stock-item">
                        <div>
                            <div class="stock-name">Alerta de stock</div>
                            <div class="stock-code">Se activa con 5 unidades o menos</div>
                        </div>
                        <span class="badge text-bg-danger rounded-pill">≤ 5</span>
                    </div>
                    <div class="alert alert-light border small mt-3 mb-0">
                        <i class="bi bi-lightbulb text-warning me-1"></i>
                        El stock se valida al asignar repuestos a una orden, pero no se descuenta automáticamente en esta parte del proyecto.
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
