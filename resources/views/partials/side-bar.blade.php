<aside class="main-sidebar sidebar-dark-primary elevation-4">
    <!-- Brand Logo -->
    <a href="#" class="brand-link">
        <img src="{{ asset('assets/images/LOGO_KKI.png') }}" alt="AdminLTE Logo"
            class="brand-image img-circle elevation-3">
        <span class="brand-text font-weight-bold ml-2">Kopkar Indcement</span>
    </a>

    <!-- Sidebar -->
    <div class="sidebar">
        <!-- Sidebar Menu -->
        <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu"
                data-accordion="false">

                <!-- Dashboard -->
                <li class="nav-item">
                    <a href="/" class="nav-link {{ request()->is('/') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-tachometer-alt"></i>
                        <p>Dashboard</p>
                    </a>
                </li>

                @if (Auth::user()->role == 1 || Auth::user()->role == 2)
                    <li class="nav-header">ENTITY</li>
                    <li class="nav-item">
                        <a href="/user" class="nav-link {{ request()->is('user*') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-users"></i>
                            <p>Employees</p>
                        </a>
                    </li>

                    <li class="nav-item">
                        <a href="/department" class="nav-link {{ request()->is('department*') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-building"></i>
                            <p>Departments</p>
                        </a>
                    </li>

                    <li class="nav-item">
                        <a href="/depuser" class="nav-link {{ request()->is('depuser*') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-landmark"></i>
                            <p>Departments Employees</p>
                        </a>
                    </li>
                @endif


                @php
                    use App\Models\WorkingList;
                    $count_request = WorkingList::where('status', 'Requested')->count();
                @endphp

                @if (Auth::user()->access_worklist == true)
                    <!-- Working List Section -->
                    <li class="nav-header">WORKING LIST</li>
                    <li
                        class="nav-item {{ request()->is('working-list*') || request()->is('need_approval*') ? 'menu-is-opening menu-open' : '' }}">
                        <a href="#"
                            class="nav-link {{ request()->is('working-list*') || request()->is('need_approval*') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-clipboard-list"></i>
                            <p>Working Lists <i class="right fas fa-angle-left"></i></p>
                        </a>
                        <ul class="nav nav-treeview">
                            <li class="nav-item">
                                <a href="/working-list"
                                    class="nav-link {{ request()->is('working-list') ? 'active' : '' }}">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>Data</p>
                                </a>
                            </li>



                            @if (Auth::user()->role == 1 || Auth::user()->role == 2)
                                @php
                                    if (Auth::user()->role == 1) {
                                        $count_request_department = WorkingList::where('status', 'Requested')->count();
                                    } else {
                                        $departmentIds = \App\Models\DepartmenUser::where(
                                            'user_id',
                                            '=',
                                            Auth::user()->id,
                                        )->pluck('unit_id');

                                        if ($departmentIds->isNotEmpty()) {
                                            $count_request_department = WorkingList::whereIn(
                                                'department_id',
                                                $departmentIds,
                                            )
                                                ->where('status', 'Requested')
                                                ->count();
                                        } else {
                                            $count_request_department = 0; // Jika tidak ada departmentIds, set ke 0
                                        }
                                    }
                                @endphp
                                <li class="nav-item">
                                    <a href="/need_approval"
                                        class="nav-link {{ request()->is('need_approval') ? 'active' : '' }}">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>Need Approval <span class="badge badge-danger">{{ $count_request_department }}</span></p>
                                    </a>
                                </li>
                            @endif
                        </ul>
                    </li>
                @endif

                @if (Auth::user()->access_control_budget == true)
                    <!-- Cost Review Section -->
                    <li class="nav-header">COST REVIEW</li>                    

                    <li
                        class="nav-item {{ request()->is('cost-review*')  ? 'menu-is-opening menu-open' : '' }}">
                        <a href="#"
                            class="nav-link {{ request()->is('cost-review*') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-money-bill-wave"></i>
                            <p>Department Budget <i class="right fas fa-angle-left"></i></p>
                        </a>
                        <ul class="nav nav-treeview">
                            <li class="nav-item">
                                <a href="/cost-review"
                                    class="nav-link {{ request()->is('cost-review') ? 'active' : '' }}">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>Budget</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="/cost-review/consolidated"
                                    class="nav-link {{ request()->is('cost-review/consolidated') ? 'active' : '' }}">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>Consolidated</p>
                                </a>
                            </li>
                        </ul>
                    </li>
                @endif

                @if (Auth::user()->access_payment_schedule == true)
                    <!-- KKI Mart Section -->
                    <li class="nav-header">KKI MART</li>
                    <li class="nav-item {{ request()->is('payment_schedule*') ? 'menu-is-opening menu-open' : '' }}">
                        <a href="#" class="nav-link {{ request()->is('payment_schedule*') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-calendar-alt"></i>
                            <p>Payment Schedule <i class="right fas fa-angle-left"></i></p>
                        </a>
                        <ul class="nav nav-treeview">
                            <li class="nav-item">
                                <a href="/payment_schedule"
                                    class="nav-link {{ request()->is('payment_schedule') ? 'active' : '' }}">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>Data</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="/payment_schedule/unpaid_recap"
                                    class="nav-link {{ request()->is('payment_schedule/unpaid_recap') ? 'active' : '' }}">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>Unpaid Recap</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="/payment_schedule/paid_recap"
                                    class="nav-link {{ request()->is('payment_schedule/paid_recap') ? 'active' : '' }}">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>Paid Recap</p>
                                </a>
                            </li>
                        </ul>
                    </li>
                @endif

                <!-- Logout -->
                <li class="nav-item">
                    <a href="/logout" class="nav-link logout-link">
                        <i class="nav-icon fas fa-power-off"></i>
                        <p>Logout</p>
                    </a>
                </li>
            </ul>
        </nav>
    </div>
</aside>
