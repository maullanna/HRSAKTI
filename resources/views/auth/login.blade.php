@extends('layouts.master-blank')

@section('content')
<div class="login-container">
    <!-- Language Selector for Mobile -->
    <div class="mobile-language-selector d-md-none">
        @php
        $currentLocale = session('locale', 'en');
        $currentLanguage = $currentLocale === 'id' ? 'Indonesian' : 'English';
        $indonesiaFlagPath = public_path('assets/images/flags/indonesia_flag.jpg');
        $indonesiaFlag = file_exists($indonesiaFlagPath)
        ? asset('assets/images/flags/indonesia_flag.jpg') . '?v=' . filemtime($indonesiaFlagPath)
        : asset('assets/images/flags/us_flag.jpg');
        $fallbackFlag = asset('assets/images/flags/us_flag.jpg');
        @endphp
        <div class="language-dropdown-mobile">
            <select class="form-control language-select" id="mobileLanguageSelect" data-base-url="{{ url('/language') }}">
                <option value="en" {{ $currentLocale === 'en' ? 'selected' : '' }}>
                    English
                </option>
                <option value="id" {{ $currentLocale === 'id' ? 'selected' : '' }}>
                    Indonesian
                </option>
            </select>
        </div>
    </div>

    <div class="login-wrapper">
        <!-- Left Side - Slideshow -->
        <div class="login-image">

            <div class="slideshow-container">
                <!-- Slide 1 - Akio Toyoda -->
                <div class="slide active">
                    <img src="{{ URL::asset('assets/images/Akio-Toyoda-GR-Photo_1600.png') }}" alt="Akio Toyoda" class="image">
                    <h1>Keep Challenging</h1>
                    <div class="quote">"The most important thing is to keep challenging ourselves and never stop improving."</div>
                    <div class="author">- Akio Toyoda</div>
                    <div class="system-info">Embrace continuous improvement</div>
                </div>

                <!-- Slide 2 - Jack Ma -->
                <div class="slide">
                    <img src="{{ URL::asset('assets/images/jack ma.png') }}" alt="Jack Ma" class="image">
                    <h1>Never Give Up</h1>
                    <div class="quote">"Never give up. Today is hard, tomorrow will be worse, but the day after tomorrow will be sunshine."</div>
                    <div class="author">- Jack Ma</div>
                    <div class="system-info">Stay persistent and keep moving forward</div>
                </div>

                <!-- Slide 3 - Einstein -->
                <div class="slide">
                    <img src="{{ URL::asset('assets/images/ainsten.png') }}" alt="ainsten" class="image">
                    <h1>Value Over Success</h1>
                    <div class="quote">"Try not to become a person of success, but rather try to become a person of value."</div>
                    <div class="author">- Albert Einstein</div>
                    <div class="system-info">Focus on creating value in your work</div>
                </div>
            </div>

            <!-- Dots Navigation -->
            <div class="slideshow-dots">
                <span class="dot active" onclick="currentSlide(1)"></span>
                <span class="dot" onclick="currentSlide(2)"></span>
                <span class="dot" onclick="currentSlide(3)"></span>
            </div>
        </div>

        <div class="login-form">
            <img src="{{ URL::asset('storage/logos/ams_logo_1762390189.png') }}" alt="ainsten" class="image">
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

                <button type="submit" class="btn-login" id="loginButton">
                    {{ __('Log In') }}
                </button>
            </form>
        </div>
    </div>
</div>
@endsection

@section('style')
<style>
    /* Mobile Language Selector - Hidden di login page (default English) */
    .mobile-language-selector {
        display: none !important;
    }

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

    .input-group .form-control:focus+.input-group-append .btn {
        border-color: #ced4da;
    }

    .login-form .image {
        width: 100px;
        height: auto;
        max-width: 140px;
        display: block;
        margin: 0 auto 1.5rem auto;
    }

    /* Responsive adjustments */
    @media (max-width: 768px) {
        .login-container {
            padding-top: 20px !important;
        }

        .login-image {
            min-height: 250px !important;
            max-height: 320px !important;
        }

        .slide .image {
            width: 80px !important;
            height: 80px !important;
            min-width: 80px !important;
            min-height: 80px !important;
            max-width: 80px !important;
            max-height: 80px !important;
        }

        .slide h1 {
            font-size: 1.5rem !important;
            margin-bottom: 0.5rem !important;
        }

        .slide .quote {
            font-size: 0.5rem !important;
            margin-bottom: 0.5rem !important;
        }

        .slide .author {
            font-size: 0.4rem !important;
        }

        .slide .system-info {
            font-size: 0.85rem !important;
        }

        .input-group-append .btn {
            padding: 14px 12px;
            min-width: 48px;
            /* Better touch target */
        }

        .input-group .form-control {
            padding-right: 50px;
        }

        .login-form .image {
            width: 70px;
            max-width: 80px;
            margin-bottom: 0.75rem;
        }
    }

    @media (max-width: 480px) {
        .login-container {
            padding-top: 15px !important;
        }

        .login-image {
            min-height: 200px !important;
            max-height: 260px !important;
        }

        .slide .image {
            width: 70px !important;
            height: 70px !important;
            min-width: 70px !important;
            min-height: 70px !important;
            max-width: 70px !important;
            max-height: 70px !important;
        }

        .slide h1 {
            font-size: 1.3rem !important;
            margin-bottom: 0.4rem !important;
        }

        .slide .quote {
            font-size: 0.6rem !important;
            margin-bottom: 0.4rem !important;
        }

        .slide .author {
            font-size: 0.7rem !important;
        }

        .slide .system-info {
            font-size: 0.6rem !important;
        }

        .input-group-append .btn {
            padding: 12px 10px;
            min-width: 44px;
        }

        .input-group .form-control {
            padding-right: 48px;
        }

        .login-form .image {
            width: 60px;
            max-width: 70px;
            margin-bottom: 0.5rem;
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

        if (slideInterval) {
            clearInterval(slideInterval);
        }
        startSlideshow();

        // Mobile language selector
        const mobileLanguageSelect = document.getElementById('mobileLanguageSelect');
        if (mobileLanguageSelect) {
            const languageBaseUrl = mobileLanguageSelect.getAttribute('data-base-url');
            mobileLanguageSelect.addEventListener('change', function() {
                const selectedLanguage = this.value;
                window.location.href = languageBaseUrl + '/' + selectedLanguage;
            });
        }
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
        if (slideInterval) {
            clearInterval(slideInterval);
        }
        slideInterval = setInterval(nextSlide, 6000);
    }

    // Reset slideshow timer
    function resetSlideshow() {
        if (slideInterval) {
            clearInterval(slideInterval);
        }
        slideInterval = setInterval(nextSlide, 6000);
    }

    // Pause slideshow on hover
    const loginImage = document.querySelector('.login-image');
    if (loginImage) {
        loginImage.addEventListener('mouseenter', function() {
            if (slideInterval) {
                clearInterval(slideInterval);
            }
        });

        loginImage.addEventListener('mouseleave', function() {
            startSlideshow();
        });
    }

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

    // Handle rate limiting - disable/hide login button
    function handleRateLimit() {
        const loginButton = document.getElementById('loginButton');
        const emailError = document.querySelector('.invalid-feedback strong');
        const emailInput = document.getElementById('email');
        const passwordInput = document.getElementById('password');
        const loginForm = document.querySelector('form[action*="login"]');

        // Cek apakah ada error rate limit dari session atau error message
        const hasRateLimitError = emailError && (
            emailError.textContent.includes('Terlalu banyak percobaan') ||
            emailError.textContent.includes('Too many login attempts')
        );

        if (hasRateLimitError) {
            // Disable dan hide button login
            if (loginButton) {
                loginButton.disabled = true;
                loginButton.style.display = 'none';
                loginButton.style.pointerEvents = 'none';
                loginButton.style.opacity = '0.5';
                loginButton.style.cursor = 'not-allowed';
            }

            // Disable form inputs
            if (emailInput) {
                emailInput.disabled = true;
                emailInput.style.cursor = 'not-allowed';
                emailInput.style.opacity = '0.6';
            }
            if (passwordInput) {
                passwordInput.disabled = true;
                passwordInput.style.cursor = 'not-allowed';
                passwordInput.style.opacity = '0.6';
            }

            // Prevent form submission
            if (loginForm) {
                loginForm.addEventListener('submit', function(e) {
                    e.preventDefault();
                    return false;
                });
            }
        }
    }

    // Run on page load
    document.addEventListener('DOMContentLoaded', handleRateLimit);

    // Run after a short delay to ensure error messages are rendered
    setTimeout(handleRateLimit, 100);
</script>
@endsection