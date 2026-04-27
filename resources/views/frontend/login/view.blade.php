@extends('frontend.layouts.master_login')
@section('titel', $login_type == 1 ? 'Teacher Login' : 'Student Login')

@section('content')
<div class="container-fluid d-flex align-items-center min-vh-100 p-0">
    <div class="login-card animate-entrance">
        <div class="border-animation"></div>
        <div class="text-center">
            <img src="{{ url('assets/oxford/img/logo.png') }}" alt="Oxford Logo" class="login-logo">
            <h2 class="mb-4 fw-bold" style="letter-spacing: 1px;">{{ $login_type == 1 ? 'Teacher Gate' : 'Student Gate' }}</h2>
        </div>

        <form action="{{route('web.login')}}" method="post">
            @csrf
            
            @if(session('danger'))
                <div class="alert alert-danger border-0 bg-danger bg-opacity-25 text-white small mb-4" role="alert">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i> {{ session('danger') }}
                </div>
            @endif

            @if(session('success'))
                <div class="alert alert-success border-0 bg-success bg-opacity-25 text-white small mb-4" role="alert">
                    <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
                </div>
            @endif

            <!-- Username Field -->
            <div class="form-floating mb-3">
                <input type="text" name="username" class="form-control @error('username') is-invalid @enderror" id="usernameInput" placeholder="Username" value="{{ old('username') }}" autocomplete="off" required>
                <label for="usernameInput"><i class="bi bi-person me-2"></i>Username</label>
                @error('username')
                    <div class="invalid-feedback text-danger small mt-1">{{ $message }}</div>
                @enderror
            </div>

            <!-- Password Field -->
            <div class="form-floating mb-4 position-relative">
                <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" id="passwordInput" placeholder="Password" required>
                <label for="passwordInput"><i class="bi bi-lock me-2"></i>Password</label>
                <span class="password-toggle" id="togglePassword">
                    <i class="bi bi-eye"></i>
                </span>
                @error('password')
                    <div class="invalid-feedback text-danger small mt-1">{{ $message }}</div>
                @enderror
            </div>

            <input type="hidden" name="login_type" value="{{$login_type}}" autocomplete="off">
            
            <button type="submit" class="btn w-100 login-btn">
                Login <i class="bi bi-arrow-right-short ms-1"></i>
            </button>

            <div class="login-links d-flex justify-content-between mt-4">
                <a href="#"><i class="bi bi-question-circle me-1"></i> Forgot?</a>
                <a href="{{route('contact.book')}}">Create Account <i class="bi bi-plus-lg ms-1"></i></a>
            </div>
        </form>
    </div>
</div>
@endsection

@section('js')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Particles.js Initialization
        if (typeof particlesJS !== 'undefined') {
            particlesJS('particles-js', {
                "particles": {
                    "number": { "value": 70, "density": { "enable": true, "value_area": 1000 } },
                    "color": { "value": "#ffffff" },
                    "shape": { "type": "star" },
                    "opacity": { "value": 0.5, "random": true },
                    "size": { "value": 5, "random": true },
                    "line_linked": { "enable": true, "distance": 150, "color": "#ffffff", "opacity": 0.2, "width": 1 },
                    "move": { "enable": true, "speed": 1 }
                },
                "interactivity": {
                    "detect_on": "canvas",
                    "events": { "onhover": { "enable": true, "mode": "bubble" }, "onclick": { "enable": true, "mode": "repulse" }, "resize": true },
                    "modes": { "bubble": { "distance": 200, "size": 8, "duration": 2, "opacity": 0.9, "speed": 3 } }
                },
                "retina_detect": true
            });
        }

        // Password Toggle
        const togglePassword = document.querySelector('#togglePassword');
        const password = document.querySelector('#passwordInput');
        if (togglePassword && password) {
            togglePassword.addEventListener('click', function() {
                const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
                password.setAttribute('type', type);
                this.querySelector('i').classList.toggle('bi-eye');
                this.querySelector('i').classList.toggle('bi-eye-slash');
            });
        }
    });
</script>
@endsection
