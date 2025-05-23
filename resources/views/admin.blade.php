    <!DOCTYPE html>
    <html lang="en" class="light-style layout-navbar-fixed layout-menu-fixed layout-compact" dir="ltr" data-theme="theme-default" data-assets-path="{{ asset('assets/') }}/" data-template="vertical-menu-template">
    <head>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />
        <title>Admin Dashboard - @yield('title')</title>
        <meta name="description" content="" />

        <!-- Base URL for assets -->
        <base href="{{ url('/') }}/" />

        <!-- Favicon -->
        <link rel="icon" type="image/x-icon" href="{{ asset('assets/img/favicon/favicon.ico') }}" />

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.googleapis.com" />
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
        <link href="https://fonts.googleapis.com/css2?family=Public+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;1,300;1,400;1,500;1,600;1,700&display=swap" rel="stylesheet" />

        <!-- Icons -->
        <link rel="stylesheet" href="{{ asset('assets/vendor/fonts/fontawesome.css') }}" />
        <link rel="stylesheet" href="{{ asset('assets/vendor/fonts/tabler-icons.css') }}" />
        <link rel="stylesheet" href="{{ asset('assets/vendor/fonts/flag-icons.css') }}" />

        <!-- Core CSS -->
        <link rel="stylesheet" href="{{ asset('assets/vendor/css/rtl/core.css') }}" class="template-customizer-core-css" />
        <link rel="stylesheet" href="{{ asset('assets/vendor/css/rtl/theme-default.css') }}" class="template-customizer-theme-css" />
        <link rel="stylesheet" href="{{ asset('assets/css/demo.css') }}" />

        <!-- Vendors CSS -->
        <link rel="stylesheet" href="{{ asset('assets/vendor/libs/node-waves/node-waves.css') }}" />
        <link rel="stylesheet" href="{{ asset('assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.css') }}" />
        <link rel="stylesheet" href="{{ asset('assets/vendor/libs/typeahead-js/typeahead.css') }}" />
        <link rel="stylesheet" href="{{ asset('assets/vendor/libs/datatables-bs5/datatables.bootstrap5.css') }}" />
        <link rel="stylesheet" href="{{ asset('assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.css') }}" />
        <link rel="stylesheet" href="{{ asset('assets/vendor/libs/datatables-buttons-bs5/buttons.bootstrap5.css') }}" />
        <link rel="stylesheet" href="{{ asset('assets/vendor/libs/apex-charts/apex-charts.css') }}" />

        <!-- CSRF Token for AJAX -->
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <!-- Helpers -->
        <script src="{{ asset('assets/vendor/js/helpers.js') }}"></script>
        <script src="{{ asset('assets/vendor/js/template-customizer.js') }}"></script>
        <script src="{{ asset('assets/js/config.js') }}"></script>
    </head>
    <body>
        <!-- Layout wrapper -->
        <div class="layout-wrapper layout-content-navbar">
            <div class="layout-container">
                <!-- Menu -->
                <aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme">
                    <div class="app-brand demo">
                        <a href="{{ route('admin.dashboard') }}" class="app-brand-link">
                            <span class="app-brand-logo demo">
                                <svg width="32" height="22" viewBox="0 0 32 22" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path fill-rule="evenodd" clip-rule="evenodd" d="M0.00172773 0V6.85398C0.00172773 6.85398 -0.133178 9.01207 1.98092 10.8388L13.6912 21.9964L19.7809 21.9181L18.8042 9.88248L16.4951 7.17289L9.23799 0H0.00172773Z" fill="#7367F0" />
                                    <path opacity="0.06" fill-rule="evenodd" clip-rule="evenodd" d="M7.69824 16.4364L12.5199 3.23696L16.5541 7.25596L7.69824 16.4364Z" fill="#161616" />
                                    <path opacity="0.06" fill-rule="evenodd" clip-rule="evenodd" d="M8.07751 15.9175L13.9419 4.63989L16.5849 7.28475L8.07751 15.9175Z" fill="#161616" />
                                    <path fill-rule="evenodd" clip-rule="evenodd" d="M7.77295 16.3566L23.6563 0H32V6.88383C32 6.88383 31.8262 9.17836 30.6591 10.4057L19.7824 22H13.6938L7.77295 16.3566Z" fill="#7367F0" />
                                </svg>
                            </span>
                            <span class="app-brand-text demo menu-text fw-bold">Lemonade</span>
                        </a>
                    </div>
                    <div class="menu-inner-shadow"></div>
                    <ul class="menu-inner py-1">
                        <li class="menu-item {{ Route::is('admin.dashboard') || Route::is('admin.users.*') || Route::is('admin.subscriptions.*') || Route::is('admin.coupons.*') || Route::is('admin.boostinteractions.*') ? 'active open' : '' }}">
                            <a href="javascript:void(0);" class="menu-link menu-toggle">
                                <i class="menu-icon tf-icons ti ti-smart-home"></i>
                                <div data-i18n="Dashboards">Dashboard</div>
                            </a>
                            <ul class="menu-sub">
                                <li class="menu-item {{ Route::is('admin.users.*') ? 'active' : '' }}">
                                    <a href="{{ route('admin.users.index') }}" class="menu-link">
                                        <div data-i18n="Users">Users</div>
                                    </a>
                                </li>
                                <li class="menu-item {{ Route::is('admin.subscriptions.index') || Route::is('admin.subscriptions.create') || Route::is('admin.subscriptions.edit') ? 'active' : '' }}">
                                    <a href="{{ route('admin.subscriptions.index') }}" class="menu-link">
                                        <div data-i18n="Subscriptions">Subscriptions</div>
                                    </a>
                                </li>
                                <li class="menu-item {{ Route::is('admin.subscriptions.active') || Route::is('admin.subscriptions.active.create') || Route::is('admin.subscriptions.active.edit') ? 'active' : '' }}">
                                    <a href="{{ route('admin.subscriptions.active') }}" class="menu-link">
                                        <div data-i18n="Active Subscriptions">Active Subscriptions</div>
                                    </a>
                                </li>
                                <li class="menu-item {{ Route::is('admin.coupons.index') || Route::is('admin.coupons.create') || Route::is('admin.coupons.edit') ? 'active' : '' }}">
                                    <a href="{{ route('admin.coupons.index') }}" class="menu-link">
                                        <div data-i18n="Promo Codes">Promo Codes</div>
                                    </a>
                                </li>
                                <li class="menu-item {{ Route::is('admin.boostinteractions.index') ? 'active' : '' }}">
                                    <a href="{{ route('admin.boostinteractions.index') }}" class="menu-link">
                                        <div data-i18n="Boost Interactions">Boost Interactions</div>
                                    </a>
                                </li>
                            </ul>
                        </li>
                    </ul>
                </aside>
                <!-- / Menu -->

                <!-- Layout container -->
                <div class="layout-page">
                    <!-- Navbar -->
                    <nav class="layout-navbar container-xxl navbar navbar-expand-xl navbar-detached align-items-center bg-navbar-theme" id="layout-navbar">
                        <div class="layout-menu-toggle navbar-nav align-items-xl-center me-3 me-xl-0 d-xl-none">
                            <a class="nav-item nav-link px-0 me-xl-4" href="javascript:void(0)">
                                <i class="ti ti-menu-2 ti-sm"></i>
                            </a>
                        </div>
                        <div class="navbar-nav-right d-flex align-items-center" id="navbar-collapse">
                            <!-- Search -->
                            <div class="navbar-nav align-items-center">
                                <div class="nav-item navbar-search-wrapper mb-0">
                                    <a class="nav-item nav-link search-toggler d-flex align-items-center px-0" href="javascript:void(0);">
                                        <i class="ti ti-search ti-md me-2"></i>
                                        <span class="d-none d-md-inline-block text-muted">Search (Ctrl+/)</span>
                                    </a>
                                </div>
                            </div>
                            <!-- /Search -->
                            <ul class="navbar-nav flex-row align-items-center ms-auto">
                                <!-- Language -->
                                <li class="nav-item dropdown-language dropdown me-2 me-xl-0">
                                    <a class="nav-link dropdown-toggle hide-arrow" href="javascript:void(0);" data-bs-toggle="dropdown">
                                        <i class="ti ti-language rounded-circle ti-md"></i>
                                    </a>
                                    <ul class="dropdown-menu dropdown-menu-end">
                                        <li><a class="dropdown-item" href="javascript:void(0);" data-language="en"><span class="align-middle">English</span></a></li>
                                        <li><a class="dropdown-item" href="javascript:void(0);" data-language="fr"><span class="align-middle">French</span></a></li>
                                        <li><a class="dropdown-item" href="javascript:void(0);" data-language="de"><span class="align-middle">German</span></a></li>
                                        <li><a class="dropdown-item" href="javascript:void(0);" data-language="pt"><span class="align-middle">Portuguese</span></a></li>
                                    </ul>
                                </li>
                                <!--/ Language -->
                                <!-- Style Switcher -->
                                <li class="nav-item dropdown-style-switcher dropdown me-2 me-xl-0">
                                    <a class="nav-link dropdown-toggle hide-arrow" href="javascript:void(0);" data-bs-toggle="dropdown">
                                        <i class="ti ti-md"></i>
                                    </a>
                                    <ul class="dropdown-menu dropdown-menu-end dropdown-styles">
                                        <li><a class="dropdown-item" href="javascript:void(0);" data-theme="light"><span class="align-middle"><i class="ti ti-sun me-2"></i>Light</span></a></li>
                                        <li><a class="dropdown-item" href="javascript:void(0);" data-theme="dark"><span class="align-middle"><i class="ti ti-moon me-2"></i>Dark</span></a></li>
                                        <li><a class="dropdown-item" href="javascript:void(0);" data-theme="system"><span class="align-middle"><i class="ti ti-device-desktop me-2"></i>System</span></a></li>
                                    </ul>
                                </li>
                                <!-- / Style Switcher-->
                                <!-- Quick links -->
                                <li class="nav-item dropdown-shortcuts navbar-dropdown dropdown me-2 me-xl-0">
                                    <a class="nav-link dropdown-toggle hide-arrow" href="javascript:void(0);" data-bs-toggle="dropdown" data-bs-auto-close="outside" aria-expanded="false">
                                        <i class="ti ti-layout-grid-add ti-md"></i>
                                    </a>
                                    <div class="dropdown-menu dropdown-menu-end py-0">
                                        <div class="dropdown-menu-header border-bottom">
                                            <div class="dropdown-header d-flex align-items-center py-3">
                                                <h5 class="text-body mb-0 me-auto">Shortcuts</h5>
                                                <a href="javascript:void(0)" class="dropdown-shortcuts-add text-body" data-bs-toggle="tooltip" data-bs-placement="top" title="Add shortcuts"><i class="ti ti-sm ti-apps"></i></a>
                                            </div>
                                        </div>
                                        <div class="dropdown-shortcuts-list scrollable-container">
                                            <div class="row row-bordered overflow-visible g-0">
                                                <div class="dropdown-shortcuts-item col">
                                                    <span class="dropdown-shortcuts-icon rounded-circle mb-2"><i class="ti ti-calendar fs-4"></i></span>
                                                    <a href="javascript:void(0)" class="stretched-link">Calendar</a>
                                                    <small class="text-muted mb-0">Appointments</small>
                                                </div>
                                                <div class="dropdown-shortcuts-item col">
                                                    <span class="dropdown-shortcuts-icon rounded-circle mb-2"><i class="ti ti-file-invoice fs-4"></i></span>
                                                    <a href="javascript:void(0)" class="stretched-link">Invoice App</a>
                                                    <small class="text-muted mb-0">Manage Accounts</small>
                                                </div>
                                            </div>
                                            <div class="row row-bordered overflow-visible g-0">
                                                <div class="dropdown-shortcuts-item col">
                                                    <span class="dropdown-shortcuts-icon rounded-circle mb-2"><i class="ti ti-users fs-4"></i></span>
                                                    <a href="{{ route('admin.users.index') }}" class="stretched-link">User App</a>
                                                    <small class="text-muted mb-0">Manage Users</small>
                                                </div>
                                                <div class="dropdown-shortcuts-item col">
                                                    <span class="dropdown-shortcuts-icon rounded-circle mb-2"><i class="ti ti-lock fs-4"></i></span>
                                                    <a href="javascript:void(0)" class="stretched-link">Role Management</a>
                                                    <small class="text-muted mb-0">Permission</small>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </li>
                                <!-- Quick links -->
                                <!-- Notification -->
                                <li class="nav-item dropdown-notifications navbar-dropdown dropdown me-3 me-xl-1">
                                    <a class="nav-link dropdown-toggle hide-arrow" href="javascript:void(0);" data-bs-toggle="dropdown" data-bs-auto-close="outside" aria-expanded="false">
                                        <i class="ti ti-bell ti-md"></i>
                                        <span class="badge bg-danger rounded-pill badge-notifications">0</span>
                                    </a>
                                    <ul class="dropdown-menu dropdown-menu-end py-0">
                                        <li class="dropdown-menu-header border-bottom">
                                            <div class="dropdown-header d-flex align-items-center py-3">
                                                <h5 class="text-body mb-0 me-auto">Notification</h5>
                                                <a href="javascript:void(0)" class="dropdown-notifications-all text-body" data-bs-toggle="tooltip" data-bs-placement="top" title="Mark all as read"><i class="ti ti-mail-opened fs-4"></i></a>
                                            </div>
                                        </li>
                                        <li class="dropdown-notifications-list scrollable-container">
                                            <ul class="list-group list-group-flush"></ul>
                                        </li>
                                        <li class="dropdown-menu-footer border-top">
                                            <a href="javascript:void(0);" class="dropdown-item d-flex justify-content-center text-primary p-2 h-px-40 mb-1 align-items-center">
                                                View all notifications
                                            </a>
                                        </li>
                                    </ul>
                                </li>
                                <!--/ Notification -->
                                <!-- User -->
                                <li class="nav-item navbar-dropdown dropdown-user dropdown">
                                    <a class="nav-link dropdown-toggle hide-arrow" href="javascript:void(0);" data-bs-toggle="dropdown">
                                        <div class="avatar avatar-online">
                                            <i class="fas fa-user-tie"></i>
                                        </div>
                                    </a>
                                    <ul class="dropdown-menu dropdown-menu-end">
                                        <li>
                                            <a class="dropdown-item" href="javascript:void(0);">
                                                <div class="d-flex">
                                                    <div class="flex-shrink-0 me-3">
                                                        <div class="avatar avatar-online">
                                                            <i class="fas fa-user-tie"></i>
                                                        </div>
                                                    </div>
                                                    <div class="flex-grow-1">
                                                        <span class="fw-medium d-block">{{ auth()->user()->name }}</span>
                                                        <small class="text-muted">{{ ucfirst(auth()->user()->role) }}</small>
                                                    </div>
                                                </div>
                                            </a>
                                        </li>
                                        <li><div class="dropdown-divider"></div></li>
                                        <li><a class="dropdown-item" href="{{ route('admin.profile') }}"><i class="ti ti-user-check me-2 ti-sm"></i><span class="align-middle">Profile</span></a></li>
                                        <li><a class="dropdown-item" href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();"><i class="ti ti-logout me-2 ti-sm"></i><span class="align-middle">Log Out</span></a></li>
                                        <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                                            @csrf
                                        </form>
                                    </ul>
                                </li>
                                <!--/ User -->
                            </ul>
                        </div>
                        <!-- Search Small Screens -->
                        <div class="navbar-search-wrapper search-input-wrapper d-none">
                            <input type="text" class="form-control search-input container-xxl border-0" placeholder="Search..." aria-label="Search..." />
                            <i class="ti ti-x ti-sm search-toggler cursor-pointer"></i>
                        </div>
                    </nav>
                    <!-- / Navbar -->

                    <!-- Content wrapper -->
                    <div class="content-wrapper">
                        <!-- Content -->
                        <div class="container-xxl flex-grow-1 container-p-y">
                            @if (session('success'))
                                <div class="alert alert-success alert-dismissible" role="alert">
                                    {{ session('success') }}
                                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                </div>
                            @endif
                            @if (session('error'))
                                <div class="alert alert-danger alert-dismissible" role="alert">
                                    {{ session('error') }}
                                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                </div>
                            @endif

                            @section('dashboard')
                                @if (Route::is('admin.dashboard'))
                                    <div class="row">
                                        <!-- Active Users Card -->
                                        <div class="col-12 col-xl-6 mb-4">
                                            <div class="card h-100">
                                                <div class="card-header d-flex align-items-center justify-content-between">
                                                    <h5 class="card-title m-0 me-2">Active Users (Last Hour)</h5>
                                                    <div class="dropdown">
                                                        <button class="btn p-0" type="button" id="activeUsers" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                            <i class="ti ti-dots-vertical"></i>
                                                        </button>
                                                        <div class="dropdown-menu dropdown-menu-end" aria-labelledby="activeUsers">
                                                            <a class="dropdown-item" href="javascript:void(0);">Last 24 Hours</a>
                                                            <a class="dropdown-item" href="javascript:void(0);">Last Week</a>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="card-body row g-3">
                                                    <div class="col-md-6">
                                                        <div id="activeUsersChart"></div>
                                                    </div>
                                                    <div class="col-md-6 d-flex justify-content-around align-items-center">
                                                        <div>
                                                            @php
                                                                $activeUsers = \App\Models\User::getActiveUsersCount();
                                                                $totalUsers = \App\Models\User::count();
                                                                $activePercentage = $totalUsers > 0 ? round(($activeUsers / $totalUsers) * 100, 2) : 0;
                                                                $inactivePercentage = $totalUsers > 0 ? round(100 - $activePercentage, 2) : 0;
                                                            @endphp
                                                            <div class="d-flex align-items-baseline my-3">
                                                                <span class="text-primary me-2"><i class="ti ti-circle-filled fs-6"></i></span>
                                                                <div>
                                                                    <p class="mb-2">Active Users</p>
                                                                    <h5>{{ $activePercentage }}%</h5>
                                                                </div>
                                                            </div>
                                                            <div class="d-flex align-items-baseline my-3">
                                                                <span class="text-secondary me-2"><i class="ti ti-circle-filled fs-6"></i></span>
                                                                <div>
                                                                    <p class="mb-2">Inactive Users</p>
                                                                    <h5>{{ $inactivePercentage }}%</h5>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Campaigns Created Today Card -->
                                        <div class="col-12 col-xl-6 mb-4">
                                            <div class="card h-100">
                                                <div class="card-header d-flex align-items-center justify-content-between">
                                                    <h5 class="card-title m-0 me-2">Campaigns Created Today</h5>
                                                    <div class="dropdown">
                                                        <button class="btn p-0" type="button" id="campaigns" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                            <i class="ti ti-dots-vertical"></i>
                                                        </button>
                                                        <div class="dropdown-menu dropdown-menu-end" aria-labelledby="campaigns">
                                                            <a class="dropdown-item" href="javascript:void(0);">This Week</a>
                                                            <a class="dropdown-item" href="javascript:void(0);">This Month</a>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="card-body row g-3">
                                                    <div class="col-md-6">
                                                        <div id="campaignsChart"></div>
                                                    </div>
                                                    <div class="col-md-6 d-flex justify-content-around align-items-center">
                                                        <div>
                                                            @php
                                                                $campaignsToday = \App\Models\LinkedinCampaign::getCampaignsCreatedTodayCount();
                                                                $totalCampaigns = \App\Models\LinkedinCampaign::count();
                                                                $todayPercentage = $totalCampaigns > 0 ? round(($campaignsToday / $totalCampaigns) * 100, 2) : 0;
                                                                $otherPercentage = $totalCampaigns > 0 ? round(100 - $todayPercentage, 2) : 0;
                                                            @endphp
                                                            <div class="d-flex align-items-baseline my-3">
                                                                <span class="text-success me-2"><i class="ti ti-circle-filled fs-6"></i></span>
                                                                <div>
                                                                    <p class="mb-2">Campaigns Today</p>
                                                                    <h5>{{ $todayPercentage }}%</h5>
                                                                </div>
                                                            </div>
                                                            <div class="d-flex align-items-baseline my-3">
                                                                <span class="text-warning me-2"><i class="ti ti-circle-filled fs-6"></i></span>
                                                                <div>
                                                                    <p class="mb-2">Other Campaigns</p>
                                                                    <h5>{{ $otherPercentage }}%</h5>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- ApexCharts JavaScript -->
                                    <script>
                                        document.addEventListener('DOMContentLoaded', function () {
                                            // Active Users Chart
                                            var activeUsersOptions = {
                                                chart: {
                                                    type: 'bar',
                                                    height: 200,
                                                    stacked: false,
                                                    toolbar: { show: false }
                                                },
                                                plotOptions: {
                                                    bar: {
                                                        horizontal: true
                                                    }
                                                },
                                                series: [{
                                                    data: [{{ $activePercentage }}, {{ $inactivePercentage }}]
                                                }],
                                                xaxis: {
                                                    categories: ['Active Users', 'Inactive Users'],
                                                    labels: {
                                                        formatter: function (val) {
                                                            return val + '%';
                                                        }
                                                    }
                                                },
                                                colors: ['#7367F0', '#666EA8'],
                                                tooltip: {
                                                    y: {
                                                        formatter: function (val) {
                                                            return val + '%';
                                                        }
                                                    }
                                                },
                                                dataLabels: {
                                                    enabled: false
                                                }
                                            };
                                            var activeUsersChart = new ApexCharts(document.querySelector("#activeUsersChart"), activeUsersOptions);
                                            activeUsersChart.render();

                                            // Campaigns Chart
                                            var campaignsOptions = {
                                                chart: {
                                                    type: 'bar',
                                                    height: 200,
                                                    stacked: false,
                                                    toolbar: { show: false }
                                                },
                                                plotOptions: {
                                                    bar: {
                                                        horizontal: true
                                                    }
                                                },
                                                series: [{
                                                    data: [{{ $todayPercentage }}, {{ $otherPercentage }}]
                                                }],
                                                xaxis: {
                                                    categories: ['Campaigns Today', 'Other Campaigns'],
                                                    labels: {
                                                        formatter: function (val) {
                                                            return val + '%';
                                                        }
                                                    }
                                                },
                                                colors: ['#28C76F', '#FF9F43'],
                                                tooltip: {
                                                    y: {
                                                        formatter: function (val) {
                                                            return val + '%';
                                                        }
                                                    }
                                                },
                                                dataLabels: {
                                                    enabled: false
                                                }
                                            };
                                            var campaignsChart = new ApexCharts(document.querySelector("#campaignsChart"), campaignsOptions);
                                            campaignsChart.render();
                                        });
                                    </script>
                                @endif
                            @show

    @section('users-index')
        @if (Route::is('admin.users.index'))
            <div class="card">
                <div class="card-header d-flex align-items-center justify-content-between">
                    <h5 class="card-title m-0 me-2">Users</h5>
                    <div class="d-flex align-items-center">
                        <form action="{{ route('admin.users.index') }}" method="GET" class="d-flex">
                            <input type="text" class="form-control me-2" name="search" placeholder="Search by name or email..." value="{{ request('search') }}" style="width: 200px;">
                            <button type="submit" class="btn btn-outline-primary me-2">Search</button>
                            <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary me-2">Clear</a>
                        </form>
                        <a href="{{ route('admin.users.create') }}" class="btn btn-primary">Create User</a>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table table-borderless border-top" id="usersTable">
                        <thead class="border-bottom">
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Role</th>
                                <th>Post Permission</th>
                                <th>Boost Permission</th>
                                <th>Suspension End</th>
                                <th>Last Activity</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($users as $user)
                                <tr>
                                    <td>{{ $user->id }}</td>
                                    <td>{{ $user->name }}</td>
                                    <td>{{ $user->email }}</td>
                                    <td>{{ $user->role }}</td>
                                    <td>
                                        <span class="{{ $user->post_perm ? 'text-success' : 'text-danger' }}">
                                            {{ $user->post_perm ? 'Yes' : 'No' }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="{{ $user->boost_perm ? 'text-success' : 'text-danger' }}">
                                            {{ $user->boost_perm ? 'Yes' : 'No' }}
                                        </span>
                                    </td>
                                    <td>{{ $user->suspend_end ? $user->suspend_end->format('Y-m-d') : 'None' }}</td>
                                    <td>{{ $user->last_activity ? $user->last_activity->format('Y-m-d H:i:s') : 'Never' }}</td>
                                    <td>
                                        <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-sm btn-outline-primary me-2">Edit</a>
                                        <form action="{{ route('admin.users.destroy', $user) }}" method="POST" style="display: inline;">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger me-2" onclick="return confirm('Are you sure?')">Delete</button>
                                        </form>
                                        <button type="button" class="btn btn-sm btn-outline-warning me-2 suspend-btn" data-bs-toggle="modal" data-bs-target="#suspendModal" data-user-id="{{ $user->id }}" data-user-name="{{ $user->name }}">
                                            Suspend
                                        </button>
                                        @if ($user->suspend_end)
                                            <form action="{{ route('admin.users.remove-suspension') }}" method="POST" style="display: inline;">
                                                @csrf
                                                <input type="hidden" name="user_id" value="{{ $user->id }}">
                                                <button type="submit" class="btn btn-sm btn-outline-success" onclick="return confirm('Are you sure you want to remove the suspension for {{ $user->name }}?')">Remove Suspension</button>
                                            </form>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="9" class="text-center">No users found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                    <div class="mt-3">
                    {{ $users->links('vendor.pagination.bootstrap-5') }}
    </div>
                </div>
            </div>

            <!-- Suspend Modal -->
            <div class="modal fade" id="suspendModal" tabindex="-1" aria-labelledby="suspendModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="suspendModalLabel">Suspend User</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <form id="suspendForm" action="{{ route('admin.users.suspend') }}" method="POST">
                                @csrf
                                <input type="hidden" name="user_id" id="suspendUserId">
                                <div class="mb-3">
                                    <label for="suspendDate" class="form-label">Select Suspension End Date</label>
                                    <input type="date" class="form-control" name="suspend_date" id="suspendDate" required min="{{ now()->format('Y-m-d') }}">
                                </div>
                                <p>Are you sure you want to suspend <strong id="suspendUserName"></strong> until the selected date?</p>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                    <button type="submit" class="btn btn-warning">Suspend</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <!-- JavaScript for Modal and DataTables -->
            <script>
                document.addEventListener('DOMContentLoaded', function () {
                    // Suspend Modal Logic
                    document.querySelectorAll('.suspend-btn').forEach(button => {
                        button.addEventListener('click', function() {
                            const userId = this.getAttribute('data-user-id');
                            const userName = this.getAttribute('data-user-name');
                            document.getElementById('suspendUserId').value = userId;
                            document.getElementById('suspendUserName').textContent = userName;
                            document.getElementById('suspendDate').value = '';
                        });
                    });

                    // Initialize DataTables
                    $('#usersTable').DataTable({
                        dom: 'frtip',
                        pageLength: 1, // One user per page
                        ordering: false, // Disable client-side sorting
                        searching: false, // Disable client-side searching
                        paging: false, // Disable DataTables pagination (use Laravel's)
                        info: false, // Hide "Showing X of Y entries"
                        language: {
                            search: '',
                            searchPlaceholder: 'Search users...'
                        }
                    });
                });
            </script>
        @endif
    @show

                            @section('users-create')
                                @if (Route::is('admin.users.create'))
                                    <div class="card">
                                        <div class="card-header">
                                            <h5 class="card-title mb-0">Create User</h5>
                                        </div>
                                        <div class="card-body">
                                            <form action="{{ route('admin.users.store') }}" method="POST">
                                                @csrf
                                                <div class="mb-3">
                                                    <label for="name" class="form-label">Name</label>
                                                    <input type="text" class="form-control" name="name" id="name" required>
                                                    @error('name')
                                                        <div class="text-danger mt-1">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                                <div class="mb-3">
                                                    <label for="email" class="form-label">Email</label>
                                                    <input type="email" class="form-control" name="email" id="email" required>
                                                    @error('email')
                                                        <div class="text-danger mt-1">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                                <div class="mb-3">
                                                    <label for="password" class="form-label">Password</label>
                                                    <input type="password" class="form-control" name="password" id="password" required>
                                                    @error('password')
                                                        <div class="text-danger mt-1">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                                <div class="mb-3">
                                                    <label for="password_confirmation" class="form-label">Confirm Password</label>
                                                    <input type="password" class="form-control" name="password_confirmation" id="password_confirmation" required>
                                                </div>
                                                <div class="mb-3">
                                                    <label for="role" class="form-label">Role</label>
                                                    <select class="form-select" name="role" id="role" required>
                                                        <option value="user">User</option>
                                                        <option value="admin">Admin</option>
                                                    </select>
                                                    @error('role')
                                                        <div class="text-danger mt-1">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                                <div class="mb-3">
                                                    <label for="post_perm" class="form-label">Post Permission</label>
                                                    <select class="form-select" name="post_perm" id="post_perm" required>
                                                        <option value="1">Yes</option>
                                                        <option value="0" selected>No</option>
                                                    </select>
                                                    @error('post_perm')
                                                        <div class="text-danger mt-1">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                                <div class="mb-3">
                                                    <label for="boost_perm" class="form-label">Boost Permission</label>
                                                    <select class="form-select" name="boost_perm" id="boost_perm" required>
                                                        <option value="1">Yes</option>
                                                        <option value="0" selected>No</option>
                                                    </select>
                                                    @error('boost_perm')
                                                        <div class="text-danger mt-1">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                                <div>
                                                    <button type="submit" class="btn btn-primary">Create</button>
                                                    <a href="{{ route('admin.users.index') }}" class="btn btn-link">Cancel</a>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                @endif
                            @show

                            @section('users-edit')
                                @if (Route::is('admin.users.edit'))
                                    <div class="card">
                                        <div class="card-header">
                                            <h5 class="card-title mb-0">Edit User</h5>
                                        </div>
                                        <div class="card-body">
                                            <form action="{{ route('admin.users.update', $user) }}" method="POST">
                                                @csrf
                                                @method('PUT')
                                                <div class="mb-3">
                                                    <label for="name" class="form-label">Name</label>
                                                    <input type="text" class="form-control" name="name" id="name" value="{{ old('name', $user->name) }}" required>
                                                    @error('name')
                                                        <div class="text-danger mt-1">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                                <div class="mb-3">
                                                    <label for="email" class="form-label">Email</label>
                                                    <input type="email" class="form-control" name="email" id="email" value="{{ old('email', $user->email) }}" required>
                                                    @error('email')
                                                        <div class="text-danger mt-1">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                                <div class="mb-3">
                                                    <label for="password" class="form-label">Password (leave blank to keep current)</label>
                                                    <input type="password" class="form-control" name="password" id="password">
                                                    @error('password')
                                                        <div class="text-danger mt-1">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                                <div class="mb-3">
                                                    <label for="password_confirmation" class="form-label">Confirm Password</label>
                                                    <input type="password" class="form-control" name="password_confirmation" id="password_confirmation">
                                                </div>
                                                <div class="mb-3">
                                                    <label for="role" class="form-label">Role</label>
                                                    <select class="form-select" name="role" id="role" required>
                                                        <option value="user" {{ $user->role === 'user' ? 'selected' : '' }}>User</option>
                                                        <option value="admin" {{ $user->role === 'admin' ? 'selected' : '' }}>Admin</option>
                                                    </select>
                                                    @error('role')
                                                        <div class="text-danger mt-1">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                                <div class="mb-3">
                                                    <label for="post_perm" class="form-label">Post Permission</label>
                                                    <select class="form-select" name="post_perm" id="post_perm" required>
                                                        <option value="1" {{ $user->post_perm ? 'selected' : '' }}>Yes</option>
                                                        <option value="0" {{ !$user->post_perm ? 'selected' : '' }}>No</option>
                                                    </select>
                                                    @error('post_perm')
                                                        <div class="text-danger mt-1">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                                <div class="mb-3">
                                                    <label for="boost_perm" class="form-label">Boost Permission</label>
                                                    <select class="form-select" name="boost_perm" id="boost_perm" required>
                                                        <option value="1" {{ $user->boost_perm ? 'selected' : '' }}>Yes</option>
                                                        <option value="0" {{ !$user->boost_perm ? 'selected' : '' }}>No</option>
                                                    </select>
                                                    @error('boost_perm')
                                                        <div class="text-danger mt-1">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                                <div>
                                                    <button type="submit" class="btn btn-primary">Update</button>
                                                    <a href="{{ route('admin.users.index') }}" class="btn btn-link">Cancel</a>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                @endif
                            @show

                            @section('profile')
                                @if (Route::is('admin.profile'))
                                    <div class="card">
                                        <div class="card-header">
                                            <h5 class="card-title mb-0">Edit Profile</h5>
                                        </div>
                                        <div class="card-body">
                                            <form action="{{ route('admin.profile.update') }}" method="POST">
                                                @csrf
                                                @method('PUT')
                                                <div class="mb-3">
                                                    <label for="name" class="form-label">Name</label>
                                                    <input type="text" class="form-control" name="name" id="name" value="{{ old('name', auth()->user()->name) }}" required>
                                                    @error('name')
                                                        <div class="text-danger mt-1">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                                <div class="mb-3">
                                                    <label for="email" class="form-label">Email</label>
                                                    <input type="email" class="form-control" name="email" id="email" value="{{ old('email', auth()->user()->email) }}" required>
                                                    @error('email')
                                                        <div class="text-danger mt-1">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                                <div class="mb-3">
                                                    <label for="password" class="form-label">Password (leave blank to keep current)</label>
                                                    <input type="password" class="form-control" name="password" id="password">
                                                    @error('password')
                                                        <div class="text-danger mt-1">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                                <div class="mb-3">
                                                    <label for="password_confirmation" class="form-label">Confirm Password</label>
                                                    <input type="password" class="form-control" name="password_confirmation" id="password_confirmation">
                                                </div>
                                                <div class="mb-3">
                                                    <label for="role" class="form-label">Role</label>
                                                    <select class="form-select" name="role" id="role" required>
                                                        <option value="user" {{ auth()->user()->role === 'user' ? 'selected' : '' }}>User</option>
                                                        <option value="admin" {{ auth()->user()->role === 'admin' ? 'selected' : '' }}>Admin</option>
                                                    </select>
                                                    @error('role')
                                                        <div class="text-danger mt-1">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                                <div class="mb-3">
                                                    <label for="post_perm" class="form-label">Post Permission</label>
                                                    <select class="form-select" name="post_perm" id="post_perm" required>
                                                        <option value="1" {{ auth()->user()->post_perm ? 'selected' : '' }}>Yes</option>
                                                        <option value="0" {{ !auth()->user()->post_perm ? 'selected' : '' }}>No</option>
                                                    </select>
                                                    @error('post_perm')
                                                        <div class="text-danger mt-1">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                                <div class="mb-3">
                                                    <label for="boost_perm" class="form-label">Boost Permission</label>
                                                    <select class="form-select" name="boost_perm" id="boost_perm" required>
                                                        <option value="1" {{ auth()->user()->boost_perm ? 'selected' : '' }}>Yes</option>
                                                        <option value="0" {{ !auth()->user()->boost_perm ? 'selected' : '' }}>No</option>
                                                    </select>
                                                    @error('boost_perm')
                                                        <div class="text-danger mt-1">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                                <div>
                                                    <button type="submit" class="btn btn-primary">Update</button>
                                                    <a href="{{ route('admin.dashboard') }}" class="btn btn-link">Cancel</a>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                @endif
                            @show

                            @section('subscriptions-index')
                                @if (Route::is('admin.subscriptions.index'))
                                    <div class="card">
                                        <div class="card-header d-flex align-items-center justify-content-between">
                                            <h5 class="card-title m-0 me-2">Subscriptions</h5>
                                            <a href="{{ route('admin.subscriptions.create') }}" class="btn btn-primary">Create Subscription</a>
                                        </div>
                                        <div class="table-responsive">
                                            <table class="table table-borderless border-top">
                                                <thead class="border-bottom">
                                                    <tr>
                                                        <th>ID</th>
                                                        <th>Name</th>
                                                        <th>Monthly Price</th>
                                                        <th>Yearly Price</th>
                                                        <th>Description</th>
                                                        <th>Features</th>
                                                        <th>LinkedIn</th>
                                                        <th>WhatsApp</th>
                                                        <th>Discount (%)</th>
                                                        <th>Actions</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach ($subscriptions ?? [] as $subscription)
                                                        <tr>
                                                            <td>{{ $subscription->id }}</td>
                                                            <td>{{ $subscription->name }}</td>
                                                            <td>{{ number_format($subscription->monthly_price, 2) }}</td>
                                                            <td>{{ number_format($subscription->yearly_price, 2) }}</td>
                                                            <td>{{ $subscription->description ?? 'No description' }}</td>
                                                            <td>
                                                                <ul>
                                                                    @if (is_array($subscription->features) || is_object($subscription->features))
                                                                        @foreach ($subscription->features as $feature)
                                                                            <li>{{ $feature }}</li>
                                                                        @endforeach
                                                                    @else
                                                                        <li>{{ $subscription->features ?? 'No features' }}</li>
                                                                    @endif
                                                                </ul>
                                                            </td>
                                                            <td>
                                                                <span class="{{ $subscription->linkedin ? 'text-success' : 'text-danger' }}">
                                                                    {{ $subscription->linkedin ? 'Yes' : 'No' }}
                                                                </span>
                                                            </td>
                                                            <td>
                                                                <span class="{{ $subscription->whatsapp ? 'text-success' : 'text-danger' }}">
                                                                    {{ $subscription->whatsapp ? 'Yes' : 'No' }}
                                                                </span>
                                                            </td>
                                                            <td>{{ number_format($subscription->discount, 2) }}</td>
                                                            <td>
                                                                <a href="{{ route('admin.subscriptions.edit', $subscription) }}" class="btn btn-sm btn-outline-primary me-2">Edit</a>
                                                                <form action="{{ route('admin.subscriptions.destroy', $subscription) }}" method="POST" style="display: inline;">
                                                                    @csrf
                                                                    @method('DELETE')
                                                                    <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('Are you sure?')">Delete</button>
                                                                </form>
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                @endif
                            @show

                            @section('subscriptions-create')
                                @if (Route::is('admin.subscriptions.create'))
                                    <div class="card">
                                        <div class="card-header">
                                            <h5 class="card-title mb-0">Create Subscription</h5>
                                        </div>
                                        <div class="card-body">
                                            <form action="{{ route('admin.subscriptions.store') }}" method="POST">
                                                @csrf
                                                <div class="mb-3">
                                                    <label for="name" class="form-label">Name</label>
                                                    <input type="text" class="form-control" name="name" id="name" required>
                                                    @error('name')
                                                        <div class="text-danger mt-1">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                                <div class="mb-3">
                                                    <label for="monthly_price" class="form-label">Monthly Price</label>
                                                    <input type="number" step="0.01" class="form-control" name="monthly_price" id="monthly_price" required>
                                                    @error('monthly_price')
                                                        <div class="text-danger mt-1">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                                <div class="mb-3">
                                                    <label for="yearly_price" class="form-label">Yearly Price</label>
                                                    <input type="number" step="0.01" class="form-control" name="yearly_price" id="yearly_price" required>
                                                    @error('yearly_price')
                                                        <div class="text-danger mt-1">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                                <div class="mb-3">
                                                    <label for="description" class="form-label">Description</label>
                                                    <textarea class="form-control" name="description" id="description"></textarea>
                                                    @error('description')
                                                        <div class="text-danger mt-1">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                                <div class="mb-3">
                                                    <label for="features" class="form-label">Features (one per line)</label>
                                                    <textarea class="form-control" name="features[]" id="features" rows="3" required></textarea>
                                                    @error('features')
                                                        <div class="text-danger mt-1">{{ $message }}</div>
                                                    @enderror
                                                    @error('features.*')
                                                        <div class="text-danger mt-1">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                                <div class="mb-3">
                                                    <label for="linkedin" class="form-label">LinkedIn Access</label>
                                                    <select class="form-select" name="linkedin" id="linkedin" required>
                                                        <option value="1">Yes</option>
                                                        <option value="0" selected>No</option>
                                                    </select>
                                                    @error('linkedin')
                                                        <div class="text-danger mt-1">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                                <div class="mb-3">
                                                    <label for="whatsapp" class="form-label">WhatsApp Access</label>
                                                    <select class="form-select" name="whatsapp" id="whatsapp" required>
                                                        <option value="1">Yes</option>
                                                        <option value="0" selected>No</option>
                                                    </select>
                                                    @error('whatsapp')
                                                        <div class="text-danger mt-1">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                                <div class="mb-3">
                                                    <label for="discount" class="form-label">Discount (%)</label>
                                                    <input type="number" step="0.01" class="form-control" name="discount" id="discount" required>
                                                    @error('discount')
                                                        <div class="text-danger mt-1">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                                <div>
                                                    <button type="submit" class="btn btn-primary">Create</button>
                                                    <a href="{{ route('admin.subscriptions.index') }}" class="btn btn-link">Cancel</a>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                @endif
                            @show

                            @section('subscriptions-edit')
                                @if (Route::is('admin.subscriptions.edit'))
                                    <div class="card">
                                        <div class="card-header">
                                            <h5 class="card-title mb-0">Edit Subscription</h5>
                                        </div>
                                        <div class="card-body">
                                            <form action="{{ route('admin.subscriptions.update', $subscription) }}" method="POST">
                                                @csrf
                                                @method('PUT')
                                                <div class="mb-3">
                                                    <label for="name" class="form-label">Name</label>
                                                    <input type="text" class="form-control" name="name" id="name" value="{{ old('name', $subscription->name) }}" required>
                                                    @error('name')
                                                        <div class="text-danger mt-1">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                                <div class="mb-3">
                                                    <label for="monthly_price" class="form-label">Monthly Price</label>
                                                    <input type="number" step="0.01" class="form-control" name="monthly_price" id="monthly_price" value="{{ old('monthly_price', $subscription->monthly_price) }}" required>
                                                    @error('monthly_price')
                                                        <div class="text-danger mt-1">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                                <div class="mb-3">
                                                    <label for="yearly_price" class="form-label">Yearly Price</label>
                                                    <input type="number" step="0.01" class="form-control" name="yearly_price" id="yearly_price" value="{{ old('yearly_price', $subscription->yearly_price) }}" required>
                                                    @error('yearly_price')
                                                        <div class="text-danger mt-1">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                                <div class="mb-3">
                                                    <label for="description" class="form-label">Description</label>
                                                    <textarea class="form-control" name="description" id="description">{{ old('description', $subscription->description) }}</textarea>
                                                    @error('description')
                                                        <div class="text-danger mt-1">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                                <div class="mb-3">
                                                    <label for="features" class="form-label">Features (one per line)</label>
                                                    <textarea class="form-control" name="features[]" id="features" rows="3" required>{{ old('features', implode("\n", (array) $subscription->features)) }}</textarea>
                                                    @error('features')
                                                        <div class="text-danger mt-1">{{ $message }}</div>
                                                    @enderror
                                                    @error('features.*')
                                                        <div class="text-danger mt-1">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                                <div class="mb-3">
                                                    <label for="linkedin" class="form-label">LinkedIn Access</label>
                                                    <select class="form-select" name="linkedin" id="linkedin" required>
                                                        <option value="1" {{ $subscription->linkedin ? 'selected' : '' }}>Yes</option>
                                                        <option value="0" {{ !$subscription->linkedin ? 'selected' : '' }}>No</option>
                                                    </select>
                                                    @error('linkedin')
                                                        <div class="text-danger mt-1">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                                <div class="mb-3">
                                                    <label for="whatsapp" class="form-label">WhatsApp Access</label>
                                                    <select class="form-select" name="whatsapp" id="whatsapp" required>
                                                        <option value="1" {{ $subscription->whatsapp ? 'selected' : '' }}>Yes</option>
                                                        <option value="0" {{ !$subscription->whatsapp ? 'selected' : '' }}>No</option>
                                                    </select>
                                                    @error('whatsapp')
                                                        <div class="text-danger mt-1">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                                <div class="mb-3">
                                                    <label for="discount" class="form-label">Discount (%)</label>
                                                    <input type="number" step="0.01" class="form-control" name="discount" id="discount" value="{{ old('discount', $subscription->discount) }}" required>
                                                    @error('discount')
                                                        <div class="text-danger mt-1">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                                <div>
                                                    <button type="submit" class="btn btn-primary">Update</button>
                                                    <a href="{{ route('admin.subscriptions.index') }}" class="btn btn-link">Cancel</a>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                @endif
                            @show

                            @section('subscriptions-active')
                                @if (Route::is('admin.subscriptions.active'))
                                    <div class="card">
                                        <div class="card-header d-flex align-items-center justify-content-between">
                                            <h5 class="card-title m-0 me-2">Active Subscriptions</h5>
                                            <div class="d-flex align-items-center">
                                                <form action="{{ route('admin.subscriptions.active') }}" method="GET" class="d-flex">
                                                    <input type="text" class="form-control me-2" name="search" placeholder="Search by user name..." value="{{ request('search') }}" style="width: 200px;">
                                                    <button type="submit" class="btn btn-outline-primary me-2">Search</button>
                                                </form>
                                                <a href="{{ route('admin.subscriptions.active.create') }}" class="btn btn-primary">Create Active Subscription</a>
                                            </div>
                                        </div>
                                        <div class="table-responsive">
                                            <table class="table table-borderless border-top">
                                                <thead class="border-bottom">
                                                    <tr>
                                                        <th>ID</th>
                                                        <th>User</th>
                                                        <th>Subscription</th>
                                                        <th>Pricing Mode</th>
                                                        <th>Expiration Date</th>
                                                        <th>Actions</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach ($userSubscriptions ?? [] as $userSubscription)
                                                        <tr>
                                                            <td>{{ $userSubscription->id }}</td>
                                                            <td>{{ $userSubscription->user->name ?? 'N/A' }}</td>
                                                            <td>{{ $userSubscription->subscription->name ?? 'N/A' }}</td>
                                                            <td>{{ ucfirst($userSubscription->pricing_mode) }}</td>
                                                            <td>{{ $userSubscription->expires_at ? $userSubscription->expires_at->format('Y-m-d') : 'N/A' }}</td>
                                                            <td>
                                                                <a href="{{ route('admin.subscriptions.active.edit', $userSubscription) }}" class="btn btn-sm btn-outline-primary me-2">Edit</a>
                                                                <form action="{{ route('admin.subscriptions.active.destroy', $userSubscription) }}" method="POST" style="display: inline;">
                                                                    @csrf
                                                                    @method('DELETE')
                                                                    <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('Are you sure?')">Delete</button>
                                                                </form>
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                            <div class="mt-3">
                                                {{ $userSubscriptions->appends(['search' => request('search')])->links() }}
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            @show

                            @section('subscriptions-active-create')
                                @if (Route::is('admin.subscriptions.active.create'))
                                    <div class="card">
                                        <div class="card-header">
                                            <h5 class="card-title mb-0">Create Active Subscription</h5>
                                        </div>
                                        <div class="card-body">
                                            <form action="{{ route('admin.subscriptions.active.store') }}" method="POST">
                                                @csrf
                                                <div class="mb-3">
                                                    <label for="user_id" class="form-label">User</label>
                                                    <select class="form-select" name="user_id" id="user_id" required>
                                                        <option value="">Select a user</option>
                                                        @foreach (\App\Models\User::all() as $user)
                                                            <option value="{{ $user->id }}">{{ $user->name }} ({{ $user->email }})</option>
                                                        @endforeach
                                                    </select>
                                                    @error('user_id')
                                                        <div class="text-danger mt-1">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                                <div class="mb-3">
                                                    <label for="subscription_id" class="form-label">Subscription</label>
                                                    <select class="form-select" name="subscription_id" id="subscription_id" required>
                                                        <option value="">Select a subscription</option>
                                                        @foreach (\App\Models\Subscription::all() as $subscription)
                                                            <option value="{{ $subscription->id }}">{{ $subscription->name }}</option>
                                                        @endforeach
                                                    </select>
                                                    @error('subscription_id')
                                                        <div class="text-danger mt-1">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                                <div class="mb-3">
                                                    <label for="pricing_mode" class="form-label">Pricing Mode</label>
                                                    <select class="form-select" name="pricing_mode" id="pricing_mode" required>
                                                        <option value="monthly">Monthly</option>
                                                        <option value="yearly">Yearly</option>
                                                    </select>
                                                    @error('pricing_mode')
                                                        <div class="text-danger mt-1">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                                <div class="mb-3">
                                                    <label for="expires_at" class="form-label">Expiration Date</label>
                                                    <input type="date" class="form-control" name="expires_at" id="expires_at" required>
                                                    @error('expires_at')
                                                        <div class="text-danger mt-1">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                                <div>
                                                    <button type="submit" class="btn btn-primary">Create</button>
                                                    <a href="{{ route('admin.subscriptions.active') }}" class="btn btn-link">Cancel</a>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                @endif
                            @show

                            @section('subscriptions-active-edit')
                                @if (Route::is('admin.subscriptions.active.edit'))
                                    <div class="card">
                                        <div class="card-header">
                                            <h5 class="card-title mb-0">Edit Active Subscription</h5>
                                        </div>
                                        <div class="card-body">
                                            <form action="{{ route('admin.subscriptions.active.update', $userSubscription) }}" method="POST">
                                                @csrf
                                                @method('PUT')
                                                <div class="mb-3">
                                                    <label for="user_id" class="form-label">User</label>
                                                    <select class="form-select" name="user_id" id="user_id" required>
                                                        <option value="">Select a user</option>
                                                        @foreach (\App\Models\User::all() as $user)
                                                            <option value="{{ $user->id }}" {{ $userSubscription->user_id == $user->id ? 'selected' : '' }}>
                                                                {{ $user->name }} ({{ $user->email }})
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                    @error('user_id')
                                                        <div class="text-danger mt-1">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                                <div class="mb-3">
                                                    <label for="subscription_id" class="form-label">Subscription</label>
                                                    <select class="form-select" name="subscription_id" id="subscription_id" required>
                                                        <option value="">Select a subscription</option>
                                                        @foreach (\App\Models\Subscription::all() as $subscription)
                                                            <option value="{{ $subscription->id }}" {{ $userSubscription->subscription_id == $subscription->id ? 'selected' : '' }}>
                                                                {{ $subscription->name }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                    @error('subscription_id')
                                                        <div class="text-danger mt-1">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                                <div class="mb-3">
                                                    <label for="pricing_mode" class="form-label">Pricing Mode</label>
                                                    <select class="form-select" name="pricing_mode" id="pricing_mode" required>
                                                        <option value="monthly" {{ $userSubscription->pricing_mode == 'monthly' ? 'selected' : '' }}>Monthly</option>
                                                        <option value="yearly" {{ $userSubscription->pricing_mode == 'yearly' ? 'selected' : '' }}>Yearly</option>
                                                    </select>
                                                    @error('pricing_mode')
                                                        <div class="text-danger mt-1">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                                <div class="mb-3">
                                                    <label for="expires_at" class="form-label">Expiration Date</label>
                                                    <input type="date" class="form-control" name="expires_at" id="expires_at" value="{{ $userSubscription->expires_at ? $userSubscription->expires_at->format('Y-m-d') : '' }}" required>
                                                    @error('expires_at')
                                                        <div class="text-danger mt-1">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                                <div>
                                                    <button type="submit" class="btn btn-primary">Update</button>
                                                    <a href="{{ route('admin.subscriptions.active') }}" class="btn btn-link">Cancel</a>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                @endif
                            @show

                            @section('coupons-index')
                                @if (Route::is('admin.coupons.index'))
                                    <div class="card">
                                        <div class="card-header d-flex align-items-center justify-content-between">
                                            <h5 class="card-title m-0 me-2">Promo Codes</h5>
                                            <a href="{{ route('admin.coupons.create') }}" class="btn btn-primary">Create Promo Code</a>
                                        </div>
                                        <div class="table-responsive">
                                            <table class="table table-borderless border-top">
                                                <thead class="border-bottom">
                                                    <tr>
                                                        <th>ID</th>
                                                        <th>Code</th>
                                                        <th>Discount (%)</th>
                                                        <th>Expiration Date</th>
                                                        <th>Actions</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach ($coupons ?? [] as $coupon)
                                                        <tr>
                                                            <td>{{ $coupon->id }}</td>
                                                            <td>{{ $coupon->code }}</td>
                                                            <td>{{ number_format($coupon->discount, 2) }}</td>
                                                            <td>{{ $coupon->expires_at ? $coupon->expires_at->format('Y-m-d') : 'N/A' }}</td>
                                                            <td>
                                                                <a href="{{ route('admin.coupons.edit', $coupon) }}" class="btn btn-sm btn-outline-primary me-2">Edit</a>
                                                                <form action="{{ route('admin.coupons.destroy', $coupon) }}" method="POST" style="display: inline;">
                                                                    @csrf
                                                                    @method('DELETE')
                                                                    <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('Are you sure?')">Delete</button>
                                                                </form>
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                @endif
                            @show

                            @section('coupons-create')
                                @if (Route::is('admin.coupons.create'))
                                    <div class="card">
                                        <div class="card-header">
                                            <h5 class="card-title mb-0">Create Promo Code</h5>
                                        </div>
                                        <div class="card-body">
                                            <form action="{{ route('admin.coupons.store') }}" method="POST">
                                                @csrf
                                                <div class="mb-3">
                                                    <label for="code" class="form-label">Code</label>
                                                    <input type="text" class="form-control" name="code" id="code" required>
                                                    @error('code')
                                                        <div class="text-danger mt-1">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                                <div class="mb-3">
                                                    <label for="discount" class="form-label">Discount (%)</label>
                                                    <input type="number" step="0.01" class="form-control" name="discount" id="discount" required>
                                                    @error('discount')
                                                        <div class="text-danger mt-1">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                                <div class="mb-3">
                                                    <label for="expires_at" class="form-label">Expiration Date</label>
                                                    <input type="date" class="form-control" name="expires_at" id="expires_at">
                                                    @error('expires_at')
                                                        <div class="text-danger mt-1">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                                <div>
                                                    <button type="submit" class="btn btn-primary">Create</button>
                                                    <a href="{{ route('admin.coupons.index') }}" class="btn btn-link">Cancel</a>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                @endif
                            @show

                            @section('coupons-edit')
                                @if (Route::is('admin.coupons.edit'))
                                    <div class="card">
                                        <div class="card-header">
                                            <h5 class="card-title mb-0">Edit Promo Code</h5>
                                        </div>
                                        <div class="card-body">
                                            <form action="{{ route('admin.coupons.update', $coupon) }}" method="POST">
                                                @csrf
                                                @method('PUT')
                                                <div class="mb-3">
                                                    <label for="code" class="form-label">Code</label>
                                                    <input type="text" class="form-control" name="code" id="code" value="{{ old('code', $coupon->code) }}" required>
                                                    @error('code')
                                                        <div class="text-danger mt-1">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                                <div class="mb-3">
                                                    <label for="discount" class="form-label">Discount (%)</label>
                                                    <input type="number" step="0.01" class="form-control" name="discount" id="discount" value="{{ old('discount', $coupon->discount) }}" required>
                                                    @error('discount')
                                                        <div class="text-danger mt-1">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                                <div class="mb-3">
                                                    <label for="expires_at" class="form-label">Expiration Date</label>
                                                    <input type="date" class="form-control" name="expires_at" id="expires_at" value="{{ old('expires_at', $coupon->expires_at ? $coupon->expires_at->format('Y-m-d') : '') }}">
                                                    @error('expires_at')
                                                        <div class="text-danger mt-1">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                                <div>
                                                    <button type="submit" class="btn btn-primary">Update</button>
                                                    <a href="{{ route('admin.coupons.index') }}" class="btn btn-link">Cancel</a>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                @endif
                            @show

                            @section('boostinteractions-index')
    @if (Route::is('admin.boostinteractions.index'))
        <div class="card">
            <div class="card-header">
                <h5 class="card-title m-0 me-2">Boost Interaction Requests</h5>
            </div>
            <div class="table-responsive">
                <table class="table table-borderless border-top">
                    <thead class="border-bottom">
                        <tr>
                            <th>ID</th>
                            <th>User</th>
                            <th>Post URL</th>
                            <th>Status</th>
                            <th>Created At</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($boostInteractions ?? [] as $boostInteraction)
                            <tr>
                                <td>{{ $boostInteraction->id }}</td>
                                <td>{{ $boostInteraction->user->name ?? 'N/A' }}</td>
                                <td>
                                    <a href="{{ $boostInteraction->post_url }}" target="_blank">{{ Str::limit($boostInteraction->post_url, 30) }}</a>
                                </td>
                                <td>
                                    <span class="badge {{ $boostInteraction->status == 'pending' ? 'bg-warning' : ($boostInteraction->status == 'accepted' ? 'bg-success' : 'bg-danger') }}">
                                        {{ ucfirst($boostInteraction->status) }}
                                    </span>
                                </td>
                                <td>{{ $boostInteraction->created_at->format('Y-m-d H:i:s') }}</td>
                                <td>
                                    @if ($boostInteraction->status == 'pending')
                                        <form action="{{ route('admin.boostinteractions.update', $boostInteraction) }}" method="POST" style="display: inline;">
                                            @csrf
                                            @method('PUT')
                                            <input type="hidden" name="status" value="accepted"> <!-- Changed from approved -->
                                            <button type="submit" class="btn btn-sm btn-outline-success me-2">Accept</button>
                                        </form>
                                        <form action="{{ route('admin.boostinteractions.update', $boostInteraction) }}" method="POST" style="display: inline;">
                                            @csrf
                                            @method('PUT')
                                            <input type="hidden" name="status" value="declined"> <!-- Changed from rejected -->
                                            <button type="submit" class="btn btn-sm btn-outline-danger me-2">Decline</button>
                                        </form>
                                    @endif
                                    <form action="{{ route('admin.boostinteractions.destroy', $boostInteraction) }}" method="POST" style="display: inline;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('Are you sure?')">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                <div class="mt-3">
                    {{ $boostInteractions->links() }}
                </div>
            </div>
        </div>
    @endif
@show

                        </div>
                        <!-- / Content -->

                        <!-- Footer -->

                        <!-- / Footer -->

                        <div class="content-backdrop fade"></div>
                    </div>
                    <!-- / Content wrapper -->
                </div>
                <!-- / Layout page -->
            </div>

            <!-- Overlay -->
            <div class="layout-overlay layout-menu-toggle"></div>

            <!-- Drag Target Area To SlideIn Menu On Small Screens -->
            <div class="drag-target"></div>
        </div>
        <!-- / Layout wrapper -->

        <!-- Core JS -->
        <script src="{{ asset('assets/vendor/libs/jquery/jquery.js') }}"></script>
        <script src="{{ asset('assets/vendor/libs/popper/popper.js') }}"></script>
        <script src="{{ asset('assets/vendor/js/bootstrap.js') }}"></script>
        <script src="{{ asset('assets/vendor/libs/node-waves/node-waves.js') }}"></script>
        <script src="{{ asset('assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.js') }}"></script>
        <script src="{{ asset('assets/vendor/libs/hammer/hammer.js') }}"></script>
        <script src="{{ asset('assets/vendor/libs/i18n/i18n.js') }}"></script>
        <script src="{{ asset('assets/vendor/libs/typeahead-js/typeahead.js') }}"></script>
        <script src="{{ asset('assets/vendor/js/menu.js') }}"></script>

        <!-- Vendors JS -->
        <script src="{{ asset('assets/vendor/libs/moment/moment.js') }}"></script>
        <script src="{{ asset('assets/vendor/libs/datatables-bs