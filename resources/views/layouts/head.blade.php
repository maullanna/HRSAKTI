<!-- App favicon -->
<link rel="shortcut icon" href="{{ URL::asset('assets/images/') }}">
<meta name="viewport" content="width=device-width, initial-scale=1">      
@yield('css')

 <!-- App css -->
<link href="{{ URL::asset('assets/css/bootstrap.min.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ URL::asset('assets/css/metismenu.min.css') }}" rel="stylesheet" type="text/css">
<link href="{{ URL::asset('assets/css/icons.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ URL::asset('assets/css/style.css') }}" rel="stylesheet" type="text/css" />

{{-- <link href="{{ URL::asset('plugins/sweet-alert2/sweetalert2.min.css') }}" rel="stylesheet" type="text/css"> --}}
<link href="{{ asset('plugins/sweetalert.min.css') }}" rel="stylesheet">
<!-- Table css -->
<link href="{{ URL::asset('plugins/RWD-Table-Patterns/dist/css/rwd-table.min.css') }}" rel="stylesheet" type="text/css" media="screen">
<!-- DataTables -->
<link href="{{ URL::asset('plugins/datatables/dataTables.bootstrap4.min.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ URL::asset('plugins/datatables/buttons.bootstrap4.min.css') }}" rel="stylesheet" type="text/css" />
<!-- Responsive datatable examples -->
<link href="{{ URL::asset('plugins/datatables/responsive.bootstrap4.min.css') }}" rel="stylesheet" type="text/css" />

<!-- Custom CSS for Login Page -->
<style>
    body.login-page {
        background:rgb(49, 49, 51) !important;
        margin: 0;
        padding: 0;
        font-family: 'Roboto', sans-serif;
    }
    
    .login-container {
        min-height: 100vh;
        display: flex;
        align-items: center;
        justify-content: center;
        background: #f8f9fa;
    }
    
    .login-wrapper {
        display: grid;
        grid-template-columns: 1fr 1fr;
        max-width: 1200px;
        width: 100%;
        min-height: 600px;
        background: white;
        border-radius: 15px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        overflow: hidden;
    }
    
    .login-image {
        background-image: linear-gradient(135deg, rgba(30, 30, 30, 0.8) 0%, rgba(60, 60, 60, 0.8) 30%, rgba(108, 117, 125, 0.8) 60%, rgba(173, 181, 189, 0.8) 85%, rgba(45, 45, 48, 0.8) 100%), url('{{ asset('assets/images/gallery/05-nature-backgrounds_1491895829.jpg') }}');
        background-size: cover;
        background-position: center;
        background-repeat: no-repeat;
        display: flex;
        align-items: center;
        justify-content: center;
        position: relative;
        overflow: hidden;
    }
    
    .login-image::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="grain" width="100" height="100" patternUnits="userSpaceOnUse"><circle cx="25" cy="25" r="1" fill="white" opacity="0.15"/><circle cx="75" cy="75" r="1" fill="white" opacity="0.15"/><circle cx="50" cy="10" r="0.5" fill="white" opacity="0.15"/><circle cx="10" cy="60" r="0.5" fill="white" opacity="0.15"/><circle cx="90" cy="40" r="0.5" fill="white" opacity="0.15"/></pattern></defs><rect width="100" height="100" fill="url(%23grain)"/></svg>');
        opacity: 0.4;
    }
    
    .login-image::after {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(0, 0, 0, 0.3);
        z-index: 1;
    }
    
    .slideshow-container {
        position: relative;
        width: 100%;
        height: 100%;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    
    .slide {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        text-align: center;
        color: white;
        padding: 2rem;
        opacity: 0;
        transform: translateX(50px);
        transition: all 0.8s ease-in-out;
        z-index: 3;
    }
    
    .slide.active {
        opacity: 1;
        transform: translateX(0);
    }
    
    .slide .icon {
        font-size: 4rem;
        margin-bottom: 1.5rem;
        opacity: 0.9;
        animation: float 3s ease-in-out infinite;
    }
    
    .slide .image {
        width: 120px;
        height: 120px;
        border-radius: 50%;
        margin-bottom: 1.5rem;
        object-fit: cover;
        border: 4px solid rgba(255, 255, 255, 0.3);
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.2);
        animation: float 3s ease-in-out infinite;
    }
    
    .slide h1 {
        font-size: 2.5rem;
        font-weight: 700;
        margin-bottom: 1rem;
        text-shadow: 0 3px 6px rgba(0, 0, 0, 0.5);
        line-height: 1.2;
    }
    
    .slide .quote {
        font-size: 1.1rem;
        font-style: italic;
        opacity: 0.95;
        margin-bottom: 1rem;
        line-height: 1.4;
        max-width: 300px;
        text-shadow: 0 2px 4px rgba(0, 0, 0, 0.4);
    }
    
    .slide .author {
        font-size: 0.9rem;
        opacity: 0.8;
        font-weight: 500;
        text-shadow: 0 2px 4px rgba(0, 0, 0, 0.4);
    }
    
    .slide .system-info {
        font-size: 1rem;
        opacity: 0.9;
        margin-top: 1rem;
        font-weight: 500;
        text-shadow: 0 2px 4px rgba(0, 0, 0, 0.4);
    }
    
    @keyframes float {
        0%, 100% { transform: translateY(0px); }
        50% { transform: translateY(-10px); }
    }
    
    .slideshow-dots {
        position: absolute;
        bottom: 2rem;
        left: 50%;
        transform: translateX(-50%);
        display: flex;
        gap: 0.5rem;
        z-index: 4;
    }
    
    .dot {
        width: 8px;
        height: 8px;
        border-radius: 50%;
        background: rgba(255, 255, 255, 0.5);
        cursor: pointer;
        transition: all 0.3s ease;
    }
    
    .dot.active {
        background: white;
        transform: scale(1.2);
    }
    
    .login-form {
        padding: 3rem;
        display: flex;
        flex-direction: column;
        justify-content: center;
    }
    
    .login-form h2 {
        color: #333;
        font-size: 2rem;
        font-weight: 600;
        margin-bottom: 0.5rem;
        text-align: center;
    }
    
    .login-form .subtitle {
        color: #666;
        text-align: center;
        margin-bottom: 2rem;
    }
    
    .form-group {
        margin-bottom: 1.5rem;
    }
    
    .form-group label {
        color: #333;
        font-weight: 500;
        margin-bottom: 0.5rem;
        display: block;
    }
    
    .form-control {
        border: 2px solid #e9ecef;
        border-radius: 8px;
        padding: 12px 15px;
        font-size: 1rem;
        transition: all 0.3s ease;
        width: 100%;
    }
    
    .form-control:focus {
        border-color: #3c3c3c;
        box-shadow: 0 0 0 0.2rem rgba(60, 60, 60, 0.25);
        outline: none;
    }
    
    .btn-login {
        background: linear-gradient(135deg, #3c3c3c 0%, #6c757d 100%);
        border: none;
        border-radius: 8px;
        padding: 12px 30px;
        color: white;
        font-weight: 600;
        font-size: 1rem;
        transition: all 0.3s ease;
        cursor: pointer;
        width: 100%;
    }
    
    .btn-login:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(60, 60, 60, 0.4);
    }
    
    .form-check {
        display: flex;
        align-items: center;
        margin-bottom: 1.5rem;
    }
    
    .form-check-input {
        margin-right: 0.5rem;
        transform: scale(1.1);
    }
    
    .form-check-label {
        color: #666;
        font-size: 0.9rem;
    }
    
    /* Responsive Design */
    @media (max-width: 768px) {
        .login-wrapper {
            grid-template-columns: 1fr;
            margin: 1rem;
            min-height: auto;
        }
        
        .login-image {
            min-height: 250px;
        }
        
        .slide h1 {
            font-size: 2rem;
        }
        
        .slide .quote {
            font-size: 1rem;
            max-width: 250px;
        }
        
        .slide .icon {
            font-size: 3rem;
        }
        
        .slide .image {
            width: 100px;
            height: 100px;
        }
        
        .slideshow-dots {
            bottom: 1rem;
        }
        
        .login-form {
            padding: 2rem;
        }
    }
    
    @media (max-width: 480px) {
        .login-container {
            padding: 0.5rem;
        }
        
        .login-wrapper {
            margin: 0;
            border-radius: 0;
        }
        
        .login-form {
            padding: 1.5rem;
        }
    }
</style>