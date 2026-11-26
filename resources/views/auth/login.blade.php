@extends('layouts.guest')

@section('title', 'Iniciar sesión')

@section('content')
    <div class="auth-heading">
        <span class="auth-heading-icon"><i class="bi bi-shield-lock-fill"></i></span>
        <div>
            <h2>Acceso al sistema</h2>
            <p>Ingresa con tus credenciales del taller.</p>
        </div>
    </div>

    @if (session('success'))
        <div class="alert alert-success small" role="alert">
            <i class="bi bi-check-circle-fill me-1"></i>{{ session('success') }}
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger small" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-1"></i>{{ session('error') }}
        </div>
    @endif

    <form action="{{ route('login.store') }}" method="POST" novalidate>
        @csrf

        <div class="mb-3">
            <label for="email" class="form-label">Correo electrónico</label>
            <div class="input-group">
                <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                <input
                    type="email"
                    id="email"
                    name="email"
                    value="{{ old('email') }}"
                    class="form-control @error('email') is-invalid @enderror"
                    placeholder="usuario@taller.com"
                    autocomplete="username"
                    autofocus
                    required
                >
            </div>
            @error('email')
                <div class="invalid-feedback d-block">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="password" class="form-label">Contraseña</label>
            <div class="input-group">
                <span class="input-group-text"><i class="bi bi-lock"></i></span>
                <input
                    type="password"
                    id="password"
                    name="password"
                    class="form-control @error('password') is-invalid @enderror"
                    placeholder="Tu contraseña"
                    autocomplete="current-password"
                    required
                >
                <button class="btn btn-soft" type="button" id="togglePassword" aria-label="Mostrar u ocultar contraseña">
                    <i class="bi bi-eye"></i>
                </button>
            </div>
            @error('password')
                <div class="invalid-feedback d-block">{{ $message }}</div>
            @enderror
        </div>

        <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-4">
            <div class="form-check">
                <input class="form-check-input" type="checkbox" name="remember" id="remember" value="1" @checked(old('remember'))>
                <label class="form-check-label" for="remember">Recordarme</label>
            </div>
            <span class="auth-secure"><i class="bi bi-lock-fill"></i>Sesión cifrada</span>
        </div>

        <button type="submit" class="btn btn-brand btn-lg w-100">
            Ingresar al sistema
            <i class="bi bi-arrow-right ms-1"></i>
        </button>
    </form>

    @if (app()->environment('local'))
        <div class="demo-box mt-4">
            <div class="demo-title"><i class="bi bi-key-fill"></i>Credenciales de prueba</div>
            <div class="demo-row">
                <span>Administrador</span>
                <code>admin@taller.com / admin123</code>
            </div>
            <div class="demo-row">
                <span>Mecánico</span>
                <code>mecanico@taller.com / mecanico123</code>
            </div>
        </div>
    @endif
@endsection

@push('scripts')
    <script>
        document.getElementById('togglePassword')?.addEventListener('click', (event) => {
            const input = document.getElementById('password');
            const icon = event.currentTarget.querySelector('i');
            const visible = input.type === 'text';

            input.type = visible ? 'password' : 'text';
            icon.className = visible ? 'bi bi-eye' : 'bi bi-eye-slash';
            event.currentTarget.setAttribute('aria-label', visible ? 'Mostrar contraseña' : 'Ocultar contraseña');
        });
    </script>
@endpush
