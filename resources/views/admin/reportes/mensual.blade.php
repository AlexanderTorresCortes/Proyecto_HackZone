<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reporte Mensual - HackZone</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/admin-dashboard.css') }}">
    <link rel="stylesheet" href="{{ asset('css/reporte-mensual.css') }}">
</head>

<body>

    @include('components.navbar-admin')

    <div class="admin-container">
        @include('components.sidebar-admin')

        <main class="admin-main">
            <!-- Screen Header -->
            <div class="report-header no-print">
                <div class="report-header-content">
                    <div class="report-title-section">
                        <div class="report-icon-badge">
                            <i class="fas fa-chart-line"></i>
                        </div>
                        <div>
                            <h2 class="report-title">Reporte Mensual</h2>
                            <p class="report-subtitle">{{ $datos['mes'] }}</p>
                        </div>
                    </div>
                    <div class="report-actions">
                        <button onclick="window.print()" class="btn-print">
                            <i class="fas fa-print"></i>
                            <span>Imprimir Reporte</span>
                        </button>
                        <button class="btn-export">
                            <i class="fas fa-file-pdf"></i>
                            <span>Exportar PDF</span>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Report Card -->
            <div class="report-card">

                <!-- Print Header -->
                <div class="print-header">
                    <div class="print-brand">
                        <div class="print-logo">
                            <i class="fas fa-shield-alt"></i>
                        </div>
                        <div class="print-brand-text">
                            <h1>HackZone</h1>
                            <p>Sistema de Gestión de Eventos</p>
                        </div>
                    </div>
                    <div class="print-info">
                        <div class="info-row">
                            <span class="info-label">Periodo:</span>
                            <span class="info-value">{{ $datos['mes'] }}</span>
                        </div>
                        <div class="info-row">
                            <span class="info-label">Generado:</span>
                            <span class="info-value">{{ now()->format('d/m/Y H:i') }}</span>
                        </div>
                    </div>
                </div>

                <!-- Stats Section -->
                <div class="stats-section">
                    <div class="stats-grid">
                        <div class="stat-box stat-box-primary">
                            <div class="stat-header">
                                <div class="stat-icon stat-icon-primary">
                                    <i class="fas fa-user-plus"></i>
                                </div>
                                <div class="stat-number">{{ $datos['nuevosUsuarios'] }}</div>
                            </div>
                            <div class="stat-label">Nuevos Usuarios</div>
                            <div class="stat-desc">Registrados este mes</div>
                        </div>

                        <div class="stat-box stat-box-secondary">
                            <div class="stat-header">
                                <div class="stat-icon stat-icon-secondary">
                                    <i class="fas fa-users"></i>
                                </div>
                                <div class="stat-number">{{ $datos['nuevosEquipos'] }}</div>
                            </div>
                            <div class="stat-label">Nuevos Equipos</div>
                            <div class="stat-desc">Equipos formados</div>
                        </div>

                        <div class="stat-box stat-box-accent">
                            <div class="stat-header">
                                <div class="stat-icon stat-icon-accent">
                                    <i class="fas fa-calendar-check"></i>
                                </div>
                                <div class="stat-number">{{ $datos['eventosRealizados'] }}</div>
                            </div>
                            <div class="stat-label">Eventos Realizados</div>
                            <div class="stat-desc">Eventos completados</div>
                        </div>
                    </div>
                </div>

                <!-- Summary Section -->
                <div class="content-section">
                    <div class="section-title-row">
                        <div class="section-icon-box">
                            <i class="fas fa-file-alt"></i>
                        </div>
                        <h3 class="section-title-text">Resumen Ejecutivo</h3>
                    </div>
                    <div class="section-body">
                        Durante el mes de <strong>{{ $datos['mes'] }}</strong>, la plataforma HackZone ha experimentado
                        un crecimiento significativo en su comunidad y actividades. Se registraron
                        <strong class="text-primary">{{ $datos['nuevosUsuarios'] }} nuevos usuarios</strong>,
                        se formaron <strong class="text-secondary">{{ $datos['nuevosEquipos'] }} equipos</strong>
                        y se llevaron a cabo <strong class="text-accent">{{ $datos['eventosRealizados'] }}
                            eventos</strong> exitosamente.
                    </div>
                </div>

                <!-- Insights Section -->
                <div class="content-section">
                    <div class="section-title-row">
                        <div class="section-icon-box">
                            <i class="fas fa-lightbulb"></i>
                        </div>
                        <h3 class="section-title-text">Indicadores Clave</h3>
                    </div>
                    <div class="insights-row">
                        <div class="insight-card">
                            <div class="insight-icon-box">
                                <i class="fas fa-chart-pie"></i>
                            </div>
                            <div class="insight-text">
                                <h4>Tasa de Participación</h4>
                                <p>Usuarios activos en eventos del periodo</p>
                            </div>
                        </div>
                        <div class="insight-card">
                            <div class="insight-icon-box">
                                <i class="fas fa-trophy"></i>
                            </div>
                            <div class="insight-text">
                                <h4>Engagement</h4>
                                <p>Equipos formados por evento realizado</p>
                            </div>
                        </div>
                        <div class="insight-card">
                            <div class="insight-icon-box">
                                <i class="fas fa-star"></i>
                            </div>
                            <div class="insight-text">
                                <h4>Crecimiento</h4>
                                <p>Incremento en la base de usuarios</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Footer -->
                <div class="report-footer">
                    <div class="footer-row">
                        <div class="footer-left">
                            <i class="fas fa-calendar-alt"></i>
                            <span>Generado el {{ now()->format('d/m/Y') }} a las {{ now()->format('H:i:s') }}</span>
                        </div>
                        <div class="footer-right">
                            <strong>HackZone</strong> - Sistema de Gestión de Eventos
                        </div>
                    </div>
                </div>

            </div>
        </main>
    </div>

    <script src="{{ asset('js/admin-dashboard.js') }}"></script>

</body>

</html>