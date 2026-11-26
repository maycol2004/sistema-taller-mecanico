@extends('errors.layout')

@section('title', 'Acceso no autorizado')
@section('code', '403')
@section('icon', 'shield-lock-fill')

@section('message')
    Tu rol no tiene permisos para realizar esta acción.
    @auth
        <span class="d-block mt-2 small">
            Sesión: <strong>{{ auth()->user()->name }}</strong> ·
            <span class="badge-soft {{ auth()->user()->rol_badge }}">{{ auth()->user()->rol_label }}</span>
        </span>
    @endauth
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
