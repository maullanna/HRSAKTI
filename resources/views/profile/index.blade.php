@extends('layouts.master')

@section('css')
<style>
    .profile-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        padding: 30px;
        border-radius: 10px;
        color: white;
        margin-bottom: 30px;
    }

    .profile-photo-container {
        text-align: center;
        margin-bottom: 20px;
    }

    .profile-photo {
        width: 150px;
        height: 150px;
        border-radius: 50%;
        object-fit: cover;
        border: 5px solid white;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
        margin-bottom: 15px;
    }

    .profile-upload-btn {
        position: relative;
        display: inline-block;
    }

    .profile-upload-btn input[type="file"] {
        position: absolute;
        opacity: 0;
        width: 100%;
        height: 100%;
        cursor: pointer;
    }

    .card {
        border-radius: 10px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        margin-bottom: 20px;
    }

    .card-header {
        background-color: #f8f9fa;
        border-bottom: 2px solid #e9ecef;
        padding: 15px 20px;
        font-weight: 600;
    }

    .form-group label {
        font-weight: 600;
        color: #495057;
        margin-bottom: 8px;
    }

    .form-control:focus {
        border-color: #667eea;
        box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
    }
</style>
@endsection

@section('breadcrumb')
<div class="col-sm-6">
    <h4 class="page-title text-left">Profile</h4>
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="javascript:void(0);">Home</a></li>
        <li class="breadcrumb-item active">Profile</li>
    </ol>
</div>
@endsection

@section('content')
@include('includes.flash')

<div class="row">
    <div class="col-12">
        <!-- Profile Header -->
        <div class="profile-header">
            <div class="row align-items-center">
                <div class="col-md-3 text-center">
                    <div class="profile-photo-container">
                        <img src="{{ $user->photo ? asset('storage/profiles/' . $user->photo) : asset('assets/images/profile1.jpg') }}" 
                             alt="Profile Photo" 
                             class="profile-photo" 
                             id="profile-photo-preview">
                        <div class="profile-upload-btn">
                            <button type="button" class="btn btn-light btn-sm" onclick="document.getElementById('photo-upload').click()">
                                <i class="mdi mdi-camera mr-2"></i>Change Photo
                            </button>
                            <form id="photo-form" action="{{ route('profile.upload-photo') }}" method="POST" enctype="multipart/form-data" style="display: none;">
                                @csrf
                                <input type="file" name="photo" id="photo-upload" accept="image/*" onchange="uploadPhoto()">
                            </form>
                        </div>
                    </div>
                </div>
                <div class="col-md-9">
                    <h3 class="mb-2">{{ $user->name }}</h3>
                    <p class="mb-1"><i class="mdi mdi-email mr-2"></i>{{ $user->email }}</p>
                    @if($user->employee_code)
                    <p class="mb-1"><i class="mdi mdi-identifier mr-2"></i>Employee Code: {{ $user->employee_code }}</p>
                    @endif
                    @if($user->position)
                    <p class="mb-0"><i class="mdi mdi-briefcase mr-2"></i>{{ $user->position }}</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Profile Information -->
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="mdi mdi-account-edit mr-2"></i>Profile Information</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('profile.update') }}" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <div class="form-group">
                        <label for="name">Full Name</label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror" 
                               id="name" name="name" value="{{ old('name', $user->name) }}" required>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="email">Email Address</label>
                        <input type="email" class="form-control @error('email') is-invalid @enderror" 
                               id="email" name="email" value="{{ old('email', $user->email) }}" required>
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    @if(isset($user->phone) || Auth::guard('employee')->check())
                    <div class="form-group">
                        <label for="phone">Phone Number</label>
                        <input type="text" class="form-control @error('phone') is-invalid @enderror" 
                               id="phone" name="phone" value="{{ old('phone', $user->phone ?? '') }}">
                        @error('phone')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    @endif

                    <button type="submit" class="btn btn-primary">
                        <i class="mdi mdi-content-save mr-2"></i>Update Profile
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Change Password -->
    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="mdi mdi-lock-reset mr-2"></i>Change Password</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('profile.update-password') }}" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <div class="form-group">
                        <label for="current_password">Current Password</label>
                        <input type="password" class="form-control @error('current_password') is-invalid @enderror" 
                               id="current_password" name="current_password" required>
                        @error('current_password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="new_password">New Password</label>
                        <input type="password" class="form-control @error('new_password') is-invalid @enderror" 
                               id="new_password" name="new_password" required>
                        @error('new_password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="new_password_confirmation">Confirm New Password</label>
                        <input type="password" class="form-control" 
                               id="new_password_confirmation" name="new_password_confirmation" required>
                    </div>

                    <button type="submit" class="btn btn-warning btn-block">
                        <i class="mdi mdi-key-change mr-2"></i>Change Password
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('script')
<script>
    function uploadPhoto() {
        const fileInput = document.getElementById('photo-upload');
        const file = fileInput.files[0];
        
        if (file) {
            // Preview image
            const reader = new FileReader();
            reader.onload = function(e) {
                document.getElementById('profile-photo-preview').src = e.target.result;
            };
            reader.readAsDataURL(file);
            
            // Submit form
            document.getElementById('photo-form').submit();
        }
    }
</script>
@endsection

