<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($title ?? 'Emigratie Calculator') ?> - Italië Calculator</title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    
    <style>
        :root {
            --primary-color: #009246;
            --secondary-color: #CE2B37;
            --accent-color: #F4F5F7;
        }
        
        .navbar-brand {
            font-weight: bold;
            color: var(--primary-color) !important;
        }
        
        /* Sidebar menu button (mobile) */
        .sidebar-toggle {
            position: fixed;
            bottom: 20px;
            right: 20px;
            z-index: 1040;
            background-color: var(--primary-color);
            border: none;
            width: 56px;
            height: 56px;
            border-radius: 50%;
            box-shadow: 0 4px 12px rgba(0,0,0,0.3);
            color: white;
            font-size: 1.5rem;
        }
        
        .sidebar-toggle:hover {
            background-color: #007a38;
        }
        
        /* Desktop sidebar */
        .sidebar {
            min-height: calc(100vh - 56px);
            background-color: var(--accent-color);
            padding: 20px 0;
        }
        
        .sidebar .nav-link {
            color: #333;
            padding: 10px 20px;
            margin-bottom: 5px;
        }
        
        .sidebar .nav-link:hover, .sidebar .nav-link.active {
            background-color: var(--primary-color);
            color: white;
        }
        
        /* Mobile off-canvas sidebar */
        .offcanvas-sidebar {
            background-color: var(--accent-color);
        }
        
        .offcanvas-sidebar .nav-link {
            color: #333;
            padding: 10px 20px;
            margin-bottom: 5px;
        }
        
        .offcanvas-sidebar .nav-link:hover,
        .offcanvas-sidebar .nav-link.active {
            background-color: var(--primary-color);
            color: white;
        }
        
        .card {
            border: none;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            margin-bottom: 20px;
        }
        
        .btn-primary {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
        }
        
        .btn-primary:hover {
            background-color: #007a38;
            border-color: #007a38;
        }
        
        .alert {
            border-radius: 8px;
        }
        
        .stat-card {
            text-align: center;
            padding: 20px;
        }
        
        .stat-card .stat-value {
            font-size: 2rem;
            font-weight: bold;
        }
        
        .stat-card .stat-label {
            color: #6c757d;
            font-size: 0.9rem;
        }
        
        .stat-card.positive {
            border-left: 4px solid #28a745;
        }
        
        .stat-card.negative {
            border-left: 4px solid #dc3545;
        }
        
        .stat-card.neutral {
            border-left: 4px solid #17a2b8;
        }
        
        /* Improve table readability */
        .table {
            font-size: 0.95rem;
        }
        
        /* Better modal scrolling */
        .modal-body {
            max-height: 70vh;
            overflow-y: auto;
        }
        
        /* Improve chart responsiveness */
        canvas {
            max-width: 100%;
            height: auto !important;
        }
        
        /* Mobile responsive improvements */
        @media (max-width: 767.98px) {
            .sidebar-toggle {
                display: block;
            }
            
            .stat-card .stat-value {
                font-size: 1.5rem;
            }
            
            .card {
                margin-bottom: 15px;
            }
            
            .navbar-brand {
                font-size: 0.9rem;
            }
            
            h1 {
                font-size: 1.75rem;
            }
            
            h2 {
                font-size: 1.5rem;
            }
            
            h3 {
                font-size: 1.25rem;
            }
            
            /* Ensure tables are scrollable */
            .table-responsive {
                overflow-x: auto;
                -webkit-overflow-scrolling: touch;
            }
            
            /* Make all tables inside cards responsive on mobile */
            .card-body > table {
                display: block;
                overflow-x: auto;
                -webkit-overflow-scrolling: touch;
                white-space: nowrap;
            }
            
            /* Better button sizing on mobile */
            .btn {
                padding: 0.5rem 0.75rem;
                font-size: 0.9rem;
            }
            
            .btn-sm {
                padding: 0.25rem 0.5rem;
                font-size: 0.8rem;
            }
            
            /* Better spacing on mobile */
            .px-md-4 {
                padding-left: 1rem !important;
                padding-right: 1rem !important;
            }
            
            /* Stack columns on mobile */
            .col-md-6.mb-4 {
                margin-bottom: 1rem !important;
            }
            
            /* Ensure forms are full width on mobile */
            .modal-dialog {
                margin: 0.5rem;
            }
            
            .input-group-text {
                font-size: 0.9rem;
            }
        }
        
        /* Hide sidebar toggle on desktop */
        @media (min-width: 768px) {
            .sidebar-toggle {
                display: none;
            }
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm">
        <div class="container-fluid">
            <a class="navbar-brand" href="/">
                <i class="bi bi-geo-alt-fill"></i> Emigratie Italië Calculator
            </a>
            
            <?php if (session()->get('isLoggedIn')): ?>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                    <span class="navbar-toggler-icon"></span>
                </button>
                
                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav ms-auto">
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown">
                                <i class="bi bi-person-circle"></i> <?= esc(session()->get('username')) ?>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li><a class="dropdown-item" href="/profile"><i class="bi bi-person"></i> Profiel</a></li>
                                <?php if (session()->get('role') === 'admin'): ?>
                                    <li><hr class="dropdown-divider"></li>
                                    <li><a class="dropdown-item" href="/admin"><i class="bi bi-shield-lock"></i> Admin</a></li>
                                <?php endif; ?>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item" href="/logout"><i class="bi bi-box-arrow-right"></i> Uitloggen</a></li>
                            </ul>
                        </li>
                    </ul>
                </div>
            <?php else: ?>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                    <span class="navbar-toggler-icon"></span>
                </button>
                
                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav ms-auto">
                        <li class="nav-item">
                            <a class="nav-link" href="/help">
                                <i class="bi bi-question-circle"></i> Help
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="/contact">
                                <i class="bi bi-envelope"></i> Contact
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="/login">
                                <i class="bi bi-box-arrow-in-right"></i> Inloggen
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="/register">
                                <i class="bi bi-person-plus"></i> Registreren
                            </a>
                        </li>
                    </ul>
                </div>
            <?php endif; ?>
        </div>
    </nav>

    <div class="container-fluid">
        <div class="row">
            <?php if (session()->get('isLoggedIn')): ?>
                <!-- Desktop Sidebar (visible on md and up) -->
                <nav class="col-md-2 d-none d-md-block sidebar">
                    <div class="position-sticky">
                        <ul class="nav flex-column">
                            <li class="nav-item">
                                <a class="nav-link <?= uri_string() == 'dashboard' ? 'active' : '' ?>" href="/dashboard">
                                    <i class="bi bi-speedometer2"></i> Dashboard
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link <?= uri_string() == 'start-position' ? 'active' : '' ?>" href="/start-position">
                                    <i class="bi bi-house-door"></i> Startpositie NL
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link <?= uri_string() == 'income' ? 'active' : '' ?>" href="/income">
                                    <i class="bi bi-cash-coin"></i> Inkomsten
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link <?= uri_string() == 'property' ? 'active' : '' ?>" href="/property">
                                    <i class="bi bi-building"></i> Vastgoed IT
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link <?= uri_string() == 'expenses' ? 'active' : '' ?>" href="/expenses">
                                    <i class="bi bi-wallet2"></i> Maandlasten
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link <?= uri_string() == 'taxes' ? 'active' : '' ?>" href="/taxes">
                                    <i class="bi bi-receipt"></i> Belastingen
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link <?= strpos(uri_string(), 'bnb') !== false ? 'active' : '' ?>" href="/bnb">
                                    <i class="bi bi-shop"></i> B&B Module
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link <?= uri_string() == 'scenarios' ? 'active' : '' ?>" href="/scenarios">
                                    <i class="bi bi-diagram-3"></i> Scenario's
                                </a>
                            </li>
                            
                            <li class="nav-item mt-3">
                                <hr>
                            </li>
                            
                            <li class="nav-item">
                                <a class="nav-link <?= uri_string() == 'help' ? 'active' : '' ?>" href="/help">
                                    <i class="bi bi-question-circle"></i> Help
                                </a>
                            </li>
                            
                            <li class="nav-item">
                                <a class="nav-link <?= uri_string() == 'contact' ? 'active' : '' ?>" href="/contact">
                                    <i class="bi bi-envelope"></i> Contact
                                </a>
                            </li>
                            
                            <li class="nav-item mt-3">
                                <a class="nav-link" href="/export/csv">
                                    <i class="bi bi-download"></i> Export CSV
                                </a>
                            </li>
                            
                            <?php if (session()->get('role') === 'admin'): ?>
                            <li class="nav-item mt-3">
                                <hr>
                                <small class="text-muted px-3">Admin</small>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link <?= strpos(uri_string(), 'admin/users') !== false ? 'active' : '' ?>" href="/admin/users">
                                    <i class="bi bi-people"></i> Gebruikersbeheer
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link <?= strpos(uri_string(), 'admin/audit-logs') !== false ? 'active' : '' ?>" href="/admin/audit-logs">
                                    <i class="bi bi-journal-text"></i> Audit Log
                                </a>
                            </li>
                            <?php endif; ?>
                        </ul>
                    </div>
                </nav>
                
                <!-- Mobile Off-canvas Sidebar -->
                <div class="offcanvas offcanvas-start offcanvas-sidebar d-md-none" tabindex="-1" id="sidebarMenu" aria-labelledby="sidebarMenuLabel">
                    <div class="offcanvas-header">
                        <h5 class="offcanvas-title" id="sidebarMenuLabel">
                            <i class="bi bi-geo-alt-fill text-success"></i> Menu
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Sluiten"></button>
                    </div>
                    <div class="offcanvas-body">
                        <ul class="nav flex-column">
                            <li class="nav-item">
                                <a class="nav-link <?= uri_string() == 'dashboard' ? 'active' : '' ?>" href="/dashboard">
                                    <i class="bi bi-speedometer2"></i> Dashboard
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link <?= uri_string() == 'start-position' ? 'active' : '' ?>" href="/start-position">
                                    <i class="bi bi-house-door"></i> Startpositie NL
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link <?= uri_string() == 'income' ? 'active' : '' ?>" href="/income">
                                    <i class="bi bi-cash-coin"></i> Inkomsten
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link <?= uri_string() == 'property' ? 'active' : '' ?>" href="/property">
                                    <i class="bi bi-building"></i> Vastgoed IT
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link <?= uri_string() == 'expenses' ? 'active' : '' ?>" href="/expenses">
                                    <i class="bi bi-wallet2"></i> Maandlasten
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link <?= uri_string() == 'taxes' ? 'active' : '' ?>" href="/taxes">
                                    <i class="bi bi-receipt"></i> Belastingen
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link <?= strpos(uri_string(), 'bnb') !== false ? 'active' : '' ?>" href="/bnb">
                                    <i class="bi bi-shop"></i> B&B Module
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link <?= uri_string() == 'scenarios' ? 'active' : '' ?>" href="/scenarios">
                                    <i class="bi bi-diagram-3"></i> Scenario's
                                </a>
                            </li>
                            
                            <li class="nav-item mt-3">
                                <hr>
                            </li>
                            
                            <li class="nav-item">
                                <a class="nav-link <?= uri_string() == 'help' ? 'active' : '' ?>" href="/help">
                                    <i class="bi bi-question-circle"></i> Help
                                </a>
                            </li>
                            
                            <li class="nav-item">
                                <a class="nav-link <?= uri_string() == 'contact' ? 'active' : '' ?>" href="/contact">
                                    <i class="bi bi-envelope"></i> Contact
                                </a>
                            </li>
                            
                            <li class="nav-item mt-3">
                                <a class="nav-link" href="/export/csv">
                                    <i class="bi bi-download"></i> Export CSV
                                </a>
                            </li>
                            
                            <?php if (session()->get('role') === 'admin'): ?>
                            <li class="nav-item mt-3">
                                <hr>
                                <small class="text-muted px-3">Admin</small>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link <?= strpos(uri_string(), 'admin/users') !== false ? 'active' : '' ?>" href="/admin/users" data-bs-dismiss="offcanvas">
                                    <i class="bi bi-people"></i> Gebruikersbeheer
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link <?= strpos(uri_string(), 'admin/audit-logs') !== false ? 'active' : '' ?>" href="/admin/audit-logs" data-bs-dismiss="offcanvas">
                                    <i class="bi bi-journal-text"></i> Audit Log
                                </a>
                            </li>
                            <?php endif; ?>
                        </ul>
                    </div>
                </div>
                
                <!-- Floating Menu Button for Mobile -->
                <button class="sidebar-toggle d-md-none" type="button" data-bs-toggle="offcanvas" data-bs-target="#sidebarMenu" aria-controls="sidebarMenu">
                    <i class="bi bi-list"></i>
                </button>

                <!-- Main Content -->
                <main class="col-md-10 ms-sm-auto px-md-4 py-4">
            <?php else: ?>
                <main class="col-12 px-md-4 py-4">
            <?php endif; ?>
            
                    <!-- Flash Messages -->
                    <?php if (session()->getFlashdata('success')): ?>
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="bi bi-check-circle"></i> <?= session()->getFlashdata('success') ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>

                    <?php if (session()->getFlashdata('error')): ?>
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="bi bi-exclamation-triangle"></i> <?= session()->getFlashdata('error') ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>

                    <?php if (session()->getFlashdata('info')): ?>
                        <div class="alert alert-info alert-dismissible fade show" role="alert">
                            <i class="bi bi-info-circle"></i> <?= session()->getFlashdata('info') ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>

                    <!-- Page Content -->
                    <?= $this->renderSection('content') ?>
                </main>
        </div>
    </div>

    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    
    <?= $this->renderSection('scripts') ?>
</body>
</html>
