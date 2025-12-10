      <!-- ========== Left Sidebar Start ========== -->
      <div class="left side-menu">
          <div class="slimscroll-menu" id="remove-scroll">

              <!--- Sidemenu -->
              <div id="sidebar-menu">

                  <!-- Left Menu Start -->
                  <ul class="metismenu" id="side-menu">
                      <li class="menu-title">
                          <?php if (auth('employee')->check()): ?>
                              <?php echo e(auth('employee')->user()->name); ?>
                          <?php elseif (auth('web')->check()): ?>
                              <?php echo e(auth()->user()->name); ?>
                          <?php else: ?>
                              <?php echo e(__('global.menu.main')); ?>
                          <?php endif; ?>
                      </li>

                      <!-- Dashboard -->
                      <li class="">
                          <a href="{{ route('admin') }}" class="waves-effect {{ request()->is("admin") || request()->is("admin/*") ? "mm active" : "" }}">
                              <?php if (getSetting('dashboard_icon_type', 'predefined') === 'upload' && !empty(getSetting('dashboard_icon'))): ?>
                                  <img src="<?php echo e(asset(getSetting('dashboard_icon'))); ?>" alt="<?php echo e(__('global.menu.dashboard')); ?>" style="width: 16px; height: 16px; margin-right: 8px;">
                              <?php else: ?>
                                  <i class="<?php echo e(getSetting('dashboard_icon', 'ti-home')); ?>"></i>
                              <?php endif; ?>
                              <span> {{ getSetting('dashboard_name', __('global.menu.dashboard')) }} </span>
                          </a>
                      </li>

                      <?php
                        $user = auth('employee')->check() ? auth('employee')->user() : auth('web')->user();
                        $isEmployee = auth('employee')->check();
                        $employeePosition = $isEmployee ? $user->position : null;

                        // Handle different role relationships
                        if ($user) {
                            if ($isEmployee) {
                                // Employee has direct role relationship
                                $role = $user->role;
                                $userRoles = $role ? [$role->slug] : ['employee'];
                                // Add position-based roles for employees
                                if ($employeePosition) {
                                    if (strpos($employeePosition, 'Section ') === 0) {
                                        $userRoles[] = 'section';
                                    } elseif (in_array($employeePosition, ['Wadir 1', 'Wadir 2'])) {
                                        $userRoles[] = 'wadir';
                                    } elseif ($employeePosition === 'SDM/HRD') {
                                        $userRoles[] = 'SDM/HRD';
                                        $userRoles[] = 'admin_sdm'; // Also treat as admin_sdm for compatibility
                                    }
                                }
                            } else {
                                // User has many-to-many role relationship - get ALL roles
                                // Check if user has roles method (for User model)
                                if (method_exists($user, 'roles') && $user instanceof \App\Models\User) {
                                    $userRoles = $user->roles()->pluck('slug')->toArray();
                                } else {
                                    $userRoles = ['employee'];
                                }
                            }
                        } else {
                            $userRoles = ['employee'];
                        }

                        // Helper function to check if user has any of the required roles
                        $hasRole = function ($requiredRoles) use ($userRoles, $employeePosition) {
                            // Check role-based access
                            if (!empty(array_intersect($requiredRoles, $userRoles))) {
                                return true;
                            }
                            // Also check position for employees (for SDM/HRD)
                            if ($employeePosition && in_array('SDM/HRD', $requiredRoles) && $employeePosition === 'SDM/HRD') {
                                return true;
                            }
                            return false;
                        };
                        ?>

                      <?php if ($hasRole(['super_admin', 'admin_sdm'])): ?>
                          <li class="menu-title"><?php echo e(__('global.menu.master_data')); ?></li>

                          <li>
                              <a href="/employees" class="waves-effect <?php echo e(request()->is("employees") || request()->is("employees/*") ? "mm active" : ""); ?>">
                                  <i class="ti-user"></i><span> <?php echo e(__('global.menu.employees')); ?> </span>
                              </a>
                          </li>

                          <?php if ($hasRole(['super_admin'])): ?>
                              <li>
                                  <a href="/admin-management" class="waves-effect <?php echo e(request()->is("admin-management") || request()->is("admin-management/*") ? "mm active" : ""); ?>">
                                      <i class="mdi mdi-account-supervisor"></i><span> <?php echo e(__('global.menu.admin_management')); ?> </span>
                                  </a>
                              </li>
                          <?php endif; ?>
                      <?php endif; ?>

                      <!-- Attendance Section - Super Admin, Admin SDM, Wadir, Employee (but not Section, Wadir, SDM/HRD) -->
                      <?php
                        $showAttendanceMenu = false;
                        if ($isEmployee) {
                            $position = $employeePosition ?? '';
                            // Show attendance menu only for regular employees, Magang, PKL
                            // Hide for Section, Wadir, SDM/HRD
                            if (strpos($position, 'Section ') === 0 || in_array($position, ['Wadir 1', 'Wadir 2']) || $position === 'SDM/HRD') {
                                $showAttendanceMenu = false;
                            } else {
                                $showAttendanceMenu = true;
                            }
                        } else {
                            // Admin: show attendance menu
                            $showAttendanceMenu = $hasRole(['super_admin', 'admin_sdm']);
                        }
                        ?>
                      <?php if ($showAttendanceMenu): ?>
                          <li class="menu-title"><?php echo e(__('global.menu.attendance')); ?></li>

                          <li class="">
                              <a href="/check" class="waves-effect <?php echo e(request()->is("check") || request()->is("check/*") ? "mm active" : ""); ?>">
                                  <i class="dripicons-to-do"></i> <span> <?php echo e(__('global.menu.attendance_sheet')); ?> </span>
                              </a>
                          </li>
                          <li class="">
                              <a href="/attendance" class="waves-effect <?php echo e(request()->is("attendance") || request()->is("attendance/*") ? "mm active" : ""); ?>">
                                  <i class="ti-calendar"></i> <span> <?php echo e(__('global.menu.attendance_logs')); ?> </span>
                              </a>
                          </li>
                      <?php endif; ?>

                      <!-- Overtime Section - All roles except none -->
                      <?php if ($hasRole(['super_admin', 'admin_sdm', 'wadir', 'section', 'employee'])): ?>
                          <li class="menu-title"><?php echo e(__('global.menu.overtime')); ?></li>

                          <li>
                              <a href="javascript:void(0);" class="waves-effect">
                                  <i class="dripicons-alarm"></i><span> <?php echo e(__('global.menu.overtime')); ?> <span class="float-right menu-arrow"><i class="mdi mdi-chevron-right"></i></span> </span>
                              </a>
                              <ul class="submenu">
                                  <?php
                                    // Only show Overtime Request for employees and magang
                                    $showOvertimeRequest = false;
                                    if ($isEmployee) {
                                        $position = $employeePosition ?? '';
                                        // Only show for Employees, Magang, PKL
                                        if (in_array($position, ['Employees', 'Magang', 'PKL'])) {
                                            $showOvertimeRequest = true;
                                        }
                                    } else {
                                        // Admin: don't show (only employees/magang can request)
                                        $showOvertimeRequest = false;
                                    }
                                    ?>
                                  <?php if ($showOvertimeRequest): ?>
                                      <li>
                                          <a href="/overtime/requests" class="waves-effect <?php echo e(request()->is("overtime/requests") || request()->is("overtime/requests/*") ? "mm active" : ""); ?>">
                                              <i class="dripicons-document"></i><span><?php echo e(__('global.menu.overtime_request')); ?></span>
                                          </a>
                                      </li>
                                  <?php endif; ?>

                                  <?php if ($hasRole(['super_admin', 'admin_sdm', 'wadir', 'section', 'SDM/HRD'])): ?>
                                      <li>
                                          <a href="/overtime/approvals" class="waves-effect <?php echo e(request()->is("overtime/approvals") || request()->is("overtime/approvals/*") ? "mm active" : ""); ?>">
                                              <i class="dripicons-checkmark"></i><span><?php echo e(__('global.menu.overtime_approvals')); ?></span>
                                          </a>
                                      </li>
                                  <?php endif; ?>

                                  <li>
                                      <a href="/reports/overtime" class="waves-effect <?php echo e(request()->is("reports/overtime") || request()->is("reports/overtime/*") ? "mm active" : ""); ?>">
                                          <i class="dripicons-graph-line"></i><span><?php echo e(__('global.menu.overtime_reports')); ?></span>
                                      </a>
                                  </li>
                              </ul>
                          </li>
                      <?php endif; ?>

                      <!-- Leaves Section - All roles except none -->
                      <?php if ($hasRole(['super_admin', 'admin_sdm', 'wadir', 'section', 'employee'])): ?>
                          <li class="menu-title"><?php echo e(__('global.menu.leaves_cuti_tunjangan')); ?></li>

                          <li class="">
                              <a href="/leave" class="waves-effect <?php echo e(request()->is("leave") || request()->is("leave/*") ? "mm active" : ""); ?>">
                                  <i class="dripicons-calendar"></i> <span> <?php echo e(__('global.menu.leaves')); ?> </span>
                              </a>
                          </li>
                      <?php endif; ?>

                      <!-- Salaries Section - Super Admin, Admin SDM, Employee -->
                      <?php if ($hasRole(['super_admin', 'admin_sdm', 'employee'])): ?>
                          <li class="menu-title"><?php echo e(__('global.menu.salaries_slip_gaji')); ?></li>

                          <li class="">
                              <a href="/salaries" class="waves-effect <?php echo e(request()->is("salaries") || request()->is("salaries/*") ? "mm active" : ""); ?>">
                                  <i class="dripicons-document"></i> <span> <?php echo e(__('global.menu.salaries')); ?> </span>
                              </a>
                          </li>
                      <?php endif; ?>

                      <!-- Trainings Section - All roles except none -->
                      <?php if ($hasRole(['super_admin', 'admin_sdm', 'wadir', 'section', 'employee'])): ?>
                          <li class="menu-title"><?php echo e(__('global.menu.trainings')); ?></li>

                          <li class="">
                              <a href="/trainings" class="waves-effect <?php echo e(request()->is("trainings") || request()->is("trainings/*") ? "mm active" : ""); ?>">
                                  <i class="dripicons-graduation"></i> <span> <?php echo e(__('global.menu.trainings')); ?> </span>
                              </a>
                          </li>
                      <?php endif; ?>


                      <!-- Settings Section - Super Admin only -->
                      <?php if ($hasRole(['super_admin'])): ?>
                          <li class="menu-title"><?php echo e(__('global.menu.settings')); ?></li>

                          <li class="">
                              <a href="/settings" class="waves-effect <?php echo e(request()->is("settings") || request()->is("settings/*") ? "mm active" : ""); ?>">
                                  <i class="dripicons-gear"></i> <span> <?php echo e(__('global.menu.settings')); ?> </span>
                              </a>
                          </li>
                      <?php endif; ?>

                      <!-- Logout -->
                      <?php if (auth('employee')->check()): ?>
                          <li class="">
                              <a href="<?php echo e(route('employee.logout')); ?>" class="waves-effect logout-link"
                                  data-form-id="logout-form-employee">
                                  <i class="mdi mdi-logout"></i> <span> <?php echo e(__('global.logout')); ?> </span>
                              </a>
                              <form id="logout-form-employee" action="<?php echo e(route('employee.logout')); ?>" method="POST" style="display: none;">
                                  <?php echo csrf_field(); ?>
                              </form>
                          </li>
                      <?php elseif (auth('web')->check()): ?>
                          <li class="">
                              <a href="<?php echo e(route('logout')); ?>" class="waves-effect logout-link"
                                  data-form-id="logout-form-admin">
                                  <i class="mdi mdi-logout"></i> <span> <?php echo e(__('global.logout')); ?> </span>
                              </a>
                              <form id="logout-form-admin" action="<?php echo e(route('logout')); ?>" method="POST" style="display: none;">
                                  <?php echo csrf_field(); ?>
                              </form>
                          </li>
                      <?php endif; ?>
                  </ul>

              </div>

              <div class="clearfix"></div>

          </div>
          <!-- Sidebar -left -->

      </div>
      <!-- Left Sidebar End -->