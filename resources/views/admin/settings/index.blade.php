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
                        <div class="col-12">
                            <div class="form-group">
                                <label for="ams_logo_type">Logo Type</label>
                                <select name="ams_logo_type" id="ams_logo_type" class="form-control" onchange="toggleLogoInput()">
                                    <option value="predefined" {{ old('ams_logo_type', getSetting('ams_logo_type', 'predefined')) == 'predefined' ? 'selected' : '' }}>Choose from predefined logos</option>
                                    <option value="upload" {{ old('ams_logo_type', getSetting('ams_logo_type', 'predefined')) == 'upload' ? 'selected' : '' }}>Upload custom logo</option>
                                </select>
                                <small class="form-text text-muted">Choose how to set the AMS logo</small>
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

                                @if(getSetting('ams_logo_type') === 'upload' && !empty(getSetting('ams_logo')))
                                <div class="mb-2 p-2 border rounded bg-light">
                                    <strong>Current Logo:</strong><br>
                                    <img src="{{ asset(getSetting('ams_logo')) }}" alt="Current Logo" style="max-width: 200px; max-height: 80px; object-fit: contain; margin-top: 5px;" onerror="this.style.display='none'">
                                    <br><small class="text-muted">{{ getSetting('ams_logo') }}</small>
                                </div>
                                @endif

                                <input type="file" name="ams_logo_upload" id="ams_logo_upload" class="form-control-file" accept="image/*">
                                <small class="form-text text-muted">Upload a custom logo (PNG, JPG, SVG - Max 2MB). This will replace the current logo.</small>
                                <div id="logo_preview" class="mt-2" style="display: none;">
                                    <strong>New Logo Preview:</strong><br>
                                    <img id="preview_logo" src="" alt="Logo Preview" style="max-width: 200px; max-height: 80px; object-fit: contain; margin-top: 5px;">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- System Name Settings -->
                    <div class="row mb-4 mt-4">
                        <div class="col-12">
                            <h6 class="text-primary">System Name Settings</h6>
                            <hr>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-12">
                            <div class="form-group">
                                <label for="system_name">System Name</label>
                                <input type="text" name="system_name" id="system_name" class="form-control"
                                    value="{{ old('system_name', getSetting('system_name', 'HRSAKTI')) }}"
                                    placeholder="e.g. HRSAKTI, HR Management, Company Name">
                                <small class="form-text text-muted">System name displayed next to the logo in header (max 30 characters)</small>
                                <div class="mt-2">
                                    <strong>Preview:</strong>
                                    <div id="system_name_preview" class="border p-2 bg-light rounded mt-1">
                                        <span style="font-size: 1.2em; font-weight: 600; color: #2f3e47;">{{ getSetting('system_name', 'HRSAKTI') }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-12">
                            <div class="form-group">
                                <label for="system_name_visible">Show System Name</label>
                                <select name="system_name_visible" id="system_name_visible" class="form-control">
                                    <option value="1" {{ old('system_name_visible', getSetting('system_name_visible', '1')) == '1' ? 'selected' : '' }}>✅ Show (Visible)</option>
                                    <option value="0" {{ old('system_name_visible', getSetting('system_name_visible', '1')) == '0' ? 'selected' : '' }}>❌ Hide (Logo Only)</option>
                                </select>
                                <small class="form-text text-muted">Choose whether to display the system name next to logo</small>
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
                                    placeholder="Enter footer text">{{ old('footer_text', getSetting('footer_text', '© {YEAR} Attendance Management System - Crafted with ❤️ by Ali Aqa Atayee.')) }}</textarea>
                                <small class="form-text text-muted">Text displayed in the footer of the application. Use {YEAR} as placeholder for current year (automatically updated).</small>
                                <div class="mt-2">
                                    <strong>Preview:</strong>
                                    <div id="footer_preview" class="border p-2 bg-light rounded mt-1" style="font-size: 0.9em;">
                                        {{ getFooterText() }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-12">
                            <div class="alert alert-info">
                                <i class="mdi mdi-information mr-2"></i>
                                <strong>Note:</strong> Year (© 2025) will be automatically added to the beginning of your footer text. You only need to enter the text content.
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

        // System Name preview functionality
        const systemNameInput = document.getElementById('system_name');
        const systemNamePreview = document.getElementById('system_name_preview');

        function updateSystemNamePreview() {
            const systemName = systemNameInput.value || 'HRSAKTI';
            systemNamePreview.innerHTML = '<span style="font-size: 1.2em; font-weight: 600; color: #2f3e47;">' + systemName + '</span>';
        }

        // Add event listener for real-time preview
        systemNameInput.addEventListener('input', updateSystemNamePreview);

        // Footer preview functionality
        const footerTextInput = document.getElementById('footer_text');
        const footerPreview = document.getElementById('footer_preview');

        function updateFooterPreview() {
            let previewText = footerTextInput.value;
            const currentYear = new Date().getFullYear();

            // Always add year at the beginning
            let finalText = '© ' + currentYear + ' ' + previewText;

            // Remove any existing year patterns to avoid duplication
            finalText = finalText.replace(/©\s*\d{4}\s*/g, '');
            finalText = '© ' + currentYear + ' ' + finalText.trim();

            footerPreview.textContent = finalText;
        }

        // Add event listeners for real-time preview
        footerTextInput.addEventListener('input', updateFooterPreview);

        // Initialize previews
        updateSystemNamePreview();
        updateFooterPreview();

        form.addEventListener('submit', function(e) {
            // Debug: Log form data
            console.log('Form submitted with data:', {
                ams_logo_type: document.getElementById('ams_logo_type').value,
                footer_text: document.getElementById('footer_text').value
            });

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