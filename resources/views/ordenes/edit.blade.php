@extends('layouts.app')

@section('title', 'Editar orden de servicio')
@section('eyebrow', 'Módulo central')
@section('subtitle', 'Actualiza los datos de la orden, sus servicios y repuestos. El costo total se recalcula al guardar.')

@section('actions')
    <a href="{{ route('ordenes.show', $orden) }}" class="btn btn-soft btn-sm">
        <i class="bi bi-arrow-left me-1"></i>Volver al detalle
    </a>
@endsection

@section('content')
    @include('ordenes._form')
@endsection
