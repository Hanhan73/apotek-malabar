@extends('layouts.auth')

@section('content')
<div class="login-container">
    <div class="login-card">
        <div class="login-header text-center mb-4">
            <h2>Apotek Malabar</h2>
            <p class="text-muted">Masuk ke Sistem</p>
        </div>

        <form method="POST" action="{{ route('login') }}">
            @csrf

            @if ($errors->any())
                <div class="alert alert-danger mb-4">
                    @foreach ($errors->all() as $error)
                        <p class="mb-0">{{ $error }}</p>
                    @endforeach
                </div>
            @endif

            <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                    <input id="email" type="email" class="form-control @error('email') is-invalid @enderror"
                           name="email" value="{{ old('email') }}" required autofocus>
                </div>
            </div>

            <div class="mb-4">
                <label for="password" class="form-label">Password</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-lock"></i></span>
                    <input id="password" type="password" class="form-control @error('password') is-invalid @enderror"
                           name="password" required>
                </div>
            </div>

            <div class="form-check mb-3">
                <input class="form-check-input" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
                <label class="form-check-label" for="remember">
                    Ingat Saya
                </label>
            </div>

            <button type="submit" class="btn btn-primary w-100 mb-3">
                <i class="bi bi-box-arrow-in-right me-2"></i> Masuk
            </button>
        </form>
    </div>
</div>
@endsection

@push('styles')
<style>
    body.auth-page {
        background: linear-gradient(120deg, #4e73df 0%, #224abe 100%);
        height: 100vh;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0;
        padding: 0;
    }
    
    .login-container {
        width: 100%;
        display: flex;
        justify-content: center;
        align-items: center;
        min-height: 100vh;
    }
    
    .login-card {
        background-color: #fff;
        border-radius: 0.5rem;
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
        padding: 2rem;
        width: 100%;
        max-width: 450px;
    }
    
    .login-header h2 {
        color: #4e73df;
        font-weight: 700;
    }
    
    .input-group-text {
        background-color: #f8f9fc;
        border-right: none;
    }
    
    .form-control {
        border-left: none;
    }
    
    .form-control:focus {
        box-shadow: none;
        border-color: #ced4da;
    }
</style>
@endpush