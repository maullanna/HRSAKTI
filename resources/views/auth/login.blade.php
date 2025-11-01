@extends('layouts.master-blank')

@section('content')
<div class="login-container">
    <div class="login-wrapper">
        <!-- Left Side - Slideshow -->
        <div class="login-image">

            <div class="slideshow-container">
                <!-- Slide 1 - Office Team -->
                <div class="slide active">
                    <img src="https://images.unsplash.com/photo-1522071820081-009f0129c71c?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=1000&q=80" alt="Office Team" class="image">
                    <h1>Teamwork</h1>
                    <div class="quote">"Alone we can do so little; together we can do so much."</div>
                    <div class="author">- Helen Keller</div>
                    <div class="system-info">Build stronger teams together</div>
                </div>
                
                <!-- Slide 2 - Success -->
                <div class="slide">
                    <img src="https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=1000&q=80" alt="Success" class="image">
                    <h1>Success</h1>
                    <div class="quote">"Success is not final, failure is not fatal: it is the courage to continue that counts."</div>
                    <div class="author">- Winston Churchill</div>
                    <div class="system-info">Every day is a new opportunity</div>
                </div>
                
                <!-- Slide 3 - Productivity -->
                <div class="slide">
                    <img src="https://images.unsplash.com/photo-1551434678-e076c223a692?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=1000&q=80" alt="Productivity" class="image">
                    <h1>Productivity</h1>
                    <div class="quote">"The way to get started is to quit talking and begin doing."</div>
                    <div class="author">- Walt Disney</div>
                    <div class="system-info">Track your work progress</div>
                </div>
            </div>
            
            <!-- Dots Navigation -->
            <div class="slideshow-dots">
                <span class="dot active" onclick="currentSlide(1)"></span>
                <span class="dot" onclick="currentSlide(2)"></span>
                <span class="dot" onclick="currentSlide(3)"></span>
            </div>
        </div>
        
        <!-- Right Side - Login Form -->
        <div class="login-form">
            <h2>Welcome Back!</h2>
            <p class="subtitle">Sign in to your account</p>
            
            <form method="POST" action="{{ route('login') }}">
                @csrf
                
                <div class="form-group">
                    <label for="email">{{ __('Email Address') }}</label>
                    <input id="email" type="email" class="form-control @error('email') is-invalid @enderror"
                        name="email" value="{{ old('email') }}" required autocomplete="email" autofocus
                        placeholder="Enter your email address">
                    @error('email')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="password">{{ __('Password / PIN Code') }}</label>
                    <div class="input-group">
                        <input id="password" type="password" class="form-control @error('password') is-invalid @enderror"
                            name="password" required autocomplete="current-password" 
                            placeholder="Enter your password or PIN code">
                        <div class="input-group-append">
                            <button class="btn btn-outline-secondary" type="button" id="togglePassword">
                                <i class="mdi mdi-eye" id="togglePasswordIcon"></i>
                            </button>
                        </div>
                    </div>
                    @error('password')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>
                
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="remember" id="remember"
                        {{ old('remember') ? 'checked' : '' }}>
                    <label class="form-check-label" for="remember">
                        {{ __('Remember Me') }}
                    </label>
                </div>
                
                <button type="submit" class="btn-login">
                    {{ __('Log In') }}
                </button>
            </form>
            
            <!-- Arrow pointing to Login button -->
            <div class="login-arrow">
                <div class="arrow-container">
                    <i class="mdi mdi-arrow-right"></i>
                    <span>Click here to login</span>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('style')
<style>
.input-group-append .btn {
    border-left: 0;
    border-color: #ced4da;
    background-color: #f8f9fa;
    color: #6c757d;
    transition: all 0.3s ease;
}

.input-group-append .btn:hover {
    background-color: #e9ecef;
    color: #495057;
    border-color: #ced4da;
}

.input-group-append .btn:focus {
    box-shadow: none;
    border-color: #ced4da;
}

.input-group .form-control:focus {
    border-color: #ced4da;
    box-shadow: none;
}

.input-group .form-control:focus + .input-group-append .btn {
    border-color: #ced4da;
}

/* Login Form Positioning */
.login-form {
    position: relative;
}

/* Login Arrow Styling */
.login-arrow {
    position: absolute;
    right: -80px;
    top: 50%;
    transform: translateY(-50%);
    z-index: 10;
    animation: bounceArrow 2s infinite;
}

.arrow-container {
    display: flex;
    flex-direction: column;
    align-items: center;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 15px 20px;
    border-radius: 25px;
    box-shadow: 0 8px 25px rgba(102, 126, 234, 0.3);
    position: relative;
    min-width: 120px;
}

.arrow-container::before {
    content: '';
    position: absolute;
    left: -10px;
    top: 50%;
    transform: translateY(-50%);
    width: 0;
    height: 0;
    border-top: 10px solid transparent;
    border-bottom: 10px solid transparent;
    border-right: 10px solid #667eea;
}

.arrow-container i {
    font-size: 24px;
    margin-bottom: 5px;
    animation: pulse 1.5s infinite;
}

.arrow-container span {
    font-size: 12px;
    font-weight: 600;
    text-align: center;
    line-height: 1.2;
}

@keyframes bounceArrow {
    0%, 20%, 50%, 80%, 100% {
        transform: translateY(-50%);
    }
    40% {
        transform: translateY(-60%);
    }
    60% {
        transform: translateY(-55%);
    }
}

@keyframes pulse {
    0% {
        transform: scale(1);
    }
    50% {
        transform: scale(1.1);
    }
    100% {
        transform: scale(1);
    }
}

/* Responsive adjustments */
@media (max-width: 1200px) {
    .login-arrow {
        right: -60px;
    }
    
    .arrow-container {
        padding: 12px 16px;
        min-width: 100px;
    }
    
    .arrow-container i {
        font-size: 20px;
    }
    
    .arrow-container span {
        font-size: 11px;
    }
}

@media (max-width: 768px) {
    .login-arrow {
        display: none;
    }
}
</style>
@endsection

@section('script')
<script>
let slideIndex = 1;
let slideInterval;

// Initialize slideshow
document.addEventListener('DOMContentLoaded', function() {
    showSlide(slideIndex);
    startSlideshow();
});

// Function to show specific slide
function currentSlide(n) {
    showSlide(slideIndex = n);
    resetSlideshow();
}

// Function to show next slide
function nextSlide() {
    slideIndex++;
    if (slideIndex > 3) {
        slideIndex = 1;
    }
    showSlide(slideIndex);
}

// Function to display slides
function showSlide(n) {
    let slides = document.getElementsByClassName("slide");
    let dots = document.getElementsByClassName("dot");
    
    if (n > slides.length) {
        slideIndex = 1;
    }
    if (n < 1) {
        slideIndex = slides.length;
    }
    
    // Hide all slides
    for (let i = 0; i < slides.length; i++) {
        slides[i].classList.remove("active");
    }
    
    // Remove active class from all dots
    for (let i = 0; i < dots.length; i++) {
        dots[i].classList.remove("active");
    }
    
    // Show current slide and activate corresponding dot
    slides[slideIndex - 1].classList.add("active");
    dots[slideIndex - 1].classList.add("active");
}

// Start automatic slideshow
function startSlideshow() {
    slideInterval = setInterval(nextSlide, 6000); // Change slide every 6 seconds
}

// Reset slideshow timer
function resetSlideshow() {
    clearInterval(slideInterval);
    startSlideshow();
}

// Pause slideshow on hover
document.querySelector('.login-image').addEventListener('mouseenter', function() {
    clearInterval(slideInterval);
});

// Resume slideshow when mouse leaves
document.querySelector('.login-image').addEventListener('mouseleave', function() {
    startSlideshow();
});

// Toggle password visibility
document.getElementById('togglePassword').addEventListener('click', function() {
    const passwordInput = document.getElementById('password');
    const toggleIcon = document.getElementById('togglePasswordIcon');
    
    if (passwordInput.type === 'password') {
        passwordInput.type = 'text';
        toggleIcon.classList.remove('mdi-eye');
        toggleIcon.classList.add('mdi-eye-off');
    } else {
        passwordInput.type = 'password';
        toggleIcon.classList.remove('mdi-eye-off');
        toggleIcon.classList.add('mdi-eye');
    }
});
</script>
@endsection


