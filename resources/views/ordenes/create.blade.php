@extends('layouts.app')

@section('title', 'Nueva orden de servicio')
@section('eyebrow', 'Módulo central')
@section('subtitle', 'Registra el vehículo, el diagnóstico y los trabajos a realizar. El total se calcula automáticamente.')

@section('actions')
    <a href="{{ route('ordenes.index') }}" class="btn btn-soft btn-sm">
        <i class="bi bi-arrow-left me-1"></i>Volver al listado
    </a>
@endsection

@section('content')
    @include('ordenes._form')
@endsection
