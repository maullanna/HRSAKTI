<!-- App's Basic Js  -->
<script src="{{ URL::asset('assets/js/jquery.min.js') }}"></script>
<script src="{{ URL::asset('assets/js/bootstrap.bundle.min.js') }}"></script>
<script src="{{ URL::asset('assets/js/metisMenu.min.js') }}"></script>
<script src="{{ URL::asset('assets/js/jquery.slimscroll.js') }}"></script>
<script src="{{ URL::asset('assets/js/waves.min.js') }}"></script>

<?php echo $__env->yieldContent('script'); ?>

<!-- App js-->
<script src="{{ URL::asset('assets/js/app.js') }}"></script>


<!-- Sweet-Alert  -->
{{-- <script src="{{ URL::asset('plugins/sweet-alert2/sweetalert2.min.js') }}"></script>
<script src="{{ URL::asset('assets/pages/sweet-alert.init.js') }}"></script> --}}
<script src="/js/sweetalert.min.js"></script>
<!-- Responsive-table-->
<script src="{{ URL::asset('plugins/RWD-Table-Patterns/dist/js/rwd-table.min.js') }}"></script>
<!-- Required datatable js -->
<script src="{{ URL::asset('plugins/datatables/jquery.dataTables.min.js') }}"></script>
<script src="{{ URL::asset('plugins/datatables/dataTables.bootstrap4.min.js') }}"></script>
<!-- Buttons examples -->
<script src="{{ URL::asset('plugins/datatables/dataTables.buttons.min.js') }}"></script>
<script src="{{ URL::asset('plugins/datatables/buttons.bootstrap4.min.js') }}"></script>
<script src="{{ URL::asset('plugins/datatables/jszip.min.js') }}"></script>
<script src="{{ URL::asset('plugins/datatables/pdfmake.min.js') }}"></script>
<script src="{{ URL::asset('plugins/datatables/vfs_fonts.js') }}"></script>
<script src="{{ URL::asset('plugins/datatables/buttons.html5.min.js') }}"></script>
<script src="{{ URL::asset('plugins/datatables/buttons.print.min.js') }}"></script>
<script src="{{ URL::asset('plugins/datatables/buttons.colVis.min.js') }}"></script>
<!-- Responsive examples -->
<script src="{{ URL::asset('plugins/datatables/dataTables.responsive.min.js') }}"></script>
<script src="{{ URL::asset('plugins/datatables/responsive.bootstrap4.min.js') }}"></script>

<!-- Datatable init js -->
<script src="{{ URL::asset('assets/pages/datatables.init.js') }}"></script>


<!-- Custom JavaScript for minimized sidebar dropdown toggle -->
<script>
   $(document).ready(function() {
      // Pastikan MetisMenu sudah diinisialisasi
      if (typeof $.fn.metisMenu !== 'undefined') {
         $("#side-menu").metisMenu({
            toggle: true,
            preventDefault: true
         });
      }
      // Handle click on menu items with submenu when sidebar is minimized
      $(document).on('click', 'body.enlarged #side-menu > li > a', function(e) {
         // Don't trigger if clicking inside submenu
         if ($(e.target).closest('.submenu').length > 0) {
            return;
         }

         var $li = $(this).parent('li');
         var $submenu = $li.children('.submenu').first();

         // Only handle if this menu item has a submenu
         if ($submenu.length > 0) {
            e.preventDefault(); // Prevent default link behavior
            e.stopPropagation();

            // Toggle submenu visibility
            $li.toggleClass('submenu-show');

            // Close other submenus
            $('body.enlarged #side-menu > li').not($li).removeClass('submenu-show');
         }
      });

      // Close submenu when clicking outside
      $(document).on('click', function(e) {
         if ($('body').hasClass('enlarged')) {
            if (!$(e.target).closest('#sidebar-menu').length) {
               $('body.enlarged #side-menu > li').removeClass('submenu-show');
            }
         }
      });

      // Prevent submenu clicks from closing parent
      $(document).on('click', 'body.enlarged #sidebar-menu .submenu a', function(e) {
         e.stopPropagation();
      });

      // Mobile sidebar toggle - override default behavior
      // Pastikan dipanggil setelah app.js
      setTimeout(function() {
         $('.button-menu-mobile').off('click').on('click', function(e) {
            e.preventDefault();
            e.stopPropagation();

            // Check if mobile view
            if ($(window).width() <= 768) {
               $('body').toggleClass('menu-open');
            } else {
               // Desktop behavior - toggle enlarged
               $("body").toggleClass("enlarged");
            }
         });
      }, 100);

      // Close sidebar when clicking outside (optional - bisa diaktifkan jika perlu)
      // $(document).on('click', function(e) {
      //    if ($('body').hasClass('menu-open')) {
      //       // Close if clicking outside sidebar and not on menu button or topbar
      //       if (!$(e.target).closest('.left.side-menu').length && 
      //           !$(e.target).closest('.button-menu-mobile').length &&
      //           !$(e.target).closest('.topbar-left').length &&
      //           !$(e.target).closest('.navbar-custom').length) {
      //          $('body').removeClass('menu-open');
      //       }
      //    }
      // });

      // Prevent sidebar click from closing
      $('.left.side-menu').on('click', function(e) {
         e.stopPropagation();
      });

      // Close sidebar when window is resized to desktop
      $(window).on('resize', function() {
         if ($(window).width() > 768) {
            $('body').removeClass('menu-open');
         }
      });

      // Pastikan ketika menu lain diklik, menu yang sebelumnya terbuka akan tertutup
      // Ini bekerja untuk semua halaman admin
      // Gunakan event delegation dengan prioritas lebih rendah dari MetisMenu
      setTimeout(function() {
         $(document).on('click', '#side-menu > li > a', function(e) {
            var $clickedLi = $(this).parent('li');
            var $clickedSubmenu = $clickedLi.children('ul, .submenu').first();

            // Jika menu yang diklik memiliki submenu
            if ($clickedSubmenu.length > 0) {
               // Tunggu MetisMenu selesai memproses, baru tutup submenu lain
               setTimeout(function() {
                  // Tutup semua submenu lain yang terbuka
                  $('#side-menu > li').not($clickedLi).each(function() {
                     var $otherLi = $(this);
                     var $otherSubmenu = $otherLi.children('ul, .submenu').first();

                     // Jika submenu lain terbuka, tutup dengan benar
                     if ($otherSubmenu.length > 0 && ($otherSubmenu.hasClass('mm-show') || $otherLi.hasClass('mm-active'))) {
                        $otherLi.removeClass('mm-active');
                        $otherSubmenu.removeClass('mm-show').addClass('mm-collapse');
                        $otherSubmenu.css({
                           'display': 'none',
                           'visibility': 'hidden',
                           'opacity': '0',
                           'max-height': '0',
                           'overflow': 'hidden'
                        });

                        // Update aria-expanded
                        $otherLi.find('> a').attr('aria-expanded', 'false');
                     }
                  });
               }, 50);
            } else {
               // Jika menu yang diklik tidak memiliki submenu, tutup semua submenu
               $('#side-menu > li').each(function() {
                  var $otherLi = $(this);
                  var $otherSubmenu = $otherLi.children('ul, .submenu').first();

                  if ($otherSubmenu.length > 0 && ($otherSubmenu.hasClass('mm-show') || $otherLi.hasClass('mm-active'))) {
                     $otherLi.removeClass('mm-active');
                     $otherSubmenu.removeClass('mm-show').addClass('mm-collapse');
                     $otherSubmenu.css({
                        'display': 'none',
                        'visibility': 'hidden',
                        'opacity': '0',
                        'max-height': '0',
                        'overflow': 'hidden'
                     });
                     $otherLi.find('> a').attr('aria-expanded', 'false');
                  }
               });
            }
         });
      }, 100);
   });
</script>

<!-- Logout Confirmation with SweetAlert -->
<?php
// Get current locale and translations
$currentLocale = session('locale', 'en');
$logoutTitle = __('global.logout_confirmation_title');
$logoutText = __('global.logout_confirmation_text');
$logoutCancel = __('global.logout_confirmation_cancel');
$logoutConfirm = __('global.logout_confirmation_confirm');
$logoutProcessing = __('global.logout_processing');
$logoutProcessingText = __('global.logout_processing_text');
?>
<style>
   /* Center logout confirmation buttons - horizontal layout */
   .swal-modal .swal-button-container,
   .sweet-alert .sa-button-container,
   .swal2-popup .swal2-actions {
      text-align: center !important;
      display: flex !important;
      flex-direction: row !important;
      justify-content: center !important;
      align-items: center !important;
      gap: 10px !important;
      width: 100% !important;
      flex-wrap: nowrap !important;
   }

   /* Force buttons to be inline/horizontal */
   .swal-modal .swal-button,
   .sweet-alert .sa-button-container button,
   .sweet-alert button,
   .swal2-popup .swal2-cancel,
   .swal2-popup .swal2-confirm {
      margin: 0 5px !important;
      float: none !important;
      display: inline-block !important;
      flex-shrink: 0 !important;
      width: auto !important;
      clear: none !important;
      vertical-align: middle !important;
   }

   /* Fix Cancel button hover state - ensure contrast */
   .swal-modal .swal-button--cancel,
   .sweet-alert .sa-button-container .cancel,
   .swal2-popup .swal2-cancel {
      background-color: #6c757d !important;
      color: #ffffff !important;
      border-color: #6c757d !important;
   }

   .swal-modal .swal-button--cancel:hover,
   .sweet-alert .sa-button-container .cancel:hover,
   .swal2-popup .swal2-cancel:hover {
      background-color: #5a6268 !important;
      color: #ffffff !important;
      border-color: #545b62 !important;
   }

   /* Fix Confirm button hover state */
   .swal-modal .swal-button--confirm,
   .swal-modal .swal-button--danger,
   .sweet-alert .sa-button-container .confirm,
   .swal2-popup .swal2-confirm {
      background-color: #dc3545 !important;
      color: #ffffff !important;
      border-color: #dc3545 !important;
   }

   .swal-modal .swal-button--confirm:hover,
   .swal-modal .swal-button--danger:hover,
   .sweet-alert .sa-button-container .confirm:hover,
   .swal2-popup .swal2-confirm:hover {
      background-color: #c82333 !important;
      color: #ffffff !important;
      border-color: #bd2130 !important;
   }
</style>
<script>
   // Set translations in window object (separated to avoid linter errors)
   window.logoutTranslations = window.logoutTranslations || {};
   window.logoutTranslations.title = <?php echo json_encode($logoutTitle); ?>;
   window.logoutTranslations.text = <?php echo json_encode($logoutText); ?>;
   window.logoutTranslations.cancel = <?php echo json_encode($logoutCancel); ?>;
   window.logoutTranslations.confirm = <?php echo json_encode($logoutConfirm); ?>;
   window.logoutTranslations.processing = <?php echo json_encode($logoutProcessing); ?>;
   window.logoutTranslations.processingText = <?php echo json_encode($logoutProcessingText); ?>;

   $(document).ready(function() {
      // Handle logout link clicks with SweetAlert confirmation
      $(document).on('click', '.logout-link', function(e) {
         e.preventDefault();

         var formId = $(this).data('form-id');
         var logoutForm = document.getElementById(formId);

         if (!logoutForm) {
            console.error('Logout form not found:', formId);
            return;
         }

         // Get translations from window object
         var logoutTitle = window.logoutTranslations.title;
         var logoutText = window.logoutTranslations.text;
         var logoutCancel = window.logoutTranslations.cancel;
         var logoutConfirm = window.logoutTranslations.confirm;
         var logoutProcessing = window.logoutTranslations.processing;
         var logoutProcessingText = window.logoutTranslations.processingText;

         // Show SweetAlert confirmation
         if (typeof swal !== 'undefined') {
            var logoutPromise = swal({
               title: logoutTitle,
               text: logoutText,
               icon: "warning",
               buttons: {
                  cancel: {
                     text: logoutCancel,
                     value: false,
                     visible: true,
                     className: "btn btn-secondary",
                     closeModal: true,
                  },
                  confirm: {
                     text: logoutConfirm,
                     value: true,
                     visible: true,
                     className: "btn btn-danger",
                     closeModal: false
                  }
               },
               dangerMode: true,
            });

            // Center buttons after alert is shown
            setTimeout(function() {
               var popup = document.querySelector('.swal-modal') ||
                  document.querySelector('.sweet-alert') ||
                  document.querySelector('.swal2-popup');

               if (popup) {
                  // Center button container
                  var buttonContainer = popup.querySelector('.swal-button-container') ||
                     popup.querySelector('.sa-button-container') ||
                     popup.querySelector('.swal2-actions');

                  if (buttonContainer) {
                     buttonContainer.style.textAlign = 'center';
                     buttonContainer.style.display = 'flex';
                     buttonContainer.style.flexDirection = 'row';
                     buttonContainer.style.justifyContent = 'center';
                     buttonContainer.style.alignItems = 'center';
                     buttonContainer.style.gap = '10px';
                     buttonContainer.style.width = '100%';
                     buttonContainer.style.flexWrap = 'nowrap';
                  }

                  // Force buttons to be horizontal
                  var buttons = popup.querySelectorAll('.swal-button, .sa-button, .swal2-cancel, .swal2-confirm');
                  buttons.forEach(function(btn) {
                     btn.style.margin = '0 5px';
                     btn.style.float = 'none';
                     btn.style.display = 'inline-block';
                     btn.style.width = 'auto';
                     btn.style.minWidth = '100px';
                  });

                  // Fix Cancel button hover state
                  var cancelButtons = popup.querySelectorAll('.swal-button--cancel, .sa-button-container .cancel, .swal2-cancel');
                  cancelButtons.forEach(function(btn) {
                     btn.style.backgroundColor = '#6c757d';
                     btn.style.color = '#ffffff';
                     btn.style.borderColor = '#6c757d';

                     // Add hover event
                     btn.addEventListener('mouseenter', function() {
                        this.style.backgroundColor = '#5a6268';
                        this.style.color = '#ffffff';
                        this.style.borderColor = '#545b62';
                     });
                     btn.addEventListener('mouseleave', function() {
                        this.style.backgroundColor = '#6c757d';
                        this.style.color = '#ffffff';
                        this.style.borderColor = '#6c757d';
                     });
                  });
               }
            }, 100);

            // Handle logout confirmation
            logoutPromise.then(function(willLogout) {
               if (willLogout) {
                  // Show loading state
                  swal({
                     title: logoutProcessing,
                     text: logoutProcessingText,
                     icon: "info",
                     button: false,
                     closeOnClickOutside: false,
                     closeOnEsc: false,
                  });

                  // Submit logout form
                  if (logoutForm) {
                     logoutForm.submit();
                  } else {
                     console.error('Logout form not found');
                     window.location.href = $(this).data('logout-url') || '/employee/login';
                  }
               }
            }).catch(function(error) {
               console.error('Logout error:', error);
               // Fallback: submit form directly
               if (logoutForm) {
                  logoutForm.submit();
               }
            });
         } else {
            // Fallback to native confirm if SweetAlert is not available
            if (confirm(logoutText)) {
               if (logoutForm) {
                  logoutForm.submit();
               } else {
                  console.error('Logout form not found');
               }
            }
         }
      });
   });
</script>

<?php echo $__env->yieldContent('script-bottom'); ?>