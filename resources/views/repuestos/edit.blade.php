@extends('layouts.app')

@section('title', 'Editar repuesto')
@section('eyebrow', 'Inventario básico')
@section('subtitle', 'Actualiza los datos del repuesto, su precio unitario o las existencias disponibles.')

@section('actions')
    <a href="{{ route('repuestos.show', $repuesto) }}" class="btn btn-soft btn-sm">
        <i class="bi bi-arrow-left me-1"></i>Volver al detalle
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
                            <h2 class="section-title">{{ $repuesto->nombre }}</h2>
                            <p class="section-subtitle">{{ $repuesto->codigo }} · Stock actual: {{ $repuesto->stock }} unidades</p>
                        </div>
                    </div>
                    @include('repuestos._form', ['repuesto' => $repuesto])
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
                        <dt>SKU</dt>
                        <dd>{{ $repuesto->codigo }}</dd>
                        <dt>Marca</dt>
                        <dd>{{ $repuesto->marca ?: 'Sin marca' }}</dd>
                        <dt>Precio unitario</dt>
                        <dd class="money">S/ {{ number_format((float) $repuesto->precio_unitario, 2) }}</dd>
                        <dt>Stock actual</dt>
                        <dd><x-stock-badge :stock="$repuesto->stock" /></dd>
                        <dt>Órdenes que lo usan</dt>
                        <dd>{{ $repuesto->ordenes()->count() }}</dd>
                    </dl>
                    <div class="alert alert-warning small mt-4 mb-0">
                        <i class="bi bi-exclamation-triangle me-1"></i>
                        Si el repuesto está asignado a órdenes, no podrá eliminarse del inventario.
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
