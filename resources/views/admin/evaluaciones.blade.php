<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Evaluaciones - HackZone</title>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/admin-dashboard.css') }}">
    <link rel="stylesheet" href="{{ asset('css/admin-tablas.css') }}">
</head>
<body>

    @include('components.navbar-admin')

    <div class="admin-container">
        @include('components.sidebar-admin')

        <main class="admin-main">
            <h2 class="titulo-pagina">Gestión de Evaluaciones</h2>

            {{-- Filtros --}}
            <div style="background: white; border-radius: 12px; padding: 1.5rem; margin-bottom: 2rem; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem;">
                    <div>
                        <label style="display: block; margin-bottom: 0.5rem; font-weight: 500; color: #4B5563;">Evento</label>
                        <select style="width: 100%; padding: 0.75rem; border: 1px solid #D1D5DB; border-radius: 8px;">
                            <option>Todos los eventos</option>
                            <option>HackZone 2025</option>
                            <option>CodeFest 2024</option>
                        </select>
                    </div>
                    <div>
                        <label style="display: block; margin-bottom: 0.5rem; font-weight: 500; color: #4B5563;">Estado</label>
                        <select style="width: 100%; padding: 0.75rem; border: 1px solid #D1D5DB; border-radius: 8px;">
                            <option>Todos</option>
                            <option>Completadas</option>
                            <option>Pendientes</option>
                            <option>En proceso</option>
                        </select>
                    </div>
                    <div>
                        <label style="display: block; margin-bottom: 0.5rem; font-weight: 500; color: #4B5563;">Juez</label>
                        <select style="width: 100%; padding: 0.75rem; border: 1px solid #D1D5DB; border-radius: 8px;">
                            <option>Todos los jueces</option>
                            <option>Dr. García</option>
                            <option>Ing. Martínez</option>
                        </select>
                    </div>
                    <div style="display: flex; align-items: flex-end;">
                        <button style="width: 100%; padding: 0.75rem; background: linear-gradient(135deg, #6366F1, #8B5CF6); color: white; border: none; border-radius: 8px; cursor: pointer; font-weight: 500;">
                            <i class="fas fa-filter"></i> Aplicar Filtros
                        </button>
                    </div>
                </div>
            </div>

            {{-- Estadísticas --}}
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1.5rem; margin-bottom: 2rem;">
                <div style="background: linear-gradient(135deg, #10B981, #059669); padding: 1.5rem; border-radius: 12px; color: white;">
                    <div style="display: flex; justify-content: space-between; align-items: center;">
                        <div>
                            <div style="font-size: 0.875rem; opacity: 0.9;">Completadas</div>
                            <div style="font-size: 2rem; font-weight: bold; margin-top: 0.5rem;">24</div>
                        </div>
                        <i class="fas fa-check-circle" style="font-size: 3rem; opacity: 0.3;"></i>
                    </div>
                </div>
                <div style="background: linear-gradient(135deg, #F59E0B, #D97706); padding: 1.5rem; border-radius: 12px; color: white;">
                    <div style="display: flex; justify-content: space-between; align-items: center;">
                        <div>
                            <div style="font-size: 0.875rem; opacity: 0.9;">En Proceso</div>
                            <div style="font-size: 2rem; font-weight: bold; margin-top: 0.5rem;">8</div>
                        </div>
                        <i class="fas fa-clock" style="font-size: 3rem; opacity: 0.3;"></i>
                    </div>
                </div>
                <div style="background: linear-gradient(135deg, #EF4444, #DC2626); padding: 1.5rem; border-radius: 12px; color: white;">
                    <div style="display: flex; justify-content: space-between; align-items: center;">
                        <div>
                            <div style="font-size: 0.875rem; opacity: 0.9;">Pendientes</div>
                            <div style="font-size: 2rem; font-weight: bold; margin-top: 0.5rem;">12</div>
                        </div>
                        <i class="fas fa-exclamation-circle" style="font-size: 3rem; opacity: 0.3;"></i>
                    </div>
                </div>
                <div style="background: linear-gradient(135deg, #6366F1, #8B5CF6); padding: 1.5rem; border-radius: 12px; color: white;">
                    <div style="display: flex; justify-content: space-between; align-items: center;">
                        <div>
                            <div style="font-size: 0.875rem; opacity: 0.9;">Promedio Total</div>
                            <div style="font-size: 2rem; font-weight: bold; margin-top: 0.5rem;">8.5</div>
                        </div>
                        <i class="fas fa-chart-line" style="font-size: 3rem; opacity: 0.3;"></i>
                    </div>
                </div>
            </div>

            {{-- Tabla de evaluaciones --}}
            <div style="background: white; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); overflow: hidden;">
                <table style="width: 100%; border-collapse: collapse;">
                    <thead>
                        <tr style="background: linear-gradient(135deg, #6366F1, #8B5CF6); color: white;">
                            <th style="padding: 1rem; text-align: left;">Equipo</th>
                            <th style="padding: 1rem; text-align: left;">Evento</th>
                            <th style="padding: 1rem; text-align: left;">Juez</th>
                            <th style="padding: 1rem; text-align: center;">Calificación</th>
                            <th style="padding: 1rem; text-align: center;">Estado</th>
                            <th style="padding: 1rem; text-align: center;">Fecha</th>
                            <th style="padding: 1rem; text-align: center;">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        {{-- Ejemplo de evaluación completada --}}
                        <tr style="border-bottom: 1px solid #E5E7EB;">
                            <td style="padding: 1rem;">
                                <div style="font-weight: 600; color: #1F2937;">Team Alpha</div>
                                <div style="font-size: 0.875rem; color: #6B7280;">Líder: Juan Pérez</div>
                            </td>
                            <td style="padding: 1rem;">
                                <div style="color: #4B5563;">HackZone 2025</div>
                            </td>
                            <td style="padding: 1rem;">
                                <div style="color: #4B5563;">Dr. García López</div>
                                <div style="font-size: 0.875rem; color: #6B7280;">garcia@email.com</div>
                            </td>
                            <td style="padding: 1rem; text-align: center;">
                                <div style="display: inline-block; background: linear-gradient(135deg, #10B981, #059669); color: white; padding: 0.5rem 1rem; border-radius: 8px; font-weight: bold; font-size: 1.25rem;">
                                    9.2
                                </div>
                            </td>
                            <td style="padding: 1rem; text-align: center;">
                                <span style="background: #D1FAE5; color: #065F46; padding: 0.5rem 1rem; border-radius: 20px; font-size: 0.875rem; font-weight: 500;">
                                    <i class="fas fa-check-circle"></i> Completada
                                </span>
                            </td>
                            <td style="padding: 1rem; text-align: center; color: #6B7280;">
                                06/12/2025
                            </td>
                            <td style="padding: 1rem; text-align: center;">
                                <button style="background: #6366F1; color: white; border: none; padding: 0.5rem 1rem; border-radius: 6px; cursor: pointer; margin-right: 0.5rem;">
                                    <i class="fas fa-eye"></i> Ver Detalles
                                </button>
                            </td>
                        </tr>

                        {{-- Ejemplo de evaluación en proceso --}}
                        <tr style="border-bottom: 1px solid #E5E7EB;">
                            <td style="padding: 1rem;">
                                <div style="font-weight: 600; color: #1F2937;">Code Warriors</div>
                                <div style="font-size: 0.875rem; color: #6B7280;">Líder: María González</div>
                            </td>
                            <td style="padding: 1rem;">
                                <div style="color: #4B5563;">HackZone 2025</div>
                            </td>
                            <td style="padding: 1rem;">
                                <div style="color: #4B5563;">Ing. Martínez Ruiz</div>
                                <div style="font-size: 0.875rem; color: #6B7280;">martinez@email.com</div>
                            </td>
                            <td style="padding: 1rem; text-align: center;">
                                <div style="color: #6B7280; font-style: italic;">
                                    En evaluación
                                </div>
                            </td>
                            <td style="padding: 1rem; text-align: center;">
                                <span style="background: #FEF3C7; color: #92400E; padding: 0.5rem 1rem; border-radius: 20px; font-size: 0.875rem; font-weight: 500;">
                                    <i class="fas fa-clock"></i> En Proceso
                                </span>
                            </td>
                            <td style="padding: 1rem; text-align: center; color: #6B7280;">
                                07/12/2025
                            </td>
                            <td style="padding: 1rem; text-align: center;">
                                <button style="background: #F59E0B; color: white; border: none; padding: 0.5rem 1rem; border-radius: 6px; cursor: pointer;">
                                    <i class="fas fa-hourglass-half"></i> Seguimiento
                                </button>
                            </td>
                        </tr>

                        {{-- Ejemplo de evaluación pendiente --}}
                        <tr style="border-bottom: 1px solid #E5E7EB;">
                            <td style="padding: 1rem;">
                                <div style="font-weight: 600; color: #1F2937;">Dev Masters</div>
                                <div style="font-size: 0.875rem; color: #6B7280;">Líder: Carlos Ramírez</div>
                            </td>
                            <td style="padding: 1rem;">
                                <div style="color: #4B5563;">CodeFest 2024</div>
                            </td>
                            <td style="padding: 1rem;">
                                <div style="color: #4B5563;">Dra. Fernández Silva</div>
                                <div style="font-size: 0.875rem; color: #6B7280;">fernandez@email.com</div>
                            </td>
                            <td style="padding: 1rem; text-align: center;">
                                <div style="color: #6B7280; font-style: italic;">
                                    Sin calificar
                                </div>
                            </td>
                            <td style="padding: 1rem; text-align: center;">
                                <span style="background: #FEE2E2; color: #991B1B; padding: 0.5rem 1rem; border-radius: 20px; font-size: 0.875rem; font-weight: 500;">
                                    <i class="fas fa-exclamation-circle"></i> Pendiente
                                </span>
                            </td>
                            <td style="padding: 1rem; text-align: center; color: #6B7280;">
                                -
                            </td>
                            <td style="padding: 1rem; text-align: center;">
                                <button style="background: #6B7280; color: white; border: none; padding: 0.5rem 1rem; border-radius: 6px; cursor: pointer;">
                                    <i class="fas fa-bell"></i> Recordar
                                </button>
                            </td>
                        </tr>

                        {{-- Más ejemplos... --}}
                        <tr style="border-bottom: 1px solid #E5E7EB;">
                            <td style="padding: 1rem;">
                                <div style="font-weight: 600; color: #1F2937;">Tech Innovators</div>
                                <div style="font-size: 0.875rem; color: #6B7280;">Líder: Ana Torres</div>
                            </td>
                            <td style="padding: 1rem;">
                                <div style="color: #4B5563;">HackZone 2025</div>
                            </td>
                            <td style="padding: 1rem;">
                                <div style="color: #4B5563;">Dr. García López</div>
                                <div style="font-size: 0.875rem; color: #6B7280;">garcia@email.com</div>
                            </td>
                            <td style="padding: 1rem; text-align: center;">
                                <div style="display: inline-block; background: linear-gradient(135deg, #10B981, #059669); color: white; padding: 0.5rem 1rem; border-radius: 8px; font-weight: bold; font-size: 1.25rem;">
                                    8.7
                                </div>
                            </td>
                            <td style="padding: 1rem; text-align: center;">
                                <span style="background: #D1FAE5; color: #065F46; padding: 0.5rem 1rem; border-radius: 20px; font-size: 0.875rem; font-weight: 500;">
                                    <i class="fas fa-check-circle"></i> Completada
                                </span>
                            </td>
                            <td style="padding: 1rem; text-align: center; color: #6B7280;">
                                05/12/2025
                            </td>
                            <td style="padding: 1rem; text-align: center;">
                                <button style="background: #6366F1; color: white; border: none; padding: 0.5rem 1rem; border-radius: 6px; cursor: pointer;">
                                    <i class="fas fa-eye"></i> Ver Detalles
                                </button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            {{-- Paginación --}}
            <div style="display: flex; justify-content: center; align-items: center; gap: 0.5rem; margin-top: 2rem;">
                <button style="padding: 0.5rem 1rem; border: 1px solid #D1D5DB; background: white; border-radius: 6px; cursor: pointer;">
                    <i class="fas fa-chevron-left"></i>
                </button>
                <button style="padding: 0.5rem 1rem; border: 1px solid #6366F1; background: #6366F1; color: white; border-radius: 6px; font-weight: 500;">1</button>
                <button style="padding: 0.5rem 1rem; border: 1px solid #D1D5DB; background: white; border-radius: 6px; cursor: pointer;">2</button>
                <button style="padding: 0.5rem 1rem; border: 1px solid #D1D5DB; background: white; border-radius: 6px; cursor: pointer;">3</button>
                <button style="padding: 0.5rem 1rem; border: 1px solid #D1D5DB; background: white; border-radius: 6px; cursor: pointer;">
                    <i class="fas fa-chevron-right"></i>
                </button>
            </div>
        </main>
    </div>

</body>
</html>
