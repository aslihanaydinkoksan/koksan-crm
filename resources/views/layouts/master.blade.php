<!DOCTYPE html>
<html lang="tr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>KÖKSAN CRM Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <style>
        body {
            background-color: #f8f9fa;
            overflow-x: hidden;
        }

        #sidebar {
            min-width: 250px;
            max-width: 250px;
            min-height: 100vh;
            background-color: #212529;
            color: #fff;
            transition: all 0.3s;
        }

        #sidebar .sidebar-header {
            padding: 20px;
            background: #1a1e21;
        }

        #sidebar ul.components {
            padding: 20px 0;
        }

        #sidebar ul li a {
            padding: 10px 20px;
            font-size: 0.95em;
            display: block;
            color: #adb5bd;
            text-decoration: none;
            transition: 0.2s;
        }

        #sidebar ul li a:hover {
            color: #fff;
            background: #343a40;
        }

        #sidebar ul li a i {
            margin-right: 10px;
        }

        .sidebar-heading {
            padding: 10px 20px;
            font-size: 0.75em;
            text-transform: uppercase;
            color: #6c757d;
            font-weight: bold;
            letter-spacing: 1px;
            margin-top: 10px;
        }

        #content {
            width: 100%;
        }

        .navbar {
            background-color: #fff;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.04);
        }
    </style>
</head>

<body>

    <div class="d-flex">
        <nav id="sidebar">
            <div class="sidebar-header">
                <h4 class="mb-0 text-white fw-bold"><i class="bi bi-hexagon-fill text-primary me-2"></i>KÖKSAN</h4>
                <small class="text-muted">Kurumsal CRM</small>
            </div>

            <ul class="list-unstyled components">
                <div class="sidebar-heading">Genel Bakış</div>
                <li><a href="#"><i class="bi bi-info-square"></i> Detaylar</a></li>
                <li><a href="#"><i class="bi bi-person-lines-fill"></i> İletişim</a></li>
                <li><a href="#"><i class="bi bi-bar-chart-line"></i> Analiz & Raporlar</a></li>

                <div class="sidebar-heading">Ticari</div>
                <li><a href="#"><i class="bi bi-box-seam"></i> Ürünler</a></li>
                <li><a href="#"><i class="bi bi-currency-dollar"></i> Fırsatlar</a></li>
                <li><a href="/samples"><i class="bi bi-droplet"></i> Numuneler</a></li>

                <div class="sidebar-heading">Teknik</div>
                <li><a href="#"><i class="bi bi-geo-alt"></i> Ziyaretler</a></li>
                <li><a href="#"><i class="bi bi-gear"></i> Makineler</a></li>
                <li><a href="#"><i class="bi bi-clipboard-check"></i> Testler</a></li>

                <div class="sidebar-heading">Destek</div>
                <li><a href="#"><i class="bi bi-truck"></i> Lojistik</a></li>
                <li><a href="#"><i class="bi bi-exclamation-triangle"></i> Şikayetler</a></li>
                <li><a href="#"><i class="bi bi-arrow-return-left"></i> İadeler</a></li>
            </ul>
        </nav>

        <div id="content">
            <nav class="navbar navbar-expand-lg navbar-light px-4 py-3">
                <div class="container-fluid">
                    <span class="navbar-brand mb-0 h1">Dashboard</span>

                    <div class="d-flex align-items-center ms-auto">
                        <span class="me-3 fw-bold text-secondary">
                            <i class="bi bi-person-circle me-1"></i> {{ Auth::user()->name ?? 'Kullanıcı' }}
                        </span>
                        <form action="{{ route('logout') }}" method="POST" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-sm btn-outline-danger">
                                <i class="bi bi-box-arrow-right"></i> Çıkış Yap
                            </button>
                        </form>
                    </div>
                </div>
            </nav>

            <div class="container-fluid p-4">
                @yield('content')
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
