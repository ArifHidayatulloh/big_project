<!-- Main Sidebar Container -->
<aside class="main-sidebar sidebar-dark-primary elevation-4">
    <!-- Brand Logo -->
    <a href="" class="brand-link text-center">
        <span class="brand-text font-weight-light">Koperasi Indocement</span>
    </a>

    <!-- Sidebar -->
    <div class="sidebar">
        <!-- Sidebar Menu -->
        <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
                <!-- Dashboard -->
                <li class="nav-item menu">
                    <a href="/dashboard" class="nav-link {{ request()->is('/') ? 'bg-secondary' : '' }}">
                        <i class="nav-icon fas fa-tachometer-alt"></i>
                        <p>
                            Dashboard
                        </p>
                    </a>
                </li>

                @if (Auth::user()->role == 1 || Auth::user()->role == 2)
                    {{-- Entity --}}
                    <li class="nav-header">ENTITY</li>

                    <li class="nav-item">
                        <a href="/user" class="nav-link {{ request()->is('user*') ? 'bg-secondary' : '' }}">
                            <i class="nav-icon fas fa-users"></i>
                            <p>
                                Employess
                            </p>
                        </a>
                    </li>

                    <li class="nav-item">
                        <a href="/department" class="nav-link {{ request()->is('department*') ? 'bg-secondary' : '' }}">
                            <i class="nav-icon fas fa-building"></i>
                            <p>
                                Departments
                            </p>
                        </a>
                    </li>

                    <li class="nav-item">
                        <a href="/depuser" class="nav-link {{ request()->is('depuser*') ? 'bg-secondary' : '' }}">
                            <i class="nav-icon fas fa-landmark"></i>
                            <p>
                                Departments Employees
                            </p>
                        </a>
                    </li>

                    {{-- End of Entity --}}
                @endif


                {{-- Working List --}}
                <li class="nav-header">Working List</li>

                <li class="nav-item">
                    <a href="/working-lists"
                        class="nav-link {{ request()->is('working-lists') ? 'bg-secondary' : '' }}">
                        <i class="nav-icon fas fa-clipboard"></i>
                        <p>
                            Working Lists
                        </p>
                    </a>
                </li>

                <li class="nav-item">
                    <a href="#" class="nav-link {{ request()->is('working-lists/*') ? 'bg-secondary' : '' }}">
                        <i class="nav-icon fas fa-copy"></i>
                        <p>
                            Working Lists
                            <i class="fas fa-angle-left right"></i>
                            <span class="badge badge-info right">6</span>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="/working-lists/data"
                                class="nav-link {{ request()->is('working-lists/data') ? 'bg-secondary' : '' }}">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Work List Data</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="/working-lists/status-change"
                                class="nav-link {{ request()->is('working-lists/status-change') ? 'bg-secondary' : '' }}">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Status Change Request</p>
                            </a>
                        </li>
                    </ul>
                </li>

                {{-- End of Working List --}}

                {{-- Control Budget --}}
                <li class="nav-header">Control Budget</li>

                <li class="nav-item">
                    <a href="/control-budget"
                        class="nav-link {{ request()->is('control-budget*') ? 'bg-secondary' : '' }}">
                        <i class="nav-icon fas fa-money-bill-wave"></i>
                        <p>
                            Departments Budgets
                        </p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="/logout"
                        class="nav-link">
                        <i class="nav-icon fas fa-power-off"></i>
                        <p>
                            Logout
                        </p>
                    </a>
                </li>
                {{-- End of Control Budget --}}
            </ul>
        </nav>
        <!-- /.sidebar-menu -->
    </div>
    <!-- /.sidebar -->
</aside>
