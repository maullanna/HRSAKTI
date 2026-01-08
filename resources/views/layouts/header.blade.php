   <!-- Top Bar Start -->
   <div class="topbar">

       <!-- LOGO -->
       <div class="topbar-left d-none d-md-block">
           <a href="{{ route('admin') }}" class="logo d-none d-md-flex" style="display: flex; align-items: center; gap: 12px; padding: 0 15px;">
               <?php
                $logoType = getSetting('ams_logo_type', 'predefined');
                $logoPath = getSetting('ams_logo', 'assets/images/logo.png');

                // Ensure logo path is valid
                if (empty($logoPath) || $logoPath === 'assets/images') {
                    $logoPath = 'assets/images/logo.png';
                }

                // For uploaded logos, check if file exists
                if ($logoType === 'upload' && !empty($logoPath)) {
                    $fullPath = public_path($logoPath);
                    if (!file_exists($fullPath)) {
                        // Fallback to default if uploaded file doesn't exist
                        $logoPath = 'assets/images/logo.png';
                    }
                }

                $systemName = getSetting('system_name', 'HRSAKTI');
                $systemNameVisible = getSetting('system_name_visible', '1');
                ?>
               <?php
                $fallbackLogo = asset('assets/images/logo.png');
                ?>
               <img src="{{ asset($logoPath) }}" alt="logo" class="logo-lg" style="height: 50px; max-width: 200px; object-fit: contain;" onerror="this.onerror=null; this.src='{{ $fallbackLogo }}';">

               <?php if ($systemNameVisible == '1'): ?>
                   <span class="logo-text d-none d-md-inline"><?php echo e($systemName); ?></span>
               <?php endif; ?>
           </a>
       </div>

       <nav class="navbar-custom">
           <ul class="navbar-right d-flex list-inline float-right mb-0">
               <li class="dropdown notification-list d-none d-md-block">
                   <form role="search" class="app-search">
                       <div class="form-group mb-0">
                           <input type="text" class="form-control" placeholder="Search..">
                           <button type="submit"><i class="fa fa-search"></i></button>
                       </div>
                   </form>
               </li>

               <!-- language-->
               <?php
                $currentLocale = session('locale', 'en');
                $currentLanguage = $currentLocale === 'id' ? 'Indonesian' : 'English';
                $indonesiaFlagPathForCurrent = public_path('assets/images/flags/indonesia_flag.jpg');
                $currentFlag = $currentLocale === 'id'
                    ? (file_exists($indonesiaFlagPathForCurrent)
                        ? asset('assets/images/flags/indonesia_flag.jpg') . '?v=' . filemtime($indonesiaFlagPathForCurrent)
                        : asset('assets/images/flags/us_flag.jpg'))
                    : asset('assets/images/flags/us_flag.jpg');

                // Check if indonesia flag exists, otherwise use US flag as fallback
                $indonesiaFlagPath = public_path('assets/images/flags/indonesia_flag.jpg');
                $indonesiaFlag = file_exists($indonesiaFlagPath)
                    ? asset('assets/images/flags/indonesia_flag.jpg') . '?v=' . filemtime($indonesiaFlagPath)
                    : asset('assets/images/flags/us_flag.jpg');
                $fallbackFlag = asset('assets/images/flags/us_flag.jpg');
                ?>
               <li class="dropdown notification-list d-none d-md-block">
                   <a class="nav-link dropdown-toggle arrow-none waves-effect" data-toggle="dropdown" href="#" role="button" aria-haspopup="false" aria-expanded="false">
                       <img src="{{ $currentFlag }}" class="mr-2" height="12" alt="" onerror="this.src='{{ $fallbackFlag }}'" /> {{ $currentLanguage }} <span class="mdi mdi-chevron-down"></span>
                   </a>
                   <div class="dropdown-menu dropdown-menu-right language-switch">
                       <a class="dropdown-item {{ $currentLocale === 'en' ? 'active' : '' }}" href="{{ route('language.switch', 'en') }}">
                           <img src="{{ asset('assets/images/flags/us_flag.jpg') }}" alt="" height="16" /> <span> English </span>
                       </a>
                       <a class="dropdown-item {{ $currentLocale === 'id' ? 'active' : '' }}" href="{{ route('language.switch', 'id') }}">
                           <img src="{{ $indonesiaFlag }}" alt="" height="16" onerror="this.src='{{ $fallbackFlag }}'" /> <span> Indonesian </span>
                       </a>
                   </div>
               </li>

               <!-- full screen -->
               <li class="dropdown notification-list d-none d-md-block">
                   <a class="nav-link waves-effect" href="#" id="btn-fullscreen">
                       <i class="mdi mdi-fullscreen noti-icon"></i>
                   </a>
               </li>

               <!-- notification -->
               <li class="dropdown notification-list d-none d-md-block">
                   <a class="nav-link dropdown-toggle arrow-none waves-effect" data-toggle="dropdown" href="#" role="button" aria-haspopup="false" aria-expanded="false">
                       <i class="mdi mdi-bell-outline noti-icon"></i>
                       <span class="badge badge-pill badge-danger noti-icon-badge">3</span>
                   </a>
                   <div class="dropdown-menu dropdown-menu-right dropdown-menu-lg">
                       <!-- item-->
                       <h6 class="dropdown-item-text">
                           Notifications (258)
                       </h6>
                       <div class="slimscroll notification-item-list">
                           <!-- item-->
                           <a href="javascript:void(0);" class="dropdown-item notify-item active">
                               <div class="notify-icon bg-success"><i class="mdi mdi-cart-outline"></i></div>
                               <p class="notify-details">Your order is placed<span class="text-muted">Dummy text of the printing and typesetting industry.</span></p>
                           </a>
                           <!-- item-->
                           <a href="javascript:void(0);" class="dropdown-item notify-item">
                               <div class="notify-icon bg-warning"><i class="mdi mdi-message-text-outline"></i></div>
                               <p class="notify-details">New Message received<span class="text-muted">You have 87 unread messages</span></p>
                           </a>
                           <!-- item-->
                           <a href="javascript:void(0);" class="dropdown-item notify-item">
                               <div class="notify-icon bg-info"><i class="mdi mdi-glass-cocktail"></i></div>
                               <p class="notify-details">Your item is shipped<span class="text-muted">It is a long established fact that a reader will</span></p>
                           </a>
                           <!-- item-->
                           <a href="javascript:void(0);" class="dropdown-item notify-item">
                               <div class="notify-icon bg-primary"><i class="mdi mdi-cart-outline"></i></div>
                               <p class="notify-details">Your order is placed<span class="text-muted">Dummy text of the printing and typesetting industry.</span></p>
                           </a>
                           <!-- item-->
                           <a href="javascript:void(0);" class="dropdown-item notify-item">
                               <div class="notify-icon bg-danger"><i class="mdi mdi-message-text-outline"></i></div>
                               <p class="notify-details">New Message received<span class="text-muted">You have 87 unread messages</span></p>
                           </a>
                       </div>
                       <!-- All-->
                       <a href="javascript:void(0);" class="dropdown-item text-center text-primary">
                           View all <i class="fi-arrow-right"></i>
                       </a>
                   </div>
               </li>

               <!-- language for mobile -->
               <li class="dropdown notification-list d-md-none">
                   <a class="nav-link dropdown-toggle arrow-none waves-effect" data-toggle="dropdown" href="#" role="button" aria-haspopup="false" aria-expanded="false">
                       <img src="{{ $currentFlag }}" class="mr-1" height="16" alt="" onerror="this.src='{{ $fallbackFlag }}'" />
                   </a>
                   <div class="dropdown-menu dropdown-menu-right language-switch">
                       <a class="dropdown-item {{ $currentLocale === 'en' ? 'active' : '' }}" href="{{ route('language.switch', 'en') }}">
                           <img src="{{ asset('assets/images/flags/us_flag.jpg') }}" alt="" height="16" /> <span> English </span>
                       </a>
                       <a class="dropdown-item {{ $currentLocale === 'id' ? 'active' : '' }}" href="{{ route('language.switch', 'id') }}">
                           <img src="{{ $indonesiaFlag }}" alt="" height="16" onerror="this.src='{{ $fallbackFlag }}'" /> <span> Indonesian </span>
                       </a>
                   </div>
               </li>

               <li class="dropdown notification-list">
                   <div class="dropdown notification-list nav-pro-img">
                       <a class="dropdown-toggle nav-link arrow-none waves-effect nav-user" data-toggle="dropdown" href="#" role="button" aria-haspopup="false" aria-expanded="false">
                           <?php
                            $user = auth('employee')->check() ? auth('employee')->user() : auth()->user();
                            $photoUrl = $user && $user->photo ? asset('storage/profiles/' . $user->photo) : asset('assets/images/profile1.jpg');
                            ?>
                           <img src="{{ $photoUrl }}" alt="user" class="rounded-circle">
                       </a>
                       <div class="dropdown-menu dropdown-menu-right profile-dropdown ">
                           <!-- item-->
                           <a class="dropdown-item" href="{{ route('profile.show') }}"><i class="mdi mdi-account-circle m-r-5"></i> Profile</a>

                           {{-- <a class="dropdown-item d-block" href="#"><span class="badge badge-success float-right">11</span><i class="mdi mdi-settings m-r-5"></i> Settings</a> --}}
                           <a class="dropdown-item" href="#"><i class="mdi mdi-lock-open-outline m-r-5"></i> Lock screen</a>
                           <div class="dropdown-divider"></div>
                           <?php if (auth('employee')->check()): ?>
                               <a class="dropdown-item text-danger logout-link" href="javascript:void(0);"
                                   data-form-id="logout-form-header-employee"><i class="mdi mdi-power text-danger"></i> <?php echo e(__('Logout')); ?></a>
                               <form id="logout-form-header-employee" action="<?php echo e(route('employee.logout')); ?>" method="POST" style="display: none;">
                                   <?php echo csrf_field(); ?>
                               </form>
                           <?php elseif (auth('web')->check()): ?>
                               <a class="dropdown-item text-danger logout-link" href="javascript:void(0);"
                                   data-form-id="logout-form-header-admin"><i class="mdi mdi-power text-danger"></i> <?php echo e(__('Logout')); ?></a>
                               <form id="logout-form-header-admin" action="<?php echo e(route('logout')); ?>" method="POST" style="display: none;">
                                   <?php echo csrf_field(); ?>
                               </form>
                           <?php endif; ?>
                       </div>
                   </div>
               </li>

           </ul>

           <ul class="list-inline menu-left mb-0">
               <li class="float-left">
                   <button class="button-menu-mobile open-left waves-effect">
                       <i class="mdi mdi-menu"></i>
                   </button>
               </li>
               {{-- <li class="d-none d-sm-block">
            <div class="dropdown pt-3 d-inline-block">
                <a class="btn btn-light dropdown-toggle" href="#" role="button" id="dropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        Create
                    </a>

                <div class="dropdown-menu" aria-labelledby="dropdownMenuLink">
                    <a class="dropdown-item" href="#">Action</a>
                    <a class="dropdown-item" href="#">Another action</a>
                    <a class="dropdown-item" href="#">Something else here</a>
                    <div class="dropdown-divider"></div>
                    <a class="dropdown-item" href="#">Separated link</a>
                </div>
            </div>
        </li> --}}
           </ul>

       </nav>

   </div>
   <!-- Top Bar End -->