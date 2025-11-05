/**
 * API Cliente para Usuario
 * Maneja todas las peticiones fetch a la API del usuario
 */

class UsuarioAPI {
    static async obtenerArchivos() {
        try {
            const response = await fetch('/api/usuario/archivos');
            const data = await response.json();
            return data;
        } catch (error) {
            console.error('Error al obtener archivos:', error);
            return { success: false, message: 'Error de conexión' };
        }
    }

    static async eliminarArchivo(id) {
        try {
            const response = await fetch('/api/usuario/eliminar-archivo', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ id })
            });
            const data = await response.json();
            return data;
        } catch (error) {
            console.error('Error al eliminar archivo:', error);
            return { success: false, message: 'Error de conexión' };
        }
    }

    static async obtenerAlmacenamiento() {
        try {
            const response = await fetch('/api/usuario/almacenamiento');
            const data = await response.json();
            return data;
        } catch (error) {
            console.error('Error al obtener almacenamiento:', error);
            return { success: false, message: 'Error de conexión' };
        }
    }
}

/**
 * Utilidades para renderizar datos en el DOM
 */
class UsuarioRenderer {
    static renderArchivos(archivos) {
        const container = document.querySelector('#archivos-container');
        const noArchivos = document.querySelector('#no-archivos');

        if (!archivos || archivos.length === 0) {
            container.style.display = 'none';
            noArchivos.style.display = 'flex';
            return;
        }

        container.style.display = 'grid';
        noArchivos.style.display = 'none';

        const html = archivos.map(archivo => {
            const extension = archivo.archivo.split('.').pop().toLowerCase();
            const icono = this.obtenerIconoArchivo(extension);
            
            // Mostrar fecha solo si existe created_at, sino mostrar ID
            let fechaHtml = '';
            if (archivo.created_at) {
                const fecha = new Date(archivo.created_at).toLocaleDateString('es-ES', {
                    year: 'numeric',
                    month: 'short',
                    day: 'numeric',
                    hour: '2-digit',
                    minute: '2-digit'
                });
                fechaHtml = `
                    <span class="archivo-fecha">
                        <i class="far fa-clock"></i> ${fecha}
                    </span>
                `;
            }

            return `
                <div class="archivo-card" data-id="${archivo.id}">
                    <div class="archivo-icon">
                        <i class="${icono}"></i>
                    </div>
                    <div class="archivo-info">
                        <h4 class="archivo-nombre" title="${archivo.archivo}">${archivo.archivo}</h4>
                        <div class="archivo-meta">
                            <span class="archivo-peso">
                                <i class="fas fa-weight-hanging"></i> ${archivo.peso} MB
                            </span>
                            ${fechaHtml}
                        </div>
                    </div>
                    <div class="archivo-actions">
                        <button class="btn-eliminar" data-id="${archivo.id}" title="Eliminar archivo">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </div>
            `;
        }).join('');

        container.innerHTML = html;

        // Agregar event listeners a los botones de eliminar
        this.agregarEventListeners();
    }

    static obtenerIconoArchivo(extension) {
        const iconos = {
            // Documentos
            'pdf': 'fas fa-file-pdf text-red',
            'doc': 'fas fa-file-word text-blue',
            'docx': 'fas fa-file-word text-blue',
            'xls': 'fas fa-file-excel text-green',
            'xlsx': 'fas fa-file-excel text-green',
            'ppt': 'fas fa-file-powerpoint text-orange',
            'pptx': 'fas fa-file-powerpoint text-orange',
            'txt': 'fas fa-file-alt',
            
            // Imágenes
            'jpg': 'fas fa-file-image text-purple',
            'jpeg': 'fas fa-file-image text-purple',
            'png': 'fas fa-file-image text-purple',
            'gif': 'fas fa-file-image text-purple',
            'bmp': 'fas fa-file-image text-purple',
            'svg': 'fas fa-file-image text-purple',
            
            // Videos
            'mp4': 'fas fa-file-video text-pink',
            'avi': 'fas fa-file-video text-pink',
            'mov': 'fas fa-file-video text-pink',
            'wmv': 'fas fa-file-video text-pink',
            
            // Audio
            'mp3': 'fas fa-file-audio text-teal',
            'wav': 'fas fa-file-audio text-teal',
            'ogg': 'fas fa-file-audio text-teal',
            
            // Comprimidos
            'zip': 'fas fa-file-archive text-yellow',
            'rar': 'fas fa-file-archive text-yellow',
            '7z': 'fas fa-file-archive text-yellow',
            
            // Código
            'html': 'fas fa-file-code text-orange',
            'css': 'fas fa-file-code text-blue',
            'js': 'fas fa-file-code text-yellow',
            'json': 'fas fa-file-code text-green',
            'xml': 'fas fa-file-code text-orange'
        };

        return iconos[extension] || 'fas fa-file';
    }

    static agregarEventListeners() {
        const botonesEliminar = document.querySelectorAll('.btn-eliminar');
        
        botonesEliminar.forEach(boton => {
            boton.addEventListener('click', async (e) => {
                e.preventDefault();
                const id = boton.dataset.id;
                
                if (confirm('¿Estás seguro de que deseas eliminar este archivo?')) {
                    await this.eliminarArchivo(id);
                }
            });
        });
    }

    static async eliminarArchivo(id) {
        const card = document.querySelector(`.archivo-card[data-id="${id}"]`);
        card.classList.add('eliminando');

        const resultado = await UsuarioAPI.eliminarArchivo(id);

        if (resultado.success) {
            card.style.animation = 'fadeOut 0.3s ease';
            setTimeout(() => {
                card.remove();
                // Verificar si quedan archivos
                const archivosRestantes = document.querySelectorAll('.archivo-card');
                if (archivosRestantes.length === 0) {
                    document.querySelector('#archivos-container').style.display = 'none';
                    document.querySelector('#no-archivos').style.display = 'flex';
                }
            }, 300);

            // Actualizar información de almacenamiento
            this.actualizarAlmacenamiento();
            
            this.mostrarNotificacion('Archivo eliminado correctamente', 'success');
        } else {
            card.classList.remove('eliminando');
            this.mostrarNotificacion(resultado.message || 'Error al eliminar archivo', 'error');
        }
    }

    static async actualizarAlmacenamiento() {
        const resultado = await UsuarioAPI.obtenerAlmacenamiento();
        
        if (resultado.success) {
            const { total, usado, disponible, porcentaje } = resultado.data;
            
            // Actualizar valores en las tarjetas
            const cards = document.querySelectorAll('.storage-card');
            if (cards[1]) {
                cards[1].querySelector('.storage-value').textContent = `${usado} MB`;
                cards[1].querySelector('small').textContent = `${porcentaje}% utilizado`;
            }
            if (cards[2]) {
                cards[2].querySelector('.storage-value').textContent = `${disponible} MB`;
            }
            
            // Actualizar barra de progreso
            const progressFill = document.querySelector('.progress-fill');
            const progressPercentage = document.querySelector('.progress-header span:last-child');
            
            if (progressFill) {
                progressFill.style.width = `${Math.min(porcentaje, 100)}%`;
            }
            if (progressPercentage) {
                progressPercentage.textContent = `${porcentaje}%`;
            }
        }
    }

    static mostrarNotificacion(mensaje, tipo = 'info') {
        const notificacion = document.createElement('div');
        notificacion.className = `notificacion notificacion-${tipo}`;
        notificacion.innerHTML = `
            <i class="fas fa-${tipo === 'success' ? 'check-circle' : tipo === 'error' ? 'exclamation-circle' : 'info-circle'}"></i>
            <span>${mensaje}</span>
        `;
        
        document.body.appendChild(notificacion);
        
        setTimeout(() => {
            notificacion.classList.add('show');
        }, 10);
        
        setTimeout(() => {
            notificacion.classList.remove('show');
            setTimeout(() => notificacion.remove(), 300);
        }, 3000);
    }
}

/**
 * Inicialización cuando el DOM está listo
 */
document.addEventListener('DOMContentLoaded', async () => {
    // Solo cargar archivos si estamos en la página del dashboard
    const archivosContainer = document.querySelector('#archivos-container');
    
    if (archivosContainer) {
        // Cargar archivos del usuario
        const resultado = await UsuarioAPI.obtenerArchivos();
        
        console.log('Resultado de API archivos:', resultado);
        
        if (resultado.success) {
            UsuarioRenderer.renderArchivos(resultado.data.archivos);
        } else {
            console.error('Error al cargar archivos:', resultado.message);
            UsuarioRenderer.mostrarNotificacion('Error al cargar archivos', 'error');
        }
    }
});
