@extends('layouts.master')

@section('breadcrumb')
    <div class="col-sm-6">
        <h4 class="page-title text-left">Settings</h4>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="javascript:void(0);">Home</a></li>
            <li class="breadcrumb-item"><a href="javascript:void(0);">Settings</a></li>
        </ol>
    </div>
@endsection

@section('content')
@include('includes.flash')

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title">System Settings</h5>
                <p class="card-text">Configure system-wide settings and preferences.</p>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('settings.update') }}" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    
                    <!-- AMS Logo Settings -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <h6 class="text-primary">AMS Logo Settings</h6>
                            <hr>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="ams_logo_type">Logo Type</label>
                                <select name="ams_logo_type" id="ams_logo_type" class="form-control" onchange="toggleLogoInput()">
                                    <option value="predefined" {{ old('ams_logo_type', getSetting('ams_logo_type', 'predefined')) == 'predefined' ? 'selected' : '' }}>Choose from predefined logos</option>
                                    <option value="upload" {{ old('ams_logo_type', getSetting('ams_logo_type', 'predefined')) == 'upload' ? 'selected' : '' }}>Upload custom logo</option>
                                </select>
                                <small class="form-text text-muted">Choose how to set the AMS logo</small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="ams_name">AMS Name</label>
                                <input type="text" name="ams_name" id="ams_name" class="form-control" 
                                       value="{{ old('ams_name', getSetting('ams_name', 'AMS')) }}" placeholder="Enter AMS name">
                                <small class="form-text text-muted">Name displayed next to the logo</small>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row" id="predefined_logo_section">
                        <div class="col-12">
                            <div class="form-group">
                                <label for="ams_logo">AMS Logo</label>
                                <select name="ams_logo" id="ams_logo" class="form-control">
                                    <option value="assets/images/logo.png" {{ old('ams_logo', getSetting('ams_logo', 'assets/images/logo.png')) == 'assets/images/logo.png' ? 'selected' : '' }}>📊 Default AMS Logo</option>
                                    <option value="assets/images/logo-dark.png" {{ old('ams_logo', getSetting('ams_logo', 'assets/images/logo.png')) == 'assets/images/logo-dark.png' ? 'selected' : '' }}>🌙 Dark AMS Logo</option>
                                    <option value="assets/images/logo-light.png" {{ old('ams_logo', getSetting('ams_logo', 'assets/images/logo.png')) == 'assets/images/logo-light.png' ? 'selected' : '' }}>☀️ Light AMS Logo</option>
                                    <option value="assets/images/logo-mini.png" {{ old('ams_logo', getSetting('ams_logo', 'assets/images/logo.png')) == 'assets/images/logo-mini.png' ? 'selected' : '' }}>🔸 Mini AMS Logo</option>
                                </select>
                                <small class="form-text text-muted">Choose the logo for AMS in sidebar</small>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row" id="upload_logo_section" style="display: none;">
                        <div class="col-12">
                            <div class="form-group">
                                <label for="ams_logo_upload">Upload Custom Logo</label>
                                <input type="file" name="ams_logo_upload" id="ams_logo_upload" class="form-control-file" accept="image/*">
                                <small class="form-text text-muted">Upload a custom logo (PNG, JPG, SVG - Max 2MB)</small>
                                <div id="logo_preview" class="mt-2" style="display: none;">
                                    <img id="preview_logo" src="" alt="Logo Preview" style="max-width: 120px; max-height: 40px;">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Footer Settings -->
                    <div class="row mb-4 mt-4">
                        <div class="col-12">
                            <h6 class="text-primary">Footer Settings</h6>
                            <hr>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-12">
                            <div class="form-group">
                                <label for="footer_text">Footer Text</label>
                                <textarea name="footer_text" id="footer_text" class="form-control" rows="3" 
                                          placeholder="Enter footer text">{{ old('footer_text', getSetting('footer_text', '© 2025 Attendance Management System - Crafted with ❤️ by Ali Aqa Atayee.')) }}</textarea>
                                <small class="form-text text-muted">Text displayed in the footer of the application</small>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="footer_show_year">Show Year in Footer</label>
                                <select name="footer_show_year" id="footer_show_year" class="form-control">
                                    <option value="1" {{ old('footer_show_year', getSetting('footer_show_year', 1)) == 1 ? 'selected' : '' }}>Yes</option>
                                    <option value="0" {{ old('footer_show_year', getSetting('footer_show_year', 1)) == 0 ? 'selected' : '' }}>No</option>
                                </select>
                                <small class="form-text text-muted">Automatically show current year in footer</small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="footer_show_author">Show Author in Footer</label>
                                <select name="footer_show_author" id="footer_show_author" class="form-control">
                                    <option value="1" {{ old('footer_show_author', getSetting('footer_show_author', 1)) == 1 ? 'selected' : '' }}>Yes</option>
                                    <option value="0" {{ old('footer_show_author', getSetting('footer_show_author', 1)) == 0 ? 'selected' : '' }}>No</option>
                                </select>
                                <small class="form-text text-muted">Show author information in footer</small>
                            </div>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="row mt-4">
                        <div class="col-12">
                            <div class="form-group">
                                <button type="submit" class="btn btn-primary">
                                    <i class="mdi mdi-content-save mr-2"></i>Save Settings
                                </button>
                                <button type="button" class="btn btn-secondary" onclick="resetForm()">
                                    <i class="mdi mdi-refresh mr-2"></i>Reset
                                </button>
                                <a href="{{ route('admin') }}" class="btn btn-info">
                                    <i class="mdi mdi-arrow-left mr-2"></i>Back to Dashboard
                                </a>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
function toggleLogoInput() {
    const logoType = document.getElementById('ams_logo_type').value;
    const predefinedSection = document.getElementById('predefined_logo_section');
    const uploadSection = document.getElementById('upload_logo_section');
    
    if (logoType === 'predefined') {
        predefinedSection.style.display = 'block';
        uploadSection.style.display = 'none';
    } else {
        predefinedSection.style.display = 'none';
        uploadSection.style.display = 'block';
    }
}

function resetForm() {
    if (confirm('Are you sure you want to reset all settings to default values?')) {
        document.querySelector('form').reset();
        toggleLogoInput(); // Reset the logo input visibility
    }
}

// Logo preview functionality
document.getElementById('ams_logo_upload').addEventListener('change', function(e) {
    const file = e.target.files[0];
    const preview = document.getElementById('logo_preview');
    const previewImg = document.getElementById('preview_logo');
    
    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            previewImg.src = e.target.result;
            preview.style.display = 'block';
        };
        reader.readAsDataURL(file);
    } else {
        preview.style.display = 'none';
    }
});

// Form validation
document.addEventListener('DOMContentLoaded', function() {
    const form = document.querySelector('form');
    
    // Initialize logo input visibility
    toggleLogoInput();
    
    form.addEventListener('submit', function(e) {
        // Debug: Log form data
        console.log('Form submitted with data:', {
            ams_name: document.getElementById('ams_name').value,
            ams_logo_type: document.getElementById('ams_logo_type').value,
            footer_text: document.getElementById('footer_text').value
        });
        
        // Basic validation
        const amsName = document.getElementById('ams_name').value.trim();
        
        console.log('AMS Name validation:', {
            value: amsName,
            length: amsName.length,
            isEmpty: !amsName,
            isTooLong: amsName.length > 100
        });
        
        if (!amsName) {
            e.preventDefault();
            alert('AMS name is required!');
            return false;
        }
        
        if (amsName.length > 100) {
            e.preventDefault();
            alert('AMS name must be less than 100 characters!');
            return false;
        }
        
        console.log('Form validation passed, submitting...');
        
        // File validation for upload
        const logoType = document.getElementById('ams_logo_type').value;
        if (logoType === 'upload') {
            const fileInput = document.getElementById('ams_logo_upload');
            if (fileInput.files.length === 0) {
                e.preventDefault();
                alert('Please select a logo file!');
                return false;
            }
            
            const file = fileInput.files[0];
            if (file.size > 2 * 1024 * 1024) { // 2MB
                e.preventDefault();
                alert('Logo file size must be less than 2MB!');
                return false;
            }
        }
    });
    
    // Debug: Add click listener to submit button
    const submitButton = form.querySelector('button[type="submit"]');
    if (submitButton) {
        submitButton.addEventListener('click', function(e) {
            console.log('Submit button clicked!');
        });
    }
    
    // Debug: Add form submit listener
    form.addEventListener('submit', function(e) {
        console.log('Form submit event triggered!');
    });
});
</script>

@endsection
