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
                    <input id="password" type="password" class="form-control @error('password') is-invalid @enderror"
                        name="password" required autocomplete="current-password" 
                        placeholder="Enter your password or PIN code">
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
        </div>
    </div>
</div>
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
</script>
@endsection


