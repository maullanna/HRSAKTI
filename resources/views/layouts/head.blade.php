<!-- App favicon -->
<link rel="shortcut icon" href="{{ URL::asset('assets/images/favicon.ico') }}">
<meta name="viewport" content="width=device-width, initial-scale=1">
<?php echo $__env->yieldContent('css'); ?>

<!-- App css -->
<link href="{{ URL::asset('assets/css/bootstrap.min.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ URL::asset('assets/css/metismenu.min.css') }}" rel="stylesheet" type="text/css">
<link href="{{ URL::asset('assets/css/icons.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ URL::asset('assets/css/style.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ URL::asset('assets/css/custom.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ URL::asset('assets/css/sidebar.css') }}" rel="stylesheet" type="text/css" />

{{-- <link href="{{ URL::asset('plugins/sweet-alert2/sweetalert2.min.css') }}" rel="stylesheet" type="text/css"> --}}
<link href="{{ asset('plugins/sweetalert.min.css') }}" rel="stylesheet">
<!-- Table css -->
<link href="{{ URL::asset('plugins/RWD-Table-Patterns/dist/css/rwd-table.min.css') }}" rel="stylesheet" type="text/css" media="screen">
<!-- DataTables -->
<link href="{{ URL::asset('plugins/datatables/dataTables.bootstrap4.min.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ URL::asset('plugins/datatables/buttons.bootstrap4.min.css') }}" rel="stylesheet" type="text/css" />
<!-- Responsive datatable examples -->
<link href="{{ URL::asset('plugins/datatables/responsive.bootstrap4.min.css') }}" rel="stylesheet" type="text/css" />

<!-- Global Action Buttons Style -->
<style>
    /* Action Buttons - Consistent Bootstrap Style */
    .action-buttons {
        display: flex;
        gap: 4px;
        align-items: center;
        justify-content: center;
    }

    .action-buttons form {
        display: inline-block;
        margin: 0;
        padding: 0;
    }

    .action-buttons .btn,
    .btn-group .btn {
        margin: 0;
        padding: 6px 10px;
        border-radius: 4px;
        transition: all 0.2s ease;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        border: none;
        min-width: 32px;
        height: 32px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
    }

    .action-buttons .btn:hover,
    .btn-group .btn:hover {
        transform: translateY(-1px);
        box-shadow: 0 2px 6px rgba(0, 0, 0, 0.15);
    }

    .action-buttons .btn:active,
    .btn-group .btn:active {
        transform: translateY(0);
        box-shadow: 0 1px 2px rgba(0, 0, 0, 0.1);
    }

    .action-buttons .btn i,
    .btn-group .btn i {
        font-size: 16px;
        line-height: 1;
    }

    /* Bootstrap button colors with consistent styling */
    .action-buttons .btn-info,
    .btn-group .btn-info {
        background-color: #17a2b8;
        color: #fff;
    }

    .action-buttons .btn-info:hover,
    .btn-group .btn-info:hover {
        background-color: #138496;
        color: #fff;
    }

    .action-buttons .btn-success,
    .btn-group .btn-success {
        background-color: #28a745;
        color: #fff;
    }

    .action-buttons .btn-success:hover,
    .btn-group .btn-success:hover {
        background-color: #218838;
        color: #fff;
    }

    .action-buttons .btn-warning,
    .btn-group .btn-warning {
        background-color: #ffc107;
        color: #212529;
    }

    .action-buttons .btn-warning:hover,
    .btn-group .btn-warning:hover {
        background-color: #e0a800;
        color: #212529;
    }

    .action-buttons .btn-danger,
    .btn-group .btn-danger {
        background-color: #dc3545;
        color: #fff;
    }

    .action-buttons .btn-danger:hover,
    .btn-group .btn-danger:hover {
        background-color: #c82333;
        color: #fff;
    }

    /* Fix Select Box Placeholder Bug - Chrome */
    select.form-control,
    select.custom-select {
        height: calc(2.25rem + 2px) !important;
        min-height: calc(2.25rem + 2px) !important;
        padding: 0.375rem 2rem 0.375rem 0.75rem !important;
        padding-right: 2rem !important;
        line-height: 1.5 !important;
        font-size: 0.875rem !important;
        vertical-align: middle !important;
        appearance: none !important;
        -webkit-appearance: none !important;
        -moz-appearance: none !important;
        background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16'%3e%3cpath fill='none' stroke='%23343a40' stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M2 5l6 6 6-6'/%3e%3c/svg%3e") !important;
        background-repeat: no-repeat !important;
        background-position: right 0.75rem center !important;
        background-size: 16px 12px !important;
    }

    select.form-control:focus,
    select.custom-select:focus {
        height: calc(2.25rem + 2px) !important;
        min-height: calc(2.25rem + 2px) !important;
        padding: 0.375rem 2rem 0.375rem 0.75rem !important;
        padding-right: 2rem !important;
        line-height: 1.5 !important;
    }

    select.form-control option,
    select.custom-select option {
        padding: 0.5rem 0.75rem !important;
        line-height: 1.5 !important;
        min-height: 1.5rem !important;
        display: block !important;
    }

    /* Fix untuk semua select di modal dan card */
    .modal-body select.form-control,
    .modal-body select.custom-select,
    .card-body select.form-control,
    .card-body select.custom-select,
    .form-group select.form-control,
    .form-group select.custom-select {
        height: calc(2.25rem + 2px) !important;
        min-height: calc(2.25rem + 2px) !important;
        padding: 0.375rem 2rem 0.375rem 0.75rem !important;
        padding-right: 2rem !important;
        line-height: 1.5 !important;
        display: block !important;
        width: 100% !important;
    }

    /* Fix untuk select2 jika digunakan */
    .select2-container .select2-selection--single {
        height: calc(2.25rem + 2px) !important;
    }

    .select2-container .select2-selection--single .select2-selection__rendered {
        line-height: calc(2.25rem + 2px) !important;
        padding-left: 0.75rem !important;
        padding-right: 2rem !important;
    }

    /* Pastikan placeholder text tidak terpotong */
    select.form-control option[disabled],
    select.custom-select option[disabled] {
        color: #6c757d !important;
        font-style: normal !important;
        padding: 0.5rem 0.75rem !important;
    }
</style>

<!-- Custom CSS for Login Page -->
<?php
$loginBgImage = asset('assets/images/gallery/05-nature-backgrounds_1491895829.jpg');
?>
<style>
    body.login-page {
        background: rgb(49, 49, 51) !important;
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
        background-image: linear-gradient(135deg, rgba(30, 30, 30, 0.8) 0%, rgba(60, 60, 60, 0.8) 30%, rgba(108, 117, 125, 0.8) 60%, rgba(173, 181, 189, 0.8) 85%, rgba(45, 45, 48, 0.8) 100%),
        url('{{ $loginBgImage }}');
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
        box-sizing: border-box;
        overflow: hidden;
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
        box-sizing: border-box;
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
        min-width: 120px;
        min-height: 120px;
        max-width: 120px;
        max-height: 120px;
        border-radius: 50%;
        margin-bottom: 1.5rem;
        object-fit: cover;
        object-position: center;
        aspect-ratio: 1 / 1;
        border: 4px solid rgba(255, 255, 255, 0.3);
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.2);
        animation: float 3s ease-in-out infinite;
        display: block;
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

        0%,
        100% {
            transform: translateY(0px);
        }

        50% {
            transform: translateY(-10px);
        }
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

    /* Responsive Design - Tablet (iPad) */
    @media (max-width: 1024px) and (min-width: 769px) {
        .login-wrapper {
            max-width: 90%;
            margin: 1.5rem;
            min-height: 550px;
        }

        .login-form {
            padding: 2.5rem;
        }

        .login-form h2 {
            font-size: 1.75rem;
        }

        .slide h1 {
            font-size: 2.25rem;
        }

        .slide .quote {
            font-size: 1.05rem;
            max-width: 280px;
        }

        .slide .image {
            width: 110px;
            height: 110px;
            min-width: 110px;
            min-height: 110px;
            max-width: 110px;
            max-height: 110px;
            aspect-ratio: 1 / 1;
            object-fit: cover;
            object-position: center;
        }
    }

    /* Responsive Design - Mobile & Small Tablets */
    @media (max-width: 768px) {
        .login-container {
            padding: 0.75rem;
            min-height: 100vh;
            align-items: flex-start;
            padding-top: 1rem;
        }

        .login-wrapper {
            grid-template-columns: 1fr;
            margin: 0;
            min-height: auto;
            max-width: 100%;
            border-radius: 10px;
        }

        .login-image {
            min-height: 200px;
            max-height: 250px;
            order: 1;
            padding-top: 1.5rem;
        }

        .slideshow-container {
            padding-top: 1rem;
        }

        .login-form {
            padding: 2rem 1.5rem;
            order: 2;
        }

        .login-form h2 {
            font-size: 1.5rem;
            margin-bottom: 0.25rem;
        }

        .login-form .subtitle {
            font-size: 0.9rem;
            margin-bottom: 1.5rem;
        }

        .slide {
            padding: 1.5rem;
        }

        .slide h1 {
            font-size: 1.75rem;
            margin-bottom: 0.75rem;
        }

        .slide .quote {
            font-size: 0.9rem;
            max-width: 90%;
            margin-bottom: 0.75rem;
            line-height: 1.3;
        }

        .slide .author {
            font-size: 0.8rem;
        }

        .slide .system-info {
            font-size: 0.85rem;
            margin-top: 0.75rem;
        }

        .slide .icon {
            font-size: 2.5rem;
            margin-bottom: 1rem;
        }

        .slide .image {
            width: 80px;
            height: 80px;
            min-width: 80px;
            min-height: 80px;
            max-width: 80px;
            max-height: 80px;
            aspect-ratio: 1 / 1;
            object-fit: cover;
            object-position: center;
            margin-bottom: 1rem;
        }

        .slideshow-dots {
            bottom: 0.75rem;
        }

        .dot {
            width: 7px;
            height: 7px;
        }

        .form-group {
            margin-bottom: 1.25rem;
        }

        .form-control {
            padding: 14px 15px;
            font-size: 16px;
            /* Prevents zoom on iOS */
        }

        .btn-login {
            padding: 14px 30px;
            font-size: 1rem;
        }

        .form-check-label {
            font-size: 0.85rem;
        }
    }

    /* Responsive Design - Small Mobile Phones */
    @media (max-width: 480px) {
        .login-container {
            padding: 0.5rem;
            padding-top: 0.75rem;
        }

        .login-wrapper {
            margin: 0;
            border-radius: 8px;
        }

        .login-image {
            min-height: 180px;
            max-height: 220px;
            padding-top: 1.25rem;
        }

        .slideshow-container {
            padding-top: 0.75rem;
        }

        .login-form {
            padding: 1.5rem 1.25rem;
        }

        .login-form h2 {
            font-size: 1.35rem;
        }

        .login-form .subtitle {
            font-size: 0.85rem;
            margin-bottom: 1.25rem;
        }

        .slide {
            padding: 1rem;
        }

        .slide h1 {
            font-size: 1.5rem;
            margin-bottom: 0.5rem;
        }

        .slide .quote {
            font-size: 0.85rem;
            max-width: 95%;
            margin-bottom: 0.5rem;
        }

        .slide .author {
            font-size: 0.75rem;
        }

        .slide .system-info {
            font-size: 0.8rem;
            margin-top: 0.5rem;
        }

        .slide .image {
            width: 70px;
            height: 70px;
            min-width: 70px;
            min-height: 70px;
            max-width: 70px;
            max-height: 70px;
            aspect-ratio: 1 / 1;
            object-fit: cover;
            object-position: center;
            margin-bottom: 0.75rem;
        }

        .slide .icon {
            font-size: 2rem;
            margin-bottom: 0.75rem;
        }

        .form-group {
            margin-bottom: 1rem;
        }

        .form-control {
            padding: 12px 14px;
        }

        .btn-login {
            padding: 12px 25px;
        }

        .form-check {
            margin-bottom: 1.25rem;
        }
    }

    /* Responsive Design - Extra Small Mobile Phones */
    @media (max-width: 360px) {
        .login-image {
            padding-top: 1rem;
        }

        .slideshow-container {
            padding-top: 0.5rem;
        }

        .login-form {
            padding: 1.25rem 1rem;
        }

        .login-form h2 {
            font-size: 1.2rem;
        }

        .slide h1 {
            font-size: 1.35rem;
        }

        .slide .quote {
            font-size: 0.8rem;
        }

        .slide .image {
            width: 60px;
            height: 60px;
            min-width: 60px;
            min-height: 60px;
            max-width: 60px;
            max-height: 60px;
            aspect-ratio: 1 / 1;
            object-fit: cover;
            object-position: center;
        }

        .form-control {
            padding: 11px 12px;
            font-size: 16px;
        }
    }

    /* Landscape Mobile Orientation */
    @media (max-width: 768px) and (orientation: landscape) {
        .login-image {
            min-height: 150px;
            max-height: 180px;
            padding-top: 1rem;
        }

        .slideshow-container {
            padding-top: 0.5rem;
        }

        .login-form {
            padding: 1.5rem;
        }

        .slide {
            padding: 1rem;
        }

        .slide h1 {
            font-size: 1.5rem;
        }

        .slide .quote {
            font-size: 0.85rem;
        }

        .slide .image {
            width: 70px;
            height: 70px;
            min-width: 70px;
            min-height: 70px;
            max-width: 70px;
            max-height: 70px;
            aspect-ratio: 1 / 1;
            object-fit: cover;
            object-position: center;
        }
    }
</style>