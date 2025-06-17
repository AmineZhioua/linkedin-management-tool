<!DOCTYPE html>
<html lang="fr" class="light-style layout-navbar-fixed layout-menu-fixed layout-compact" dir="ltr" data-theme="theme-default" data-assets-path="{{ asset('assets/') }}/" data-template="vertical-menu-template">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />
    <title>Tableau de bord Admin - @yield('title')</title>
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
                    <li class="menu-item {{ Route::is('admin.dashboard') || Route::is('admin.users.*') || Route::is('admin.subscriptions.*') || Route::is('admin.coupons.*') || Route::is('admin.boostinteractions.*') || Route::is('admin.alerts.*') ? 'active open' : '' }}">
                        <a href="javascript:void(0);" class="menu-link menu-toggle">
                            <i class="menu-icon tf-icons ti ti-smart-home"></i>
                            <div data-i18n="Dashboards">Tableaux de bord</div>
                        </a>
                        <ul class="menu-sub">
                            <li class="menu-item {{ Route::is('admin.users.*') ? 'active' : '' }}">
                                <a href="{{ route('admin.users.index') }}" class="menu-link">
                                    <div data-i18n="Users">Utilisateurs</div>
                                </a>
                            </li>
                            <li class="menu-item {{ Route::is('admin.subscriptions.index') || Route::is('admin.subscriptions.create') || Route::is('admin.subscriptions.edit') ? 'active' : '' }}">
                                <a href="{{ route('admin.subscriptions.index') }}" class="menu-link">
                                    <div data-i18n="Subscriptions">Abonnements</div>
                                </a>
                            </li>
                            <li class="menu-item {{ Route::is('admin.subscriptions.active') || Route::is('admin.subscriptions.active.create') || Route::is('admin.subscriptions.active.edit') ? 'active' : '' }}">
                                <a href="{{ route('admin.subscriptions.active') }}" class="menu-link">
                                    <div data-i18n="Active Subscriptions">Abonnements actifs</div>
                                </a>
                            </li>
                            <li class="menu-item {{ Route::is('admin.coupons.index') || Route::is('admin.coupons.create') || Route::is('admin.coupons.edit') ? 'active' : '' }}">
                                <a href="{{ route('admin.coupons.index') }}" class="menu-link">
                                    <div data-i18n="Promo Codes">Codes promotionnels</div>
                                </a>
                            </li>
                            <li class="menu-item {{ Route::is('admin.boostinteractions.index') ? 'active' : '' }}">
                                <a href="{{ route('admin.boostinteractions.index') }}" class="menu-link">
                                    <div data-i18n="Boost Interactions">Boost d'interactions</div>
                                </a>
                            </li>
                            <li class="menu-item {{ Route::is('admin.alerts.index') || Route::is('admin.alerts.create') || Route::is('admin.alerts.edit') ? 'active' : '' }}">
                                <a href="{{ route('admin.alerts.index') }}" class="menu-link">
                                    <div data-i18n="Alerts">Alertes</div>
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
                        <ul class="navbar-nav flex-row align-items-center ms-auto">
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
                                                    <small class="text-muted">{{ ucfirst(auth()->user()->role == 'admin' ? 'Administrateur' : 'Utilisateur') }}</small>
                                                </div>
                                            </div>
                                        </a>
                                    </li>
                                    <li><div class="dropdown-divider"></div></li>
                                    <li><a class="dropdown-item" href="{{ route('admin.profile') }}"><i class="ti ti-user-check me-2 ti-sm"></i><span class="align-middle">Profil</span></a></li>
                                    <li><a class="dropdown-item" href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();"><i class="ti ti-logout me-2 ti-sm"></i><span class="align-middle">Déconnexion</span></a></li>
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
                        <input type="text" class="form-control search-input container-xxl border-0" placeholder="Rechercher..." aria-label="Rechercher..." />
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
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fermer"></button>
                            </div>
                        @endif
                        @if (session('error'))
                            <div class="alert alert-danger alert-dismissible" role="alert">
                                {{ session('error') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fermer"></button>
                            </div>
                        @endif

                        @section('dashboard')
                            @if (Route::is('admin.dashboard'))
                                <div class="row">
                                    <!-- User Metrics Card -->
                                    <div class="col-12 col-xl-3 mb-4">
                                        <div class="card h-100">
                                            <div class="card-header d-flex align-items-center justify-content-between">
                                                <h5 class="card-title m-0 me-2">Métriques des utilisateurs</h5>
                                                <div class="dropdown">
                                                    <button class="btn p-0" type="button" id="userMetricsFilter" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                        <i class="ti ti-dots-vertical"></i>
                                                    </button>
                                                    <div class="dropdown-menu dropdown-menu-end" aria-labelledby="userMetricsFilter">
                                                        <a class="dropdown-item" href="javascript:void(0);" data-filter="suspended">Utilisateurs suspendus</a>
                                                        <a class="dropdown-item" href="javascript:void(0);" data-filter="email_verified">Utilisateurs vérifiés</a>
                                                        <a class="dropdown-item" href="javascript:void(0);" data-filter="active_last_hour">Utilisateurs actifs dernière heure</a>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="card-body">
                                                <div id="userMetricsChart"></div>
                                                <div class="text-center mt-3" id="userMetricsSummary"></div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Campaign Metrics Card -->
                                    <div class="col-12 col-xl-3 mb-4">
                                        <div class="card h-100">
                                            <div class="card-header d-flex align-items-center justify-content-between">
                                                <h5 class="card-title m-0 me-2">Métriques des campagnes</h5>
                                            </div>
                                            <div class="card-body">
                                                <div id="campaignMetricsChart"></div>
                                                <div class="text-center mt-3" id="campaignMetricsSummary"></div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Boost Interaction Metrics Card -->
                                    <div class="col-12 col-xl-3 mb-4">
                                        <div class="card h-100">
                                            <div class="card-header d-flex align-items-center justify-content-between">
                                                <h5 class="card-title m-0 me-2">Métriques d'amplification</h5>
                                            </div>
                                            <div class="card-body">
                                                <div id="boostInteractionMetricsChart"></div>
                                                <div class="text-center mt-3" id="boostInteractionMetricsSummary"></div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Post Metrics Card -->
                                    <div class="col-12 col-xl-3 mb-4">
                                        <div class="card h-100">
                                            <div class="card-header d-flex align-items-center justify-content-between">
                                                <h5 class="card-title m-0 me-2">Métriques des publications</h5>
                                            </div>
                                            <div class="card-body">
                                                <div id="postMetricsChart"></div>
                                                <div class="text-center mt-3" id="postMetricsSummary"></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- ApexCharts JavaScript -->
                                <script>
                                    document.addEventListener('DOMContentLoaded', function () {
                                        // Function to initialize a donut chart
                                        function initializeChart(chartId, summaryId, endpoint, unit = 'éléments') {
                                            var options = {
                                                chart: {
                                                    type: 'donut',
                                                    height: 250,
                                                    toolbar: { show: false }
                                                },
                                                series: [],
                                                labels: [],
                                                colors: ['#7367F0', '#FF9F43', '#28C76F'],
                                                dataLabels: {
                                                    enabled: true,
                                                    formatter: function (val, opts) {
                                                        return opts.w.config.series[opts.seriesIndex];
                                                    }
                                                },
                                                legend: {
                                                    show: true,
                                                    position: 'bottom'
                                                },
                                                tooltip: {
                                                    y: {
                                                        formatter: function (val) {
                                                            return val + ' ' + unit;
                                                        }
                                                    }
                                                }
                                            };
                                            var chart = new ApexCharts(document.querySelector("#" + chartId), options);
                                            chart.render();

                                            // Fetch and update chart data
                                            fetch(endpoint, {
                                                headers: {
                                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                                    'Accept': 'application/json'
                                                }
                                            })
                                            .then(response => response.json())
                                            .then(data => {
                                                chart.updateOptions({
                                                    series: data.data,
                                                    labels: data.labels
                                                });
                                                let summary = '';
                                                data.labels.forEach((label, index) => {
                                                    summary += `${label}: ${data.data[index]}<br>`;
                                                });
                                                document.getElementById(summaryId).innerHTML = summary;
                                            })
                                            .catch(error => console.error('Erreur lors de la récupération des métriques:', error));
                                        }

                                        // Initialize User Metrics Chart with filter
                                        var userMetricsOptions = {
                                            chart: {
                                                type: 'donut',
                                                height: 250,
                                                toolbar: { show: false }
                                            },
                                            series: [],
                                            labels: [],
                                            colors: ['#7367F0', '#FF9F43'],
                                            dataLabels: {
                                                enabled: true,
                                                formatter: function (val, opts) {
                                                    return opts.w.config.series[opts.seriesIndex];
                                                }
                                            },
                                            legend: {
                                                show: true,
                                                position: 'bottom'
                                            },
                                            tooltip: {
                                                y: {
                                                    formatter: function (val) {
                                                        return val + ' utilisateurs';
                                                    }
                                                }
                                            }
                                        };
                                        var userMetricsChart = new ApexCharts(document.querySelector("#userMetricsChart"), userMetricsOptions);
                                        userMetricsChart.render();

                                        function updateUserChart(filter = 'suspended') {
                                            fetch('{{ route('admin.metrics') }}?filter=' + filter, {
                                                headers: {
                                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                                    'Accept': 'application/json'
                                                }
                                            })
                                            .then(response => response.json())
                                            .then(data => {
                                                userMetricsChart.updateOptions({
                                                    series: data.data,
                                                    labels: data.labels
                                                });
                                                let summary = '';
                                                data.labels.forEach((label, index) => {
                                                    summary += `${label}: ${data.data[index]} utilisateurs<br>`;
                                                });
                                                document.getElementById('userMetricsSummary').innerHTML = summary;
                                            })
                                            .catch(error => console.error('Erreur lors de la récupération des métriques utilisateurs:', error));
                                        }

                                        // Initial user chart load
                                        updateUserChart();

                                        // Handle user filter dropdown clicks
                                        document.querySelectorAll('#userMetricsFilter ~ .dropdown-menu .dropdown-item').forEach(item => {
                                            item.addEventListener('click', function () {
                                                const filter = this.getAttribute('data-filter');
                                                updateUserChart(filter);
                                            });
                                        });

                                        // Initialize other charts
                                        initializeChart('campaignMetricsChart', 'campaignMetricsSummary', '{{ route('admin.campaign.metrics') }}', 'campagnes');
                                        initializeChart('boostInteractionMetricsChart', 'boostInteractionMetricsSummary', '{{ route('admin.boost.interaction.metrics') }}', 'interactions');
                                        initializeChart('postMetricsChart', 'postMetricsSummary', '{{ route('admin.post.metrics') }}', 'publications');
                                    });
                                </script>
                            @endif
                        @show

                        @section('users-index')
                            @if (Route::is('admin.users.index'))
                                <div class="card">
                                    <div class="card-header d-flex align-items-center justify-content-between">
                                        <h5 class="card-title m-0 me-2">Utilisateurs</h5>
                                        <div class="d-flex align-items-center">
                                            <form action="{{ route('admin.users.index') }}" method="GET" class="d-flex">
                                                <input type="text" class="form-control me-2" name="search" placeholder="Rechercher par nom ou email..." value="{{ request('search') }}" style="width: 200px;">
                                                <button type="submit" class="btn btn-outline-primary me-2">Rechercher</button>
                                                <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary me-2">Effacer</a>
                                            </form>
                                            <a href="{{ route('admin.users.create') }}" class="btn btn-primary">Créer un utilisateur</a>
                                        </div>
                                    </div>
                                    <div class="table-responsive">
                                        <table class="table table-borderless border-top" id="usersTable">
                                            <thead class="border-bottom">
                                                <tr>
                                                    <th>ID</th>
                                                    <th>Nom</th>
                                                    <th>Email</th>
                                                    <th>Rôle</th>
                                                    <th>Permission de publication</th>
                                                    <th>Permission d'amplification</th>
                                                    <th>Fin de suspension</th>
                                                    <th>Dernière activité</th>
                                                    <th>Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @forelse ($users as $user)
                                                    <tr>
                                                        <td>{{ $user->id }}</td>
                                                        <td>{{ $user->name }}</td>
                                                        <td>{{ $user->email }}</td>
                                                        <td>{{ $user->role == 'admin' ? 'Administrateur' : 'Utilisateur' }}</td>
                                                        <td>
                                                            <span class="{{ $user->post_perm ? 'text-success' : 'text-danger' }}">
                                                                {{ $user->post_perm ? 'Oui' : 'Non' }}
                                                            </span>
                                                        </td>
                                                        <td>
                                                            <span class="{{ $user->boost_perm ? 'text-success' : 'text-danger' }}">
                                                                {{ $user->boost_perm ? 'Oui' : 'Non' }}
                                                            </span>
                                                        </td>
                                                        <td>{{ $user->suspend_end ? $user->suspend_end->format('d-m-Y') : 'Aucune' }}</td>
                                                        <td>{{ $user->last_activity ? $user->last_activity->format('d-m-Y H:i:s') : 'Jamais' }}</td>
                                                        <td>
                                                            <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-sm btn-outline-primary me-2">Modifier</a>
                                                            <form action="{{ route('admin.users.destroy', $user) }}" method="POST" style="display: inline;">
                                                                @csrf
                                                                @method('DELETE')
                                                                <button type="submit" class="btn btn-sm btn-outline-danger me-2" onclick="return confirm('Êtes-vous sûr ?')">Supprimer</button>
                                                            </form>
                                                            <button type="button" class="btn btn-sm btn-outline-warning me-2 suspend-btn" data-bs-toggle="modal" data-bs-target="#suspendModal" data-user-id="{{ $user->id }}" data-user-name="{{ $user->name }}">
                                                                Suspendre
                                                            </button>
                                                            @if ($user->suspend_end)
                                                                <form action="{{ route('admin.users.remove-suspension') }}" method="POST" style="display: inline;">
                                                                    @csrf
                                                                    <input type="hidden" name="user_id" value="{{ $user->id }}">
                                                                    <button type="submit" class="btn btn-sm btn-outline-success" onclick="return confirm('Êtes-vous sûr de vouloir lever la suspension pour {{ $user->name }} ?')">Lever la suspension</button>
                                                                </form>
                                                            @endif
                                                        </td>
                                                    </tr>
                                                @empty
                                                    <tr>
                                                        <td colspan="9" class="text-center">Aucun utilisateur trouvé.</td>
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
                                                <h5 class="modal-title" id="suspendModalLabel">Suspendre un utilisateur</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fermer"></button>
                                            </div>
                                            <div class="modal-body">
                                                <form id="suspendForm" action="{{ route('admin.users.suspend') }}" method="POST">
                                                    @csrf
                                                    <input type="hidden" name="user_id" id="suspendUserId">
                                                    <div class="mb-3">
                                                        <label for="suspendDate" class="form-label">Sélectionner la date de fin de suspension</label>
                                                        <input type="date" class="form-control" name="suspend_date" id="suspendDate" required min="{{ now()->format('Y-m-d') }}">
                                                    </div>
                                                    <p>Êtes-vous sûr de vouloir suspendre <strong id="suspendUserName"></strong> jusqu'à la date sélectionnée ?</p>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                                                        <button type="submit" class="btn btn-warning">Suspendre</button>
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

                                        // Initialize DataTables with French translation
                                        $('#usersTable').DataTable({
                                            dom: 'frtip',
                                            pageLength: 1,
                                            ordering: false,
                                            searching: false,
                                            paging: false,
                                            info: false,
                                            language: {
                                                search: '',
                                                searchPlaceholder: 'Rechercher des utilisateurs...',
                                                emptyTable: 'Aucun utilisateur trouvé',
                                                zeroRecords: 'Aucun résultat correspondant'
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
                                        <h5 class="card-title mb-0">Créer un utilisateur</h5>
                                    </div>
                                    <div class="card-body">
                                        <form action="{{ route('admin.users.store') }}" method="POST">
                                            @csrf
                                            <div class="mb-3">
                                                <label for="name" class="form-label">Nom</label>
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
                                                <label for="password" class="form-label">Mot de passe</label>
                                                <input type="password" class="form-control" name="password" id="password" required>
                                                @error('password')
                                                    <div class="text-danger mt-1">{{ $message }}</div>
                                                @enderror
                                            </div>
                                            <div class="mb-3">
                                                <label for="password_confirmation" class="form-label">Confirmer le mot de passe</label>
                                                <input type="password" class="form-control" name="password_confirmation" id="password_confirmation" required>
                                            </div>
                                            <div class="mb-3">
                                                <label for="role" class="form-label">Rôle</label>
                                                <select class="form-select" name="role" id="role" required>
                                                    <option value="user">Utilisateur</option>
                                                    <option value="admin">Administrateur</option>
                                                </select>
                                                @error('role')
                                                    <div class="text-danger mt-1">{{ $message }}</div>
                                                @enderror
                                            </div>
                                            <div class="mb-3">
                                                <label for="post_perm" class="form-label">Permission de publication</label>
                                                <select class="form-select" name="post_perm" id="post_perm" required>
                                                    <option value="1">Oui</option>
                                                    <option value="0" selected>Non</option>
                                                </select>
                                                @error('post_perm')
                                                    <div class="text-danger mt-1">{{ $message }}</div>
                                                @enderror
                                            </div>
                                            <div class="mb-3">
                                                <label for="boost_perm" class="form-label">Permission d'amplification</label>
                                                <select class="form-select" name="boost_perm" id="boost_perm" required>
                                                    <option value="1">Oui</option>
                                                    <option value="0" selected>Non</option>
                                                </select>
                                                @error('boost_perm')
                                                    <div class="text-danger mt-1">{{ $message }}</div>
                                                @enderror
                                            </div>
                                            <div>
                                                <button type="submit" class="btn btn-primary">Créer</button>
                                                <a href="{{ route('admin.users.index') }}" class="btn btn-link">Annuler</a>
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
                                        <h5 class="card-title mb-0">Modifier un utilisateur</h5>
                                    </div>
                                    <div class="card-body">
                                        <form action="{{ route('admin.users.update', $user) }}" method="POST">
                                            @csrf
                                            @method('PUT')
                                            <div class="mb-3">
                                                <label for="name" class="form-label">Nom</label>
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
                                                <label for="password" class="form-label">Mot de passe (laisser vide pour conserver l'actuel)</label>
                                                <input type="password" class="form-control" name="password" id="password">
                                                @error('password')
                                                    <div class="text-danger mt-1">{{ $message }}</div>
                                                @enderror
                                            </div>
                                            <div class="mb-3">
                                                <label for="password_confirmation" class="form-label">Confirmer le mot de passe</label>
                                                <input type="password" class="form-control" name="password_confirmation" id="password_confirmation">
                                            </div>
                                            <div class="mb-3">
                                                <label for="role" class="form-label">Rôle</label>
                                                <select class="form-select" name="role" id="role" required>
                                                    <option value="user" {{ $user->role === 'user' ? 'selected' : '' }}>Utilisateur</option>
                                                    <option value="admin" {{ $user->role === 'admin' ? 'selected' : '' }}>Administrateur</option>
                                                </select>
                                                @error('role')
                                                    <div class="text-danger mt-1">{{ $message }}</div>
                                                @enderror
                                            </div>
                                            <div class="mb-3">
                                                <label for="post_perm" class="form-label">Permission de publication</label>
                                                <select class="form-select" name="post_perm" id="post_perm" required>
                                                    <option value="1" {{ $user->post_perm ? 'selected' : '' }}>Oui</option>
                                                    <option value="0" {{ !$user->post_perm ? 'selected' : '' }}>Non</option>
                                                </select>
                                                @error('post_perm')
                                                    <div class="text-danger mt-1">{{ $message }}</div>
                                                @enderror
                                            </div>
                                            <div class="mb-3">
                                                <label for="boost_perm" class="form-label">Permission d'amplification</label>
                                                <select class="form-select" name="boost_perm" id="boost_perm" required>
                                                    <option value="1" {{ $user->boost_perm ? 'selected' : '' }}>Oui</option>
                                                    <option value="0" {{ !$user->boost_perm ? 'selected' : '' }}>Non</option>
                                                </select>
                                                @error('boost_perm')
                                                    <div class="text-danger mt-1">{{ $message }}</div>
                                                @enderror
                                            </div>
                                            <div>
                                                <button type="submit" class="btn btn-primary">Mettre à jour</button>
                                                <a href="{{ route('admin.users.index') }}" class="btn btn-link">Annuler</a>
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
                                        <h5 class="card-title mb-0">Modifier le profil</h5>
                                    </div>
                                    <div class="card-body">
                                        <form action="{{ route('admin.profile.update') }}" method="POST">
                                            @csrf
                                            @method('PUT')
                                            <div class="mb-3">
                                                <label for="name" class="form-label">Nom</label>
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
                                                <label for="password" class="form-label">Mot de passe (laisser vide pour conserver l'actuel)</label>
                                                <input type="password" class="form-control" name="password" id="password">
                                                @error('password')
                                                    <div class="text-danger mt-1">{{ $message }}</div>
                                                @enderror
                                            </div>
                                            <div class="mb-3">
                                                <label for="password_confirmation" class="form-label">Confirmer le mot de passe</label>
                                                <input type="password" class="form-control" name="password_confirmation" id="password_confirmation">
                                            </div>
                                            <div class="mb-3">
                                                <label for="role" class="form-label">Rôle</label>
                                                <select class="form-select" name="role" id="role" required>
                                                    <option value="user" {{ auth()->user()->role === 'user' ? 'selected' : '' }}>Utilisateur</option>
                                                    <option value="admin" {{ auth()->user()->role === 'admin' ? 'selected' : '' }}>Administrateur</option>
                                                </select>
                                                @error('role')
                                                    <div class="text-danger mt-1">{{ $message }}</div>
                                                @enderror
                                            </div>
                                            <div class="mb-3">
                                                <label for="post_perm" class="form-label">Permission de publication</label>
                                                <select class="form-select" name="post_perm" id="post_perm" required>
                                                    <option value="1" {{ auth()->user()->post_perm ? 'selected' : '' }}>Oui</option>
                                                    <option value="0" {{ !auth()->user()->post_perm ? 'selected' : '' }}>Non</option>
                                                </select>
                                                @error('post_perm')
                                                    <div class="text-danger mt-1">{{ $message }}</div>
                                                @enderror
                                            </div>
                                            <div class="mb-3">
                                                <label for="boost_perm" class="form-label">Permission d'amplification</label>
                                                <select class="form-select" name="boost_perm" id="boost_perm" required>
                                                    <option value="1" {{ auth()->user()->boost_perm ? 'selected' : '' }}>Oui</option>
                                                    <option value="0" {{ !auth()->user()->boost_perm ? 'selected' : '' }}>Non</option>
                                                </select>
                                                @error('boost_perm')
                                                    <div class="text-danger mt-1">{{ $message }}</div>
                                                @enderror
                                            </div>
                                            <div>
                                                <button type="submit" class="btn btn-primary">Mettre à jour</button>
                                                <a href="{{ route('admin.dashboard') }}" class="btn btn-link">Annuler</a>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            @endif
                        @show

 <!-- Subscriptions Index Section -->
@section('subscriptions-index')
    @if (Route::is('admin.subscriptions.index'))
        <div class="card">
            <div class="card-header d-flex align-items-center justify-content-between">
                <h5 class="card-title m-0 me-2">Abonnements</h5>
                <a href="{{ route('admin.subscriptions.create') }}" class="btn btn-primary">Créer un abonnement</a>
            </div>
            <div class="table-responsive">
                <table class="table table-borderless border-top">
                    <thead class="border-bottom">
                        <tr>
                            <th>ID</th>
                            <th>Nom</th>
                            <th>Prix mensuel</th>
                            <th>Prix annuel</th>
                            <th>Description</th>
                            <th>WhatsApp</th>
                            <th>Réduction (%)</th>
                            <th>Boost J'aime</th>
                            <th>Publications disponibles</th>
                            <th>Boost Commentaires</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($subscriptions ?? [] as $subscription)
                            <tr>
                                <td>{{ $subscription->id }}</td>
                                <td>{{ $subscription->name }}</td>
                                <td>{{ number_format($subscription->monthly_price, 2) }}€</td>
                                <td>{{ number_format($subscription->yearly_price, 2) }}€</td>
                                <td>{{ $subscription->description ?? 'Aucune description' }}</td>
                                <td>
                                    <span class="{{ $subscription->whatsapp ? 'text-success' : 'text-danger' }}">
                                        {{ $subscription->whatsapp ? 'Oui' : 'Non' }}
                                    </span>
                                </td>
                                <td>{{ number_format($subscription->discount, 2) }}</td>
                                <td>{{ $subscription->boost_likes }}</td>
                                <td>{{ $subscription->available_posts }}</td>
                                <td>{{ $subscription->boost_comments }}</td>
                                <td>
                                    <a href="{{ route('admin.subscriptions.edit', $subscription) }}" class="btn btn-sm btn-outline-primary me-2">Modifier</a>
                                    <form action="{{ route('admin.subscriptions.destroy', $subscription) }}" method="POST" style="display: inline;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('Êtes-vous sûr ?')">Supprimer</button>
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

<!-- Subscriptions Create Section -->
@section('subscriptions-create')
    @if (Route::is('admin.subscriptions.create'))
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Créer un abonnement</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.subscriptions.store') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label for="name" class="form-label">Nom</label>
                        <input type="text" class="form-control" name="name" id="name" required>
                        @error('name')
                            <div class="text-danger mt-1">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label for="monthly_price" class="form-label">Prix mensuel</label>
                        <input type="number" step="0.01" class="form-control" name="monthly_price" id="monthly_price" required>
                        @error('monthly_price')
                            <div class="text-danger mt-1">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label for="yearly_price" class="form-label">Prix annuel</label>
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
                        <label for="whatsapp" class="form-label">Accès WhatsApp</label>
                        <select class="form-select" name="whatsapp" id="whatsapp" required>
                            <option value="1">Oui</option>
                            <option value="0" selected>Non</option>
                        </select>
                        @error('whatsapp')
                            <div class="text-danger mt-1">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label for="discount" class="form-label">Réduction (%)</label>
                        <input type="number" step="0.01" class="form-control" name="discount" id="discount" required>
                        @error('discount')
                            <div class="text-danger mt-1">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label for="boost_likes" class="form-label">Boost J'aime</label>
                        <input type="number" class="form-control" name="boost_likes" id="boost_likes" required>
                        @error('boost_likes')
                            <div class="text-danger mt-1">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label for="available_posts" class="form-label">Publications disponibles</label>
                        <input type="number" class="form-control" name="available_posts" id="available_posts" required>
                        @error('available_posts')
                            <div class="text-danger mt-1">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label for="boost_comments" class="form-label">Boost Commentaires</label>
                        <input type="number" class="form-control" name="boost_comments" id="boost_comments" required>
                        @error('boost_comments')
                            <div class="text-danger mt-1">{{ $message }}</div>
                        @enderror
                    </div>
                    <div>
                        <button type="submit" class="btn btn-primary">Créer</button>
                        <a href="{{ route('admin.subscriptions.index') }}" class="btn btn-link">Annuler</a>
                    </div>
                </form>
            </div>
        </div>
    @endif
@show

<!-- Subscriptions Edit Section -->
@section('subscriptions-edit')
    @if (Route::is('admin.subscriptions.edit'))
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Modifier un abonnement</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.subscriptions.update', $subscription) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="mb-3">
                        <label for="name" class="form-label">Nom</label>
                        <input type="text" class="form-control" name="name" id="name" value="{{ old('name', $subscription->name) }}" required>
                        @error('name')
                            <div class="text-danger mt-1">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label for="monthly_price" class="form-label">Prix mensuel</label>
                        <input type="number" step="0.01" class="form-control" name="monthly_price" id="monthly_price" value="{{ old('monthly_price', $subscription->monthly_price) }}" required>
                        @error('monthly_price')
                            <div class="text-danger mt-1">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label for="yearly_price" class="form-label">Prix annuel</label>
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
                        <label for="whatsapp" class="form-label">Accès WhatsApp</label>
                        <select class="form-select" name="whatsapp" id="whatsapp" required>
                            <option value="1" {{ $subscription->whatsapp ? 'selected' : '' }}>Oui</option>
                            <option value="0" {{ !$subscription->whatsapp ? 'selected' : '' }}>Non</option>
                        </select>
                        @error('whatsapp')
                            <div class="text-danger mt-1">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label for="discount" class="form-label">Réduction (%)</label>
                        <input type="number" step="0.01" class="form-control" name="discount" id="discount" value="{{ old('discount', $subscription->discount) }}" required>
                        @error('discount')
                            <div class="text-danger mt-1">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label for="boost_likes" class="form-label">Boost J'aime</label>
                        <input type="number" class="form-control" name="boost_likes" id="boost_likes" value="{{ old('boost_likes', $subscription->boost_likes) }}" required>
                        @error('boost_likes')
                            <div class="text-danger mt-1">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label for="available_posts" class="form-label">Publications disponibles</label>
                        <input type="number" class="form-control" name="available_posts" id="available_posts" value="{{ old('available_posts', $subscription->available_posts) }}" required>
                        @error('available_posts')
                            <div class="text-danger mt-1">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label for="boost_comments" class="form-label">Boost Commentaires</label>
                        <input type="number" class="form-control" name="boost_comments" id="boost_comments" value="{{ old('boost_comments', $subscription->boost_comments) }}" required>
                        @error('boost_comments')
                            <div class="text-danger mt-1">{{ $message }}</div>
                        @enderror
                    </div>
                    <div>
                        <button type="submit" class="btn btn-primary">Mettre à jour</button>
                        <a href="{{ route('admin.subscriptions.index') }}" class="btn btn-link">Annuler</a>
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
                                        <h5 class="card-title m-0 me-2">Abonnements actifs</h5>
                                        <div class="d-flex align-items-center">
                                            <form action="{{ route('admin.subscriptions.active') }}" method="GET" class="d-flex">
                                                <input type="text" class="form-control me-2" name="search" placeholder="Rechercher par nom d'utilisateur..." value="{{ request('search') }}" style="width: 200px;">
                                                <button type="submit" class="btn btn-outline-primary me-2">Rechercher</button>
                                            </form>
                                            <a href="{{ route('admin.subscriptions.active.create') }}" class="btn btn-primary">Créer un abonnement actif</a>
                                        </div>
                                    </div>
                                    <div class="table-responsive">
                                        <table class="table table-borderless border-top">
                                            <thead class="border-bottom">
                                                <tr>
                                                    <th>ID</th>
                                                    <th>Utilisateur</th>
                                                    <th>Abonnement</th>
                                                    <th>Mode de tarification</th>
                                                    <th>Date d'expiration</th>
                                                    <th>Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($userSubscriptions ?? [] as $userSubscription)
                                                    <tr>
                                                        <td>{{ $userSubscription->id }}</td>
                                                        <td>{{ $userSubscription->user->name ?? 'N/A' }}</td>
                                                        <td>{{ $userSubscription->subscription->name ?? 'N/A' }}</td>
                                                        <td>{{ ucfirst($userSubscription->pricing_mode == 'monthly' ? 'Mensuel' : 'Annuel') }}</td>
                                                        <td>{{ $userSubscription->expires_at ? $userSubscription->expires_at->format('d-m-Y') : 'N/A' }}</td>
                                                        <td>
                                                            <a href="{{ route('admin.subscriptions.active.edit', $userSubscription) }}" class="btn btn-sm btn-outline-primary me-2">Modifier</a>
                                                            <form action="{{ route('admin.subscriptions.active.destroy', $userSubscription) }}" method="POST" style="display: inline;">
                                                                @csrf
                                                                @method('DELETE')
                                                                <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('Êtes-vous sûr ?')">Supprimer</button>
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
                                        <h5 class="card-title mb-0">Créer un abonnement actif</h5>
                                    </div>
                                    <div class="card-body">
                                        <form action="{{ route('admin.subscriptions.active.store') }}" method="POST">
                                            @csrf
                                            <div class="mb-3">
                                                <label for="user_id" class="form-label">Utilisateur</label>
                                                <select class="form-select" name="user_id" id="user_id" required>
                                                    <option value="">Sélectionner un utilisateur</option>
                                                    @foreach (\App\Models\User::all() as $user)
                                                        <option value="{{ $user->id }}">{{ $user->name }} ({{ $user->email }})</option>
                                                    @endforeach
                                                </select>
                                                @error('user_id')
                                                    <div class="text-danger mt-1">{{ $message }}</div>
                                                @enderror
                                            </div>
                                            <div class="mb-3">
                                                <label for="subscription_id" class="form-label">Abonnement</label>
                                                <select class="form-select" name="subscription_id" id="subscription_id" required>
                                                    <option value="">Sélectionner un abonnement</option>
                                                    @foreach (\App\Models\Subscription::all() as $subscription)
                                                        <option value="{{ $subscription->id }}">{{ $subscription->name }}</option>
                                                    @endforeach
                                                </select>
                                                @error('subscription_id')
                                                    <div class="text-danger mt-1">{{ $message }}</div>
                                                @enderror
                                            </div>
                                            <div class="mb-3">
                                                <label for="pricing_mode" class="form-label">Mode de tarification</label>
                                                <select class="form-select" name="pricing_mode" id="pricing_mode" required>
                                                    <option value="monthly">Mensuel</option>
                                                    <option value="yearly">Annuel</option>
                                                </select>
                                                @error('pricing_mode')
                                                    <div class="text-danger mt-1">{{ $message }}</div>
                                                @enderror
                                            </div>
                                            <div class="mb-3">
                                                <label for="expires_at" class="form-label">Date d'expiration</label>
                                                <input type="date" class="form-control" name="expires_at" id="expires_at" required>
                                                @error('expires_at')
                                                    <div class="text-danger mt-1">{{ $message }}</div>
                                                @enderror
                                            </div>
                                            <div>
                                                <button type="submit" class="btn btn-primary">Créer</button>
                                                <a href="{{ route('admin.subscriptions.active') }}" class="btn btn-link">Annuler</a>
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
                                        <h5 class="card-title mb-0">Modifier un abonnement actif</h5>
                                    </div>
                                    <div class="card-body">
                                        <form action="{{ route('admin.subscriptions.active.update', $userSubscription) }}" method="POST">
                                            @csrf
                                            @method('PUT')
                                            <div class="mb-3">
                                                <label for="user_id" class="form-label">Utilisateur</label>
                                                <select class="form-select" name="user_id" id="user_id" required>
                                                    <option value="">Sélectionner un utilisateur</option>
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
                                                <label for="subscription_id" class="form-label">Abonnement</label>
                                                <select class="form-select" name="subscription_id" id="subscription_id" required>
                                                    <option value="">Sélectionner un abonnement</option>
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
                                                <label for="pricing_mode" class="form-label">Mode de tarification</label>
                                                <select class="form-select" name="pricing_mode" id="pricing_mode" required>
                                                    <option value="monthly" {{ $userSubscription->pricing_mode == 'monthly' ? 'selected' : '' }}>Mensuel</option>
                                                    <option value="yearly" {{ $userSubscription->pricing_mode == 'yearly' ? 'selected' : '' }}>Annuel</option>
                                                </select>
                                                @error('pricing_mode')
                                                    <div class="text-danger mt-1">{{ $message }}</div>
                                                @enderror
                                            </div>
                                            <div class="mb-3">
                                                <label for="expires_at" class="form-label">Date d'expiration</label>
                                                <input type="date" class="form-control" name="expires_at" id="expires_at" value="{{ $userSubscription->expires_at ? $userSubscription->expires_at->format('Y-m-d') : '' }}" required>
                                                @error('expires_at')
                                                    <div class="text-danger mt-1">{{ $message }}</div>
                                                @enderror
                                            </div>
                                            <div>
                                                <button type="submit" class="btn btn-primary">Mettre à jour</button>
                                                <a href="{{ route('admin.subscriptions.active') }}" class="btn btn-link">Annuler</a>
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
                                        <h5 class="card-title m-0 me-2">Codes promotionnels</h5>
                                        <a href="{{ route('admin.coupons.create') }}" class="btn btn-primary">Créer un code promotionnel</a>
                                    </div>
                                    <div class="table-responsive">
                                        <table class="table table-borderless border-top" id="couponsTable">
                                            <thead class="border-bottom">
                                                <tr>
                                                    <th>ID</th>
                                                    <th>Code</th>
                                                    <th>Réduction (%)</th>
                                                    <th>Date d'expiration</th>
                                                    <th>Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($coupons ?? [] as $coupon)
                                                    <tr>
                                                        <td>{{ $coupon->id }}</td>
                                                        <td>{{ $coupon->code }}</td>
                                                        <td>{{ number_format($coupon->discount, 2) }}</td>
                                                        <td>{{ $coupon->expires_at ? $coupon->expires_at->format('d-m-Y') : 'N/A' }}</td>
                                                        <td>
                                                            <a href="{{ route('admin.coupons.edit', $coupon) }}" class="btn btn-sm btn-outline-primary me-2">Modifier</a>
                                                            <form action="{{ route('admin.coupons.destroy', $coupon) }}" method="POST" style="display: inline;">
                                                                @csrf
                                                                @method('DELETE')
                                                                <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('Êtes-vous sûr ?')">Supprimer</button>
                                                            </form>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                <script>
                                    document.addEventListener('DOMContentLoaded', function () {
                                        $('#couponsTable').DataTable({
                                            dom: 'frtip',
                                            pageLength: 10,
                                            ordering: true,
                                            searching: true,
                                            paging: true,
                                            info: true,
                                            language: {
                                                search: '',
                                                searchPlaceholder: 'Rechercher des codes promotionnels...',
                                                emptyTable: 'Aucun code promotionnel trouvé',
                                                zeroRecords: 'Aucun résultat correspondant',
                                                lengthMenu: 'Afficher _MENU_ entrées',
                                                info: 'Affichage de _START_ à _END_ sur _TOTAL_ entrées',
                                                paginate: {
                                                    first: 'Premier',
                                                    last: 'Dernier',
                                                    next: 'Suivant',
                                                    previous: 'Précédent'
                                                }
                                            }
                                        });
                                    });
                                </script>
                            @endif
                        @show

                        @section('coupons-create')
                            @if (Route::is('admin.coupons.create'))
                                <div class="card">
                                    <div class="card-header">
                                        <h5 class="card-title mb-0">Créer un code promotionnel</h5>
                                    </div>
                                    <div class="card-body">
                                        <form action="{{ route('admin.coupons.store') }}" method="POST">
                                            @csrf
                                            <div class="mb-3">
                                                <label for="code" class="form-label">Code</label>
                                                <input type="text" class="form-control" name="code" id="code" value="{{ old('code') }}" required>
                                                @error('code')
                                                    <div class="text-danger mt-1">{{ $message }}</div>
                                                @enderror
                                            </div>
                                            <div class="mb-3">
                                                <label for="discount" class="form-label">Réduction (%)</label>
                                                <input type="number" step="0.01" class="form-control" name="discount" id="discount" value="{{ old('discount') }}" required>
                                                @error('discount')
                                                    <div class="text-danger mt-1">{{ $message }}</div>
                                                @enderror
                                            </div>
                                            <div class="mb-3">
                                                <label for="type" class="form-label">Type</label>
                                                <select class="form-select" name="type" id="type" required>
                                                    <option value="percentage" {{ old('type') == 'percentage' ? 'selected' : '' }}>Pourcentage</option>
                                                    <option value="fixed" {{ old('type') == 'fixed' ? 'selected' : '' }}>Fixe</option>
                                                </select>
                                                @error('type')
                                                    <div class="text-danger mt-1">{{ $message }}</div>
                                                @enderror
                                            </div>
                                            <div class="mb-3">
                                                <label for="expires_at" class="form-label">Date d'expiration</label>
                                                <input type="date" class="form-control" name="expires_at" id="expires_at" value="{{ old('expires_at') }}">
                                                @error('expires_at')
                                                    <div class="text-danger mt-1">{{ $message }}</div>
                                                @enderror
                                            </div>
                                            <div>
                                                <button type="submit" class="btn btn-primary">Créer</button>
                                                <a href="{{ route('admin.coupons.index') }}" class="btn btn-link">Annuler</a>
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
                                        <h5 class="card-title mb-0">Modifier un code promotionnel</h5>
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
                                                <label for="discount" class="form-label">Réduction (%)</label>
                                                <input type="number" step="0.01" class="form-control" name="discount" id="discount" value="{{ old('discount', $coupon->discount) }}" required>
                                                @error('discount')
                                                    <div class="text-danger mt-1">{{ $message }}</div>
                                                @enderror
                                            </div>
                                            <div class="mb-3">
                                                <label for="type" class="form-label">Type</label>
                                                <select class="form-select" name="type" id="type" required>
                                                    <option value="percentage" {{ old('type', $coupon->type) == 'percentage' ? 'selected' : '' }}>Pourcentage</option>
                                                    <option value="fixed" {{ old('type', $coupon->type) == 'fixed' ? 'selected' : '' }}>Fixe</option>
                                                </select>
                                                @error('type')
                                                    <div class="text-danger mt-1">{{ $message }}</div>
                                                @enderror
                                            </div>
                                            <div class="mb-3">
                                                <label for="expires_at" class="form-label">Date d'expiration</label>
                                                <input type="date" class="form-control" name="expires_at" id="expires_at" value="{{ old('expires_at', $coupon->expires_at ? $coupon->expires_at->format('Y-m-d') : '') }}">
                                                @error('expires_at')
                                                    <div class="text-danger mt-1">{{ $message }}</div>
                                                @enderror
                                            </div>
                                            <div>
                                                <button type="submit" class="btn btn-primary">Mettre à jour</button>
                                                <a href="{{ route('admin.coupons.index') }}" class="btn btn-link">Annuler</a>
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
                                        <h5 class="card-title m-0 me-2">Demande de boost d'interactions</h5>
                                    </div>
                                    <div class="table-responsive">
                                        <table class="table table-borderless border-top">
                                            <thead class="border-bottom">
                                                <tr>
                                                    <th>ID</th>
                                                    <th>Utilisateur</th>
                                                    <th>Utilisateur LinkedIn</th>
                                                    <th>ID de publication</th>
                                                    <th>URL de publication</th>
                                                    <th>J'aime</th>
                                                    <th>Commentaires</th>
                                                    <th>Message</th>
                                                    <th>Statut</th>
                                                    <th>Créé le</th>
                                                    <th>Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($boostInteractions ?? [] as $boostInteraction)
                                                    <tr>
                                                        <td>{{ $boostInteraction->id }}</td>
                                                        <td>{{ $boostInteraction->user->name ?? 'N/A' }}</td>
                                                        <td>{{ $boostInteraction->linkedinUser->name ?? 'N/A' }}</td>
                                                        <td>{{ $boostInteraction->post_id }}</td>
                                                        <td>
                                                            <a href="{{ $boostInteraction->post_url }}" target="_blank">{{ Str::limit($boostInteraction->post_url, 30) }}</a>
                                                        </td>
                                                        <td>{{ $boostInteraction->nb_likes }}</td>
                                                        <td>{{ $boostInteraction->nb_comments }}</td>
                                                        <td>{{ Str::limit($boostInteraction->message, 30) ?? 'N/A' }}</td>
                                                        <td>
                                                            <span class="badge {{ $boostInteraction->status == 'pending' ? 'bg-warning' : ($boostInteraction->status == 'accepted' ? 'bg-success' : 'bg-danger') }}">
                                                                {{ ucfirst($boostInteraction->status == 'pending' ? 'En attente' : ($boostInteraction->status == 'accepted' ? 'Accepté' : 'Refusé')) }}
                                                            </span>
                                                        </td>
                                                        <td>{{ $boostInteraction->created_at ? $boostInteraction->created_at->format('d-m-Y H:i:s') : 'N/A' }}</td>
                                                        <td>
                                                            @if ($boostInteraction->status == 'pending')
                                                                <form action="{{ route('admin.boostinteractions.update', $boostInteraction) }}" method="POST" style="display: inline;">
                                                                    @csrf
                                                                    @method('PUT')
                                                                    <input type="hidden" name="status" value="accepted">
                                                                    <button type="submit" class="btn btn-sm btn-outline-success me-2">Accepter</button>
                                                                </form>
                                                                <form action="{{ route('admin.boostinteractions.update', $boostInteraction) }}" method="POST" style="display: inline;">
                                                                    @csrf
                                                                    @method('PUT')
                                                                    <input type="hidden" name="status" value="declined">
                                                                    <button type="submit" class="btn btn-sm btn-outline-danger me-2">Refuser</button>
                                                                </form>
                                                            @endif
                                                            <form action="{{ route('admin.boostinteractions.destroy', $boostInteraction) }}" method="POST" style="display: inline;">
                                                                @csrf
                                                                @method('DELETE')
                                                                <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('Êtes-vous sûr ?')">Supprimer</button>
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
   <!-- Alerts Index Section -->
   @section('alerts-index')
    @if (Route::is('admin.alerts.index'))
        <div class="card">
            <div class="card-header d-flex align-items-center justify-content-between">
                <h5 class="card-title m-0 me-2">Alerts</h5>
                <div class="d-flex align-items-center">
                    <form action="{{ route('admin.alerts.index') }}" method="GET" class="d-flex">
                        <input type="text" class="form-control me-2" name="search" placeholder="Search by message..." value="{{ request('search') }}" style="width: 200px;">
                        <button type="submit" class="btn btn-outline-primary me-2">Search</button>
                        <a href="{{ route('admin.alerts.index') }}" class="btn btn-outline-secondary me-2">Clear</a>
                    </form>
                    <a href="{{ route('admin.alerts.create') }}" class="btn btn-primary">Create Alert</a>
                </div>
            </div>
            <div class="table-responsive">
                <table class="table table-borderless border-top" id="alertsTable">
                    <thead class="border-bottom">
                        <tr>
                            <th>ID</th>
                            <th>Message</th>
                            <th>Start At</th>
                            <th>End At</th>
                            <th>Created At</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($alerts as $alert)
                            <tr>
                                <td>{{ $alert->id }}</td>
                                <td>{{ Str::limit($alert->message, 50) }}</td>
                                <td>{{ $alert->start_at ? $alert->start_at->format('Y-m-d H:i:s') : 'N/A' }}</td>
                                <td>{{ $alert->end_at ? $alert->end_at->format('Y-m-d H:i:s') : 'N/A' }}</td>
                                <td>{{ $alert->created_at->format('Y-m-d H:i:s') }}</td>
                                <td>
                                    <a href="{{ route('admin.alerts.edit', $alert) }}" class="btn btn-sm btn-outline-primary me-2">Edit</a>
                                    <form action="{{ route('admin.alerts.destroy', $alert) }}" method="POST" style="display: inline;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('Are you sure?')">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center">No alerts found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
                <div class="mt-3">
                    {{ $alerts->appends(['search' => request('search')])->links('vendor.pagination.bootstrap-5') }}
                </div>
            </div>
        </div>
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                $('#alertsTable').DataTable({
                    dom: 'frtip',
                    pageLength: 10,
                    ordering: false,
                    searching: false,
                    paging: false,
                    info: false
                });
            });
        </script>
    @endif
@show

                        <!-- Alerts Create Section -->
                        @section('alerts-create')
    @if (Route::is('admin.alerts.create'))
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Create Alert</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.alerts.store') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label for="message" class="form-label">Message</label>
                        <textarea class="form-control" name="message" id="message" rows="5" required>{{ old('message') }}</textarea>
                        @error('message')
                            <div class="text-danger mt-1">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label for="start_at" class="form-label">Start At</label>
                        <input type="datetime-local" class="form-control" name="start_at" id="start_at" value="{{ old('start_at') }}">
                        @error('start_at')
                            <div class="text-danger mt-1">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label for="end_at" class="form-label">End At</label>
                        <input type="datetime-local" class="form-control" name="end_at" id="end_at" value="{{ old('end_at') }}">
                        @error('end_at')
                            <div class="text-danger mt-1">{{ $message }}</div>
                        @enderror
                    </div>
                    <div>
                        <button type="submit" class="btn btn-primary">Create</button>
                        <a href="{{ route('admin.alerts.index') }}" class="btn btn-link">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    @endif
@show

                        <!-- Alerts Edit Section -->
                        @section('alerts-edit')
    @if (Route::is('admin.alerts.edit'))
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Edit Alert</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.alerts.update', $alert) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="mb-3">
                        <label for="message" class="form-label">Message</label>
                        <textarea class="form-control" name="message" id="message" rows="5" required>{{ old('message', $alert->message) }}</textarea>
                        @error('message')
                            <div class="text-danger mt-1">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label for="start_at" class="form-label">Start At</label>
                        <input type="datetime-local" class="form-control" name="start_at" id="start_at" value="{{ old('start_at', $alert->start_at ? $alert->start_at->format('Y-m-d\TH:i') : '') }}">
                        @error('start_at')
                            <div class="text-danger mt-1">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label for="end_at" class="form-label">End At</label>
                        <input type="datetime-local" class="form-control" name="end_at" id="end_at" value="{{ old('end_at', $alert->end_at ? $alert->end_at->format('Y-m-d\TH:i') : '') }}">
                        @error('end_at')
                            <div class="text-danger mt-1">{{ $message }}</div>
                        @enderror
                    </div>
                    <div>
                        <button type="submit" class="btn btn-primary">Update</button>
                        <a href="{{ route('admin.alerts.index') }}" class="btn btn-link">Cancel</a>
                    </div>
                </form>
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
<script src="{{ asset('assets/vendor/libs/apex-charts/apexcharts.js') }}"></script>
<script src="{{ asset('assets/vendor/libs/datatables-bs5/datatables.bootstrap5.js') }}"></script>