<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css" />
    <link rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/overlayscrollbars@2.11.0/styles/overlayscrollbars.min.css" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@4.0.0/dist/css/adminlte.min.css" />
    <style>
        .app-content-header {
            margin-bottom: 1rem;
        }

        .app-content-header .breadcrumb {
            background: transparent;
            padding: 0;
            margin: 0;
        }

        .app-content .card {
            border: 0;
            border-radius: 1rem;
            box-shadow: 0 0.35rem 1rem rgba(15, 23, 42, 0.08);
        }

        .app-content .card-header {
            background: transparent;
            border-bottom: 0;
            padding-bottom: 0.2rem;
        }

        .app-content .card-body {
            padding-top: 0.5rem;
        }

        .app-content .btn {
            border-radius: 999px;
        }

        .app-content .form-control,
        .app-content .form-select {
            border-radius: 0.8rem;
        }

        .app-content .table thead th {
            font-size: 0.75rem;
            letter-spacing: 0.04em;
            text-transform: uppercase;
            color: #6c757d;
        }

        .app-content .table td,
        .app-content .table th {
            padding: 0.8rem 1rem;
            vertical-align: middle;
        }

        .app-content .alert {
            border-radius: 1rem;
        }

        @media (max-width: 575.98px) {
            .app-content-header .d-flex {
                flex-direction: column;
                align-items: flex-start;
            }
        }
    </style>
</head>

<body class="layout-fixed sidebar-expand-lg bg-body-tertiary">
    <div class="app-wrapper">

        @include('partials.navigation')

        @include('partials.sidebar')

        <!-- Main content -->
        <main class="app-main">
            @yield('content')
        </main>

    </div>
    <!-- Bootstrap + Popper + AdminLTE (JS) -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/overlayscrollbars@2.11.0/browser/overlayscrollbars.browser.es6.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/admin-lte@4.0.0/dist/js/adminlte.min.js"></script>
</body>

</html>
