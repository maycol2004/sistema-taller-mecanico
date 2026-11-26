@extends('errors.layout')

@section('title', 'Recurso no encontrado')
@section('code', '404')
@section('icon', 'search')

@section('message')
    La página o el recurso que buscas no existe, fue movido o ya no está disponible.
@endsection

@section('actions')
    @auth
        <a href="{{ route('dashboard') }}" class="btn btn-brand">
            <i class="bi bi-grid-1x2-fill me-1"></i>Ir al panel
        </a>
        <a href="{{ url()->previous() }}" class="btn btn-soft">
            <i class="bi bi-arrow-left me-1"></i>Volver
        </a>
    @else
        <a href="{{ route('login') }}" class="btn btn-brand">
            <i class="bi bi-box-arrow-in-right me-1"></i>Iniciar sesión
        </a>
    @endauth
@endsection
