// ===========================
// ADMIN API y RENDERIZACIÓN
// ===========================

class AdminAPI {
    constructor() {
        this.baseURL = '/api/admin';
    }

    async obtenerDashboard() {
        try {
            const response = await fetch(`${this.baseURL}/dashboard`);
            const result = await response.json();
            
            // Depurar los datos recibidos
            console.log('Datos recibidos:', result);
            console.log('Estadísticas:', result.data.estadisticas);
            
            if (!result.success) {
                throw new Error(result.message || 'Error al obtener datos del dashboard');
            }

            // Devolver la estructura de datos correcta
            return {
                estadisticas: result.data.estadisticas,
                usuarios: result.data.usuarios,
                grupos: result.data.grupos
            };
        } catch (error) {
            console.error('Error en API Dashboard:', error);
            throw error;
        }
    }
}

class AdminRenderer {
    constructor() {
        this.api = new AdminAPI();
    }

    async cargarDashboard() {
        try {
            // Mostrar loading en las estadísticas
            this.mostrarLoading();
            
            const data = await this.api.obtenerDashboard();
            
            // Actualizar estadísticas
            this.actualizarEstadisticas(data.estadisticas);
            
            // Renderizar tablas
            this.renderizarGrupos(data.grupos);
            this.renderizarUsuarios(data.usuarios);
            
        } catch (error) {
            console.error('Error al cargar dashboard:', error);
            this.mostrarError('Error al cargar los datos del dashboard');
        }
    }

    mostrarLoading() {
        // Actualizar contadores con loading
        const statsCards = document.querySelectorAll('.admin-stat-card h3');
        statsCards.forEach(card => {
            card.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
        });
    }

    actualizarEstadisticas(estadisticas) {
        const stats = [
            { selector: '.admin-stat-card:nth-child(1) h3', value: estadisticas.total_grupos },
            { selector: '.admin-stat-card:nth-child(2) h3', value: estadisticas.total_usuarios }
        ];

        stats.forEach(stat => {
            const element = document.querySelector(stat.selector);
            if (element) {
                element.textContent = stat.value;
            }
        });
    }

    

    renderizarGrupos(grupos) {
        // Buscar la tabla de grupos por el título de la sección
        const seccionGrupos = Array.from(document.querySelectorAll('.dashboard-section')).find(section => {
            const titulo = section.querySelector('h2');
            return titulo && titulo.textContent.includes('Grupos');
        });
        
        if (!seccionGrupos) {
            console.error('No se encontró la sección de grupos');
            return;
        }
        
        const tbody = seccionGrupos.querySelector('tbody');
        if (!tbody) return;

        if (grupos.length === 0) {
            this.mostrarEstadoVacio(tbody.closest('.table-container'), 'grupos');
            return;
        }

        tbody.innerHTML = grupos.map(grupo => `
            <tr>
                <td>${grupo.id}</td>
                <td>${this.escapeHtml(grupo.nombre)}</td>
                <td>
                    <span class="admin-name">
                        <i class="fas fa-user-shield"></i> 
                        ${this.escapeHtml(grupo.admin_nombre)}
                    </span>
                </td>
                <td>${grupo.peso} MB</td>
                <td>${grupo.total_usuarios} usuarios</td>
                <td class="acciones">
                    <a href="/admin/grupos/editar?id=${grupo.id}" class="btn btn-warning btn-sm">
                        Editar
                    </a>
                    <form method="POST" action="/admin/grupos/eliminar" style="display: inline;">
                        <input type="hidden" name="id" value="${grupo.id}">
                        <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('¿Estás seguro de eliminar este grupo?')">
                            Eliminar
                        </button>
                    </form>
                </td>
            </tr>
        `).join('');
    }

    renderizarUsuarios(usuarios) {
        // Buscar la tabla de usuarios por el título de la sección
        const seccionUsuarios = Array.from(document.querySelectorAll('.dashboard-section')).find(section => {
            const titulo = section.querySelector('h2');
            return titulo && titulo.textContent.includes('Usuarios');
        });
        
        if (!seccionUsuarios) {
            console.error('No se encontró la sección de usuarios');
            return;
        }
        
        const tbody = seccionUsuarios.querySelector('tbody');
        if (!tbody) return;

        if (usuarios.length === 0) {
            this.mostrarEstadoVacio(tbody.closest('.table-container'), 'usuarios');
            return;
        }

        tbody.innerHTML = usuarios.map(usuario => `
            <tr>
                <td>${usuario.id}</td>
                <td>${this.escapeHtml(usuario.nombre)}</td>
                <td>${this.formatearRol(usuario.rol || 'basico')}</td>
                <td>${this.escapeHtml(usuario.email)}</td>
                <td>
                    <span class="estado ${usuario.estado || 'activo'}">${this.formatearEstado(usuario.estado || 'activo')}</span>
                </td>
                <td>${usuario.grupo_nombre || 'Sin grupo'}</td>
                <td class="acciones">
                    <a href="/admin/usuarios/editar?id=${usuario.id}" class="btn btn-warning btn-sm">
                        Editar
                    </a>
                    <form method="POST" action="/admin/usuarios/eliminar" style="display: inline;">
                        <input type="hidden" name="id" value="${usuario.id}">
                        <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('¿Estás seguro de eliminar este usuario?')">
                            Eliminar
                        </button>
                    </form>
                </td>
            </tr>
        `).join('');
    }

    generarBotonEstado(item, tipo) {
        const estadoActual = item.estado || 'activa';
        const nuevoEstado = estadoActual === 'activa' ? 'inactiva' : 'activa';
        const btnClass = estadoActual === 'activa' ? 'btn-secondary' : 'btn-success';
        const texto = estadoActual === 'activa' ? 'Desactivar' : 'Activar';

        return `
            <form method="POST" action="/admin/${tipo}/estado" style="display: inline;">
                <input type="hidden" name="id" value="${item.id}">
                <input type="hidden" name="estado" value="${nuevoEstado}">
                <button type="submit" class="btn ${btnClass} btn-sm">
                    ${texto}
                </button>
            </form>
        `;
    }

    mostrarEstadoVacio(container, tipo) {
        const mensajes = {
            grupos: { icono: 'fa-box', texto: 'No hay grupos registrados', url: '/admin/grupos/crear', btnTexto: 'Crear primer grupo' },
            usuarios: { icono: 'fa-users', texto: 'No hay usuarios registrados', url: '/admin/usuarios/crear', btnTexto: 'Crear primer usuario' }
        };

        const config = mensajes[tipo];
        
        container.innerHTML = `
            <div class="empty-state">
                <i class="fas ${config.icono}"></i>
                <p>${config.texto}</p>
                <a href="${config.url}" class="btn btn-primary">${config.btnTexto}</a>
            </div>
        `;
    }

    mostrarError(mensaje) {
        const container = document.querySelector('.admin-dashboard');
        if (container) {
            const errorDiv = document.createElement('div');
            errorDiv.className = 'alert alert-warning';
            errorDiv.innerHTML = `
                <i class="fas fa-exclamation-triangle"></i>
                <strong>Error:</strong> ${mensaje}
            `;
            container.insertBefore(errorDiv, container.firstChild);
        }
    }

    // Métodos de utilidad
    escapeHtml(text) {
        if (!text) return '';
        const map = {
            '&': '&amp;',
            '<': '&lt;',
            '>': '&gt;',
            '"': '&quot;',
            "'": '&#039;'
        };
        return text.replace(/[&<>"']/g, m => map[m]);
    }

    formatearFecha(fecha) {
        if (!fecha) return 'N/A';
        try {
            const date = new Date(fecha);
            return date.toLocaleDateString('es-ES');
        } catch (error) {
            return 'N/A';
        }
    }

    formatearEstado(estado) {
        const estados = {
            'activo': 'Activo',
            'inactivo': 'Inactivo',
            'activa': 'Activa',
            'inactiva': 'Inactiva'
        };
        return estados[estado] || estado;
    }

    formatearRol(rol) {
        const roles = {
            'administrador': 'Administrador',
            'basico': 'Básico'
        };
        return roles[rol] || rol;
    }
}

// Inicialización cuando el DOM esté listo
document.addEventListener('DOMContentLoaded', () => {
    // Solo ejecutar en la página del admin
    if (document.querySelector('.admin-dashboard')) {
        const adminRenderer = new AdminRenderer();
        adminRenderer.cargarDashboard();
    }
});