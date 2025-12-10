<?php if (session()->has('flash_message')): ?>
    <script>
        (function() {
            function showAlert() {
                if (typeof swal !== 'undefined') {
                    swal({
                        title: "{{session('flash_message.title')}}",
                        text: "{{session('flash_message.message')}}",
                        icon: "{{session('flash_message.level')}}",
                        button: true,
                        timer: 2500,
                    });
                } else {
                    setTimeout(showAlert, 100);
                }
            }
            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', showAlert);
            } else {
                showAlert();
            }
        })();
    </script>
<?php endif; ?>

<?php // Welcome Alert (untuk login) 
?>
<?php if (session()->has('welcome')): ?>
    <?php
    // Get user name and photo based on guard
    $userName = '';
    $userPhoto = '';
    try {
        if (auth('employee')->check()) {
            $user = auth('employee')->user();
            $userName = $user && isset($user->name) ? trim($user->name) : '';
            $userPhoto = $user && isset($user->photo) && $user->photo ? asset('storage/profiles/' . $user->photo) : asset('assets/images/profile1.jpg');
        } elseif (auth('web')->check()) {
            $user = auth()->user();
            $userName = $user && isset($user->name) ? trim($user->name) : '';
            $userPhoto = $user && isset($user->photo) && $user->photo ? asset('storage/profiles/' . $user->photo) : asset('assets/images/profile1.jpg');
        }
    } catch (\Exception $e) {
        $userName = '';
        $userPhoto = asset('assets/images/profile1.jpg');
    }

    // Prepare JavaScript variables safely
    $userNameJs = json_encode($userName, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT);
    $userPhotoJs = json_encode($userPhoto, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT);
    ?>
    <script>
        (function() {
            function showAlert() {
                if (typeof swal !== 'undefined') {
                    var userName = <?php echo $userNameJs; ?>;
                    var userPhoto = <?php echo $userPhotoJs; ?>;
                    var fallbackPhoto = '<?php echo asset('assets/images/profile1.jpg'); ?>';

                    // Buat struktur: Image di tengah, Welcome [Nama] di bawah
                    var fullMessage = '<div style="text-align: center; padding: 10px 0; margin-bottom: 20px;">';

                    // Tambahkan foto user langsung di HTML (responsive)
                    fullMessage += '<div class="welcome-image-container" style="text-align: center; padding: 20px 0; margin: 20px 0;">';
                    fullMessage += '<img src="' + userPhoto + '" ';
                    fullMessage += 'class="welcome-image" ';
                    fullMessage += 'style="width: 120px; height: 120px; border-radius: 50%; box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1); display: block; margin: 0 auto; max-width: 100%; object-fit: cover;" ';
                    fullMessage += 'alt="' + (userName || 'User') + '" ';
                    fullMessage += 'onerror="this.src=\'' + fallbackPhoto + '\'" />';
                    fullMessage += '</div>';

                    // Welcome message dengan nama di bawah
                    if (userName && userName !== null && userName !== undefined && String(userName).trim() !== '' && String(userName) !== 'null') {
                        fullMessage += '<p class="user-name-display" style="font-size: 20px; font-weight: bold; color: #1a1a1a; margin: 15px 0 0 0; text-shadow: 0 1px 2px rgba(0,0,0,0.1);">Welcome ' + String(userName) + '</p>';
                    } else {
                        fullMessage += '<p style="font-size: 18px; color: #2c3e50; font-weight: 500; margin: 15px 0 0 0;">Welcome</p>';
                    }
                    fullMessage += '</div>';

                    swal({
                        title: "Welcome to Dashboard",
                        html: fullMessage,
                        icon: false,
                        button: true,
                        timer: 6000,
                        allowOutsideClick: false,
                    });

                    setTimeout(function() {
                        var popup = document.querySelector('.swal-modal') ||
                            document.querySelector('.sweet-alert') ||
                            document.querySelector('.swal2-popup');

                        var container = document.querySelector('.swal2-container') ||
                            document.querySelector('.swal-overlay') ||
                            document.querySelector('.sweet-overlay');

                        // Prevent scrolling in container and ensure center position
                        if (container) {
                            container.style.position = 'fixed';
                            container.style.top = '0';
                            container.style.left = '0';
                            container.style.right = '0';
                            container.style.bottom = '0';
                            container.style.display = 'flex';
                            container.style.alignItems = 'center';
                            container.style.justifyContent = 'center';
                            container.style.padding = '0';
                            container.style.margin = '0';
                            container.style.height = '100%';
                            container.style.width = '100%';
                            container.style.zIndex = '9999';
                            container.style.overflow = 'hidden';
                            container.style.overflowY = 'hidden';
                            container.style.overflowX = 'hidden';
                        }

                        if (popup) {
                            // Ensure popup is centered (for SweetAlert v1)
                            if (popup.classList.contains('sweet-alert')) {
                                popup.style.position = 'fixed';
                                popup.style.top = '50%';
                                popup.style.left = '50%';
                                popup.style.transform = 'translate(-50%, -50%)';
                                popup.style.margin = '0';
                            }

                            // Prevent scrolling in popup
                            popup.style.overflow = 'hidden';
                            popup.style.overflowY = 'hidden';
                            popup.style.overflowX = 'hidden';
                            popup.style.maxHeight = 'none';

                            // Prevent scrolling in html container
                            var htmlContainer = popup.querySelector('.swal2-html-container') ||
                                popup.querySelector('.swal-text');
                            if (htmlContainer) {
                                htmlContainer.style.overflow = 'hidden';
                                htmlContainer.style.overflowY = 'hidden';
                                htmlContainer.style.overflowX = 'hidden';
                                htmlContainer.style.maxHeight = 'none';
                            }

                            // Prevent scrolling in content area
                            var contentArea = popup.querySelector('.swal2-content');
                            if (contentArea) {
                                contentArea.style.overflow = 'hidden';
                                contentArea.style.overflowY = 'hidden';
                                contentArea.style.overflowX = 'hidden';
                                contentArea.style.maxHeight = 'none';
                            }

                            // Cek apakah gambar sudah ada
                            var existingImg = popup.querySelector('img');
                            if (!existingImg) {
                                var contentContainer = popup.querySelector('.welcome-image-container') ||
                                    popup.querySelector('.swal-text') ||
                                    popup.querySelector('.swal2-html-container') ||
                                    popup;

                                var userName = <?php echo $userNameJs; ?>;
                                var userPhoto = <?php echo $userPhotoJs; ?>;
                                var fallbackPhoto = '<?php echo asset('assets/images/profile1.jpg'); ?>';

                                var img = document.createElement('img');
                                img.src = userPhoto;
                                img.className = 'welcome-image';
                                img.style.cssText = 'width: 120px; height: 120px; border-radius: 50%; box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1); display: block; margin: 20px auto; max-width: 100%; object-fit: cover;';
                                img.alt = userName || 'User';

                                img.onerror = function() {
                                    this.src = fallbackPhoto;
                                };

                                if (contentContainer.firstChild) {
                                    contentContainer.insertBefore(img, contentContainer.firstChild);
                                } else {
                                    contentContainer.appendChild(img);
                                }
                            }

                            // Pastikan nama muncul jika belum ada
                            var userName = <?php echo $userNameJs; ?>;
                            var existingName = popup.querySelector('.user-name-display');
                            if (!existingName && userName && String(userName).trim() !== '' && String(userName) !== 'null') {
                                var nameElement = document.createElement('p');
                                nameElement.className = 'user-name-display';
                                nameElement.style.cssText = 'font-size: 20px; font-weight: bold; color: #1a1a1a; margin: 15px 0 0 0; text-shadow: 0 1px 2px rgba(0,0,0,0.1); text-align: center;';
                                nameElement.textContent = 'Welcome ' + String(userName);

                                var content = popup.querySelector('.swal-text') ||
                                    popup.querySelector('.swal2-html-container') ||
                                    popup.querySelector('div[style*="text-align: center"]') ||
                                    popup;

                                if (content) {
                                    content.appendChild(nameElement);
                                }
                            }
                        }
                    }, 200);
                } else {
                    setTimeout(showAlert, 100);
                }
            }
            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', showAlert);
            } else {
                showAlert();
            }
        })();
    </script>
    <style>
        /* Welcome Alert Styles */
        .swal-modal,
        .sweet-alert,
        .swal2-popup {
            border-radius: 15px !important;
            display: flex !important;
            flex-direction: column !important;
            overflow: hidden !important;
            max-height: none !important;
        }

        /* Pastikan alert muncul tepat di tengah layar - SweetAlert2 */
        .swal2-container,
        .swal-overlay {
            position: fixed !important;
            inset: 0 !important;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            padding: 0 !important;
            margin: 0 !important;
            height: 100% !important;
            width: 100% !important;
            z-index: 9999 !important;
            overflow: hidden !important;
            overflow-y: hidden !important;
            overflow-x: hidden !important;
        }

        /* Pastikan alert muncul tepat di tengah layar - SweetAlert v1 */
        .sweet-overlay {
            position: fixed !important;
            top: 0 !important;
            left: 0 !important;
            right: 0 !important;
            bottom: 0 !important;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            z-index: 9999 !important;
        }

        .sweet-alert {
            position: fixed !important;
            top: 50% !important;
            left: 50% !important;
            transform: translate(-50%, -50%) !important;
            margin: 0 !important;
        }

        .swal-modal img,
        .sweet-alert img,
        .swal2-popup img {
            animation: welcomePulse 2s ease-in-out infinite;
            object-fit: cover;
        }

        .swal-modal p,
        .sweet-alert p,
        .swal2-popup p {
            color: #2c3e50 !important;
            font-weight: 500 !important;
            line-height: 1.6 !important;
        }

        .swal-modal .swal-text,
        .sweet-alert .swal-text,
        .swal2-popup .swal2-html-container {
            color: #2c3e50 !important;
            font-size: 16px !important;
            order: 1 !important;
            margin-bottom: 30px !important;
            flex: 0 0 auto !important;
            overflow: hidden !important;
            max-height: none !important;
            overflow-y: hidden !important;
            overflow-x: hidden !important;
        }

        .swal2-popup .swal2-content {
            overflow: hidden !important;
            overflow-y: hidden !important;
            overflow-x: hidden !important;
            max-height: none !important;
        }

        .swal-modal .swal-title,
        .sweet-alert h2,
        .swal2-popup .swal2-title {
            order: 0 !important;
            flex: 0 0 auto !important;
        }

        .swal-modal .swal-button-container,
        .sweet-alert .sa-button-container,
        .swal2-popup .swal2-actions {
            order: 2 !important;
            margin-top: auto !important;
            padding-top: 20px !important;
            text-align: center !important;
            display: flex !important;
            justify-content: center !important;
            align-items: center !important;
            border-top: 1px solid #e0e0e0 !important;
            flex: 0 0 auto !important;
        }

        .swal-modal .swal-button,
        .sweet-alert button,
        .swal2-popup .swal2-confirm {
            margin: 0 auto !important;
            display: block !important;
            float: none !important;
        }

        @keyframes welcomePulse {

            0%,
            100% {
                transform: scale(1);
            }

            50% {
                transform: scale(1.05);
            }
        }

        /* Mobile Responsive Styles untuk Welcome Alert */
        @media (max-width: 768px) {

            /* Alert popup lebih kecil di mobile */
            .swal-modal,
            .sweet-alert,
            .swal2-popup {
                width: 90% !important;
                max-width: 400px !important;
                padding: 20px 15px !important;
                margin: 10px !important;
            }

            /* Gambar lebih kecil di mobile */
            .swal-modal img,
            .sweet-alert img,
            .swal2-popup img {
                width: 80px !important;
                height: 80px !important;
                margin: 10px auto !important;
            }

            /* Title lebih kecil */
            .swal-modal .swal-title,
            .sweet-alert h2,
            .swal2-popup .swal2-title {
                font-size: 18px !important;
                margin-bottom: 10px !important;
                padding: 0 10px !important;
            }

            /* Text content lebih kecil */
            .swal-modal .swal-text,
            .sweet-alert .swal-text,
            .swal2-popup .swal2-html-container {
                font-size: 14px !important;
                padding: 0 10px !important;
                margin-bottom: 15px !important;
            }

            /* User name display lebih kecil */
            .swal-modal .user-name-display,
            .sweet-alert .user-name-display,
            .swal2-popup .user-name-display {
                font-size: 16px !important;
                margin: 10px 0 !important;
                padding: 0 10px !important;
            }

            /* Button container lebih compact */
            .swal-modal .swal-button-container,
            .sweet-alert .sa-button-container,
            .swal2-popup .swal2-actions {
                padding-top: 15px !important;
                margin-top: 10px !important;
            }

            /* Button lebih kecil */
            .swal-modal .swal-button,
            .sweet-alert button,
            .swal2-popup .swal2-confirm {
                font-size: 14px !important;
                padding: 8px 20px !important;
                min-width: 80px !important;
            }

            /* Container tetap centered */
            .swal2-container,
            .swal-overlay,
            .sweet-overlay {
                padding: 10px !important;
            }

            /* Pastikan tidak ada overflow */
            .swal-modal,
            .sweet-alert,
            .swal2-popup {
                max-height: 90vh !important;
                overflow-y: auto !important;
            }
        }

        /* Mobile Extra Small */
        @media (max-width: 480px) {

            /* Alert popup lebih kecil lagi */
            .swal-modal,
            .sweet-alert,
            .swal2-popup {
                width: 95% !important;
                max-width: 350px !important;
                padding: 15px 10px !important;
                margin: 5px !important;
            }

            /* Gambar lebih kecil */
            .swal-modal img,
            .sweet-alert img,
            .swal2-popup img {
                width: 70px !important;
                height: 70px !important;
                margin: 8px auto !important;
            }

            /* Title lebih kecil */
            .swal-modal .swal-title,
            .sweet-alert h2,
            .swal2-popup .swal2-title {
                font-size: 16px !important;
                margin-bottom: 8px !important;
                padding: 0 5px !important;
            }

            /* Text content lebih kecil */
            .swal-modal .swal-text,
            .sweet-alert .swal-text,
            .swal2-popup .swal2-html-container {
                font-size: 13px !important;
                padding: 0 5px !important;
                margin-bottom: 10px !important;
            }

            /* User name display lebih kecil */
            .swal-modal .user-name-display,
            .sweet-alert .user-name-display,
            .swal2-popup .user-name-display {
                font-size: 15px !important;
                margin: 8px 0 !important;
                padding: 0 5px !important;
            }

            /* Button lebih kecil */
            .swal-modal .swal-button,
            .sweet-alert button,
            .swal2-popup .swal2-confirm {
                font-size: 13px !important;
                padding: 6px 16px !important;
                min-width: 70px !important;
            }
        }
    </style>
<?php endif; ?>

<?php // Success Alert (untuk operasi CRUD biasa) 
?>
<?php if (session()->has('success')): ?>
    <script>
        (function() {
            function showAlert() {
                if (typeof swal !== 'undefined') {
                    swal({
                        title: "Success!",
                        text: "{{ session('success') }}",
                        icon: "success",
                        button: true,
                        timer: 3000,
                    });
                } else {
                    setTimeout(showAlert, 100);
                }
            }
            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', showAlert);
            } else {
                showAlert();
            }
        })();
    </script>
<?php endif; ?>

<?php if (session()->has('flash_message_overlay')): ?>
    <script>
        (function() {
            function showAlert() {
                if (typeof swal !== 'undefined') {
                    swal({
                        title: "{{session('flash_message_overlay.title')}}",
                        text: "{{session('flash_message_overlay.message')}}",
                        icon: "{{session('flash_message_overlay.level')}}",
                        button: 'Okay',
                    });
                } else {
                    setTimeout(showAlert, 100);
                }
            }
            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', showAlert);
            } else {
                showAlert();
            }
        })();
    </script>
<?php endif; ?>