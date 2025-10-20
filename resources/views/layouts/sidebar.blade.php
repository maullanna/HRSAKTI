      <!-- ========== Left Sidebar Start ========== -->
            <div class="left side-menu">
                <div class="slimscroll-menu" id="remove-scroll">

                    <!--- Sidemenu -->
                    <div id="sidebar-menu">
                        
                        <!-- Left Menu Start -->
                        <ul class="metismenu" id="side-menu">
                            <li class="menu-title">
                                @if(Auth::guard('employee')->check())
                                    {{ Auth::guard('employee')->user()->name }}
                                @elseif(Auth::guard('web')->check())
                                    {{ Auth::user()->name }}
                                @else
                                    Main
                                @endif
                            </li>
                            
                            <!-- Dashboard -->
                            <li class="">
                                <a href="{{ route('admin') }}" class="waves-effect {{ request()->is("admin") || request()->is("admin/*") ? "mm active" : "" }}">
                                    @if(getSetting('dashboard_icon_type', 'predefined') === 'upload')
                                        <img src="{{ asset(getSetting('dashboard_icon', 'mdi mdi-view-dashboard')) }}" alt="Dashboard" style="width: 16px; height: 16px; margin-right: 8px;">
                                    @else
                                        <i class="{{ getSetting('dashboard_icon', 'ti-home') }}"></i>
                                    @endif
                                    <span> {{ getSetting('dashboard_name', 'Dashboard') }} </span>
                                </a>
                            </li>

                            @php
                                $user = Auth::guard('employee')->check() ? Auth::guard('employee')->user() : Auth::guard('web')->user();
                                
                                // Handle different role relationships
                                if ($user) {
                                    if (Auth::guard('employee')->check()) {
                                        // Employee has direct role relationship
                                        $role = $user->role;
                                        $userRoles = $role ? [$role->slug] : ['employee'];
                                    } else {
                                        // User has many-to-many role relationship - get ALL roles
                                        $userRoles = $user->roles()->pluck('slug')->toArray();
                                    }
                                } else {
                                    $userRoles = ['employee'];
                                }
                                
                                // Helper function to check if user has any of the required roles
                                $hasRole = function($requiredRoles) use ($userRoles) {
                                    return !empty(array_intersect($requiredRoles, $userRoles));
                                };
                            @endphp

                            <!-- Master Data Section - Super Admin & Admin SDM only -->
                            @if($hasRole(['super_admin', 'admin_sdm']))
                            <li class="menu-title">Master Data</li>
                            
                            <li>
                                <a href="/employees" class="waves-effect {{ request()->is("employees") || request()->is("employees/*") ? "mm active" : "" }}">
                                    <i class="ti-user"></i><span> Employees </span>
                                </a>
                            </li>
                            @endif

                            <!-- Attendance Section - Super Admin, Admin SDM, Wadir, Employee -->
                            @if($hasRole(['super_admin', 'admin_sdm', 'wadir', 'employee']))
                            <li class="menu-title">Attendance</li>
                            
                            <li class="">
                                <a href="/check" class="waves-effect {{ request()->is("check") || request()->is("check/*") ? "mm active" : "" }}">
                                    <i class="dripicons-to-do"></i> <span> Attendance Sheet </span>
                                </a>
                            </li>
                            <li class="">
                                <a href="/attendance" class="waves-effect {{ request()->is("attendance") || request()->is("attendance/*") ? "mm active" : "" }}">
                                    <i class="ti-calendar"></i> <span> Attendance Logs </span>
                                </a>
                            </li>
                            @endif

                            <!-- Overtime Section - All roles except none -->
                            @if($hasRole(['super_admin', 'admin_sdm', 'wadir', 'section', 'employee']))
                            <li class="menu-title">Overtime</li>
                            
                            <li>
                                <a href="javascript:void(0);" class="waves-effect">
                                    <i class="dripicons-alarm"></i><span> Overtime <span class="float-right menu-arrow"><i class="mdi mdi-chevron-right"></i></span> </span>
                                </a>
                                <ul class="submenu">
                                    @if($hasRole(['super_admin', 'admin_sdm', 'wadir', 'section', 'YTI Board of Directors']))
                                    <li>
                                        <a href="/overtime/requests" class="waves-effect {{ request()->is("overtime/requests") || request()->is("overtime/requests/*") ? "mm active" : "" }}">
                                            <i class="dripicons-document"></i><span>Overtime Requests</span>
                                        </a>
                                    </li>
                                    @endif
                                    
                                    @if($hasRole(['super_admin', 'admin_sdm', 'wadir', 'YTI Board of Directors']))
                                    <li>
                                        <a href="/overtime/approvals" class="waves-effect {{ request()->is("overtime/approvals") || request()->is("overtime/approvals/*") ? "mm active" : "" }}">
                                            <i class="dripicons-checkmark"></i><span>Overtime Approvals</span>
                                        </a>
                                    </li>
                                    @endif
                                    
                                    <li>
                                        <a href="/reports/overtime" class="waves-effect {{ request()->is("reports/overtime") || request()->is("reports/overtime/*") ? "mm active" : "" }}">
                                            <i class="dripicons-graph-line"></i><span>Overtime Reports</span>
                                        </a>
                                    </li>
                                </ul>
                            </li>
                            @endif

                            <!-- Leaves Section - All roles except none -->
                            @if($hasRole(['super_admin', 'admin_sdm', 'wadir', 'section', 'employee']))
                            <li class="menu-title">Leaves (Cuti & Tunjangan)</li>
                            
                            <li class="">
                                <a href="/leave" class="waves-effect {{ request()->is("leave") || request()->is("leave/*") ? "mm active" : "" }}">
                                    <i class="dripicons-calendar"></i> <span> Leaves </span>
                                </a>
                            </li>
                            @endif

                            <!-- Salaries Section - Super Admin, Admin SDM, Employee -->
                            @if($hasRole(['super_admin', 'admin_sdm', 'employee']))
                            <li class="menu-title">Salaries (Slip Gaji)</li>
                            
                            <li class="">
                                <a href="/salaries" class="waves-effect {{ request()->is("salaries") || request()->is("salaries/*") ? "mm active" : "" }}">
                                    <i class="dripicons-document"></i> <span> Salaries </span>
                                </a>
                            </li>
                            @endif

                            <!-- Trainings Section - All roles except none -->
                            @if($hasRole(['super_admin', 'admin_sdm', 'wadir', 'section', 'employee']))
                            <li class="menu-title">Trainings</li>
                            
                            <li class="">
                                <a href="/trainings" class="waves-effect {{ request()->is("trainings") || request()->is("trainings/*") ? "mm active" : "" }}">
                                    <i class="dripicons-graduation"></i> <span> Trainings </span>
                                </a>
                            </li>
                            @endif


                            <!-- Settings Section - Super Admin only -->
                            @if($hasRole(['super_admin']))
                            <li class="menu-title">Settings</li>
                            
                            <li class="">
                                <a href="/settings" class="waves-effect {{ request()->is("settings") || request()->is("settings/*") ? "mm active" : "" }}">
                                    <i class="dripicons-gear"></i> <span> Settings </span>
                                </a>
                            </li>
                            @endif

                            <!-- Logout -->
                            @if(Auth::guard('employee')->check())
                            <li class="">
                                <a href="{{ route('employee.logout') }}" class="waves-effect" 
                                   onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                    <i class="mdi mdi-logout"></i> <span> Logout </span>
                                </a>
                                <form id="logout-form" action="{{ route('employee.logout') }}" method="POST" style="display: none;">
                                    @csrf
                                </form>
                            </li>
                            @elseif(Auth::guard('web')->check())
                            <li class="">
                                <a href="{{ route('logout') }}" class="waves-effect" 
                                   onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                    <i class="mdi mdi-logout"></i> <span> Logout </span>
                                </a>
                                <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                                    @csrf
                                </form>
                            </li>
                            @endif

                        </ul>

                    </div>
                    <!-- Sidebar -->
                    <div class="clearfix"></div>

                </div>
                <!-- Sidebar -left -->

            </div>
            <!-- Left Sidebar End -->
