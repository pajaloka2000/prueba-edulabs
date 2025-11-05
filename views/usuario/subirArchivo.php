<div class="usuario-dashboard">
    <div class="dashboard-header">
        <h1>
            <i class="fas fa-cloud-upload-alt"></i>
            Subir Archivos
        </h1>
        <p>Sube tus archivos al sistema</p>
    </div>

    <!-- Información de Almacenamiento -->
    <div class="storage-info-card">
        <div class="storage-header">
            <h3><i class="fas fa-hdd"></i> Almacenamiento Disponible</h3>
        </div>
        <div class="storage-details" id="storage-details">
            <div class="storage-item">
                <span class="label">Total:</span>
                <span class="value" id="storage-total">Cargando...</span>
            </div>
            <div class="storage-item">
                <span class="label">Usado:</span>
                <span class="value" id="storage-used">Cargando...</span>
            </div>
            <div class="storage-item">
                <span class="label">Disponible:</span>
                <span class="value available" id="storage-available">Cargando...</span>
            </div>
        </div>
        <div class="storage-progress">
            <div class="progress-bar">
                <div class="progress-fill" id="progress-fill" style="width: 0%"></div>
            </div>
            <span class="progress-text" id="progress-text">0%</span>
        </div>
    </div>

    <!-- Formulario de Subida -->
    <div class="upload-form-card">
        <h3><i class="fas fa-upload"></i> Seleccionar Archivo</h3>
        
        <div id="alert-container"></div>

        <form id="upload-form" enctype="multipart/form-data">
            <div class="upload-area" id="upload-area">
                <div class="upload-icon">
                    <i class="fas fa-cloud-upload-alt"></i>
                </div>
                <p class="upload-text">Arrastra y suelta tu archivo aquí</p>
                <p class="upload-subtext">o</p>
                <label for="file-input" class="btn btn-primary">
                    <i class="fas fa-folder-open"></i>
                    Seleccionar Archivo
                </label>
                <input type="file" id="file-input" name="archivo" accept="*/*" style="display: none;">
                <p class="upload-info" id="max-size-info">Tamaño máximo: <span id="max-file-size">10 MB</span></p>
            </div>

            <div class="selected-file" id="selected-file" style="display: none;">
                <div class="file-info">
                    <i class="fas fa-file"></i>
                    <div class="file-details">
                        <span class="file-name" id="file-name"></span>
                        <span class="file-size" id="file-size"></span>
                    </div>
                </div>
                <button type="button" class="btn-remove" id="remove-file">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-success" id="submit-btn" disabled>
                    <i class="fas fa-upload"></i>
                    Subir Archivo
                </button>
                <a href="/usuario" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i>
                    Volver al Dashboard
                </a>
            </div>
        </form>
    </div>

    <!-- Mis Archivos -->
    <div class="files-list-card">
        <div class="section-header">
            <h3><i class="fas fa-folder-open"></i> Mis Archivos</h3>
            <span class="badge" id="files-count">0 archivos</span>
        </div>
        <div class="files-container" id="files-container">
            <div class="loading-files">
                <i class="fas fa-spinner fa-spin"></i>
                <p>Cargando archivos...</p>
            </div>
        </div>
    </div>
</div>

<script>

let storageData = {
    total: 10,
    usado: 0,
    disponible: 10,
    porcentaje: 0
};

let selectedFile = null;
let extensionesProhibidas = [];

document.addEventListener('DOMContentLoaded', async function() {
    await loadExtensionesProhibidas();
    await loadStorageInfo();
    await loadUserFiles();
    initializeUploadHandlers();
});

/**
 * Cargar extensiones prohibidas
 */
async function loadExtensionesProhibidas() {
    try {
        const response = await fetch('/api/extensiones-prohibidas');
        const data = await response.json();
        
        if (data.success) {
            extensionesProhibidas = data.data.extensiones;
            console.log('Extensiones prohibidas cargadas:', extensionesProhibidas);
        }
    } catch (error) {
        console.error('Error loading extensiones prohibidas:', error);
    }
}

/**
 * Cargar información de almacenamiento del usuario
 */
async function loadStorageInfo() {
    try {
        const response = await fetch('/api/usuario/almacenamiento');
        const data = await response.json();
        
        if (data.success) {
            storageData = {
                total: parseFloat(data.data.total),
                usado: parseFloat(data.data.usado),
                disponible: parseFloat(data.data.disponible),
                porcentaje: parseFloat(data.data.porcentaje)
            };
            
            updateStorageDisplay();
        }
    } catch (error) {
        console.error('Error loading storage info:', error);
        showAlert('Error al cargar información de almacenamiento', 'error');
    }
}

/**
 * Actualizar visualización de almacenamiento
 */
function updateStorageDisplay() {
    document.getElementById('storage-total').textContent = `${storageData.total} MB`;
    document.getElementById('storage-used').textContent = `${storageData.usado} MB`;
    document.getElementById('storage-available').textContent = `${storageData.disponible} MB`;
    document.getElementById('max-file-size').textContent = `${storageData.disponible} MB`;
    document.getElementById('progress-fill').style.width = `${storageData.porcentaje}%`;
    document.getElementById('progress-text').textContent = `${storageData.porcentaje.toFixed(1)}%`;
    
    // Cambiar color según el porcentaje usado
    const progressFill = document.getElementById('progress-fill');
    if (storageData.porcentaje >= 90) {
        progressFill.style.backgroundColor = '#e74c3c';
    } else if (storageData.porcentaje >= 70) {
        progressFill.style.backgroundColor = '#f39c12';
    } else {
        progressFill.style.backgroundColor = '#27ae60';
    }
}

/**
 * Inicializar manejadores de eventos
 */
function initializeUploadHandlers() {
    const fileInput = document.getElementById('file-input');
    const uploadArea = document.getElementById('upload-area');
    const uploadForm = document.getElementById('upload-form');
    const removeBtn = document.getElementById('remove-file');
    
    // Click en el área de subida
    uploadArea.addEventListener('click', (e) => {
        if (e.target.tagName !== 'INPUT') {
            fileInput.click();
        }
    });
    
    // Cambio de archivo
    fileInput.addEventListener('change', handleFileSelect);
    
    // Drag and drop
    uploadArea.addEventListener('dragover', (e) => {
        e.preventDefault();
        uploadArea.classList.add('dragover');
    });
    
    uploadArea.addEventListener('dragleave', () => {
        uploadArea.classList.remove('dragover');
    });
    
    uploadArea.addEventListener('drop', (e) => {
        e.preventDefault();
        uploadArea.classList.remove('dragover');
        
        const files = e.dataTransfer.files;
        if (files.length > 0) {
            handleFile(files[0]);
        }
    });
    
    // Remover archivo
    removeBtn.addEventListener('click', clearFileSelection);
    
    // Envío del formulario
    uploadForm.addEventListener('submit', handleFormSubmit);
}

/**
 * Manejar selección de archivo
 */
function handleFileSelect(e) {
    const file = e.target.files[0];
    if (file) {
        handleFile(file);
    }
}

/**
 * Procesar archivo seleccionado
 */
function handleFile(file) {
    // Obtener extensión del archivo
    const fileName = file.name;
    const extension = fileName.split('.').pop().toLowerCase();
    
    // Validar extensión del archivo
    if (extensionesProhibidas.includes(extension)) {
        showAlert(
            `Error: El tipo de archivo '.${extension}' no está permitido por razones de seguridad`,
            'error'
        );
        return;
    }
    
    // Validar tamaño del archivo
    const fileSizeMB = file.size / (1024 * 1024);
    
    if (fileSizeMB > storageData.disponible) {
        showAlert(
            `El archivo (${fileSizeMB.toFixed(2)} MB) excede el espacio disponible (${storageData.disponible} MB)`,
            'error'
        );
        return;
    }
    
    if (fileSizeMB > storageData.total) {
        showAlert(
            `El archivo (${fileSizeMB.toFixed(2)} MB) excede el tamaño máximo permitido (${storageData.total} MB)`,
            'error'
        );
        return;
    }
    
    // Guardar archivo seleccionado
    selectedFile = file;
    
    // Mostrar información del archivo
    document.getElementById('file-name').textContent = file.name;
    document.getElementById('file-size').textContent = `${fileSizeMB.toFixed(2)} MB`;
    document.getElementById('selected-file').style.display = 'flex';
    document.getElementById('upload-area').style.display = 'none';
    document.getElementById('submit-btn').disabled = false;
}

/**
 * Limpiar selección de archivo
 */
function clearFileSelection() {
    selectedFile = null;
    document.getElementById('file-input').value = '';
    document.getElementById('selected-file').style.display = 'none';
    document.getElementById('upload-area').style.display = 'flex';
    document.getElementById('submit-btn').disabled = true;
}

/**
 * Manejar envío del formulario
 */
async function handleFormSubmit(e) {
    e.preventDefault();
    
    if (!selectedFile) {
        showAlert('Por favor selecciona un archivo', 'error');
        return;
    }
    
    const submitBtn = document.getElementById('submit-btn');
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Subiendo...';
    
    const formData = new FormData();
    formData.append('archivo', selectedFile);
    
    try {
        const response = await fetch('/api/usuario/subir-archivo', {
            method: 'POST',
            body: formData
        });
        
        const data = await response.json();
        
        if (data.success) {
            showAlert('Archivo subido correctamente', 'success');
            clearFileSelection();
            await loadStorageInfo();
            await loadUserFiles();
        } else {
            showAlert(data.message || 'Error al subir el archivo', 'error');
        }
    } catch (error) {
        console.error('Error uploading file:', error);
        showAlert('Error al subir el archivo', 'error');
    } finally {
        submitBtn.disabled = false;
        submitBtn.innerHTML = '<i class="fas fa-upload"></i> Subir Archivo';
    }
}

/**
 * Cargar archivos del usuario
 */
async function loadUserFiles() {
    try {
        const response = await fetch('/api/usuario/archivos');
        const data = await response.json();
        
        if (data.success) {
            renderUserFiles(data.data.archivos);
            document.getElementById('files-count').textContent = `${data.data.archivos.length} archivo(s)`;
        }
    } catch (error) {
        console.error('Error loading files:', error);
        document.getElementById('files-container').innerHTML = '<p class="no-files">Error al cargar archivos</p>';
    }
}

/**
 * Renderizar lista de archivos
 */
function renderUserFiles(files) {
    const container = document.getElementById('files-container');
    
    if (files.length === 0) {
        container.innerHTML = '<p class="no-files"><i class="fas fa-folder-open"></i> No tienes archivos subidos</p>';
        return;
    }
    
    container.innerHTML = files.map(file => `
        <div class="file-item">
            <div class="file-icon">
                <i class="fas fa-file"></i>
            </div>
            <div class="file-info-detail">
                <h4>${escapeHtml(file.archivo)}</h4>
                <div class="file-meta">
                    <span><i class="fas fa-weight"></i> ${file.peso} MB</span>
                    <span><i class="fas fa-clock"></i> ${formatDate(file.created_at)}</span>
                </div>
            </div>
            <div class="file-actions">
                <button onclick="deleteFile(${file.id})" class="btn-delete" title="Eliminar">
                    <i class="fas fa-trash"></i>
                </button>
            </div>
        </div>
    `).join('');
}

/**
 * Eliminar archivo
 */
async function deleteFile(fileId) {
    if (!confirm('¿Estás seguro de eliminar este archivo?')) {
        return;
    }
    
    try {
        const response = await fetch('/api/usuario/eliminar-archivo', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ id: fileId })
        });
        
        const data = await response.json();
        
        if (data.success) {
            showAlert('Archivo eliminado correctamente', 'success');
            await loadStorageInfo();
            await loadUserFiles();
        } else {
            showAlert(data.message || 'Error al eliminar el archivo', 'error');
        }
    } catch (error) {
        console.error('Error deleting file:', error);
        showAlert('Error al eliminar el archivo', 'error');
    }
}

/**
 * Mostrar alertas
 */
function showAlert(message, type) {
    const alertContainer = document.getElementById('alert-container');
    const alertClass = type === 'success' ? 'alert-success' : 'alert-error';
    const icon = type === 'success' ? 'fa-check-circle' : 'fa-exclamation-triangle';
    
    const alertDiv = document.createElement('div');
    alertDiv.className = `alert ${alertClass}`;
    alertDiv.innerHTML = `
        <i class="fas ${icon}"></i>
        <span>${message}</span>
        <button onclick="this.parentElement.remove()" class="alert-close">
            <i class="fas fa-times"></i>
        </button>
    `;
    
    alertContainer.appendChild(alertDiv);
    
    setTimeout(() => {
        alertDiv.remove();
    }, 5000);
}

/**
 * Utilidades
 */
function escapeHtml(text) {
    const map = {
        '&': '&amp;',
        '<': '&lt;',
        '>': '&gt;',
        '"': '&quot;',
        "'": '&#039;'
    };
    return text.replace(/[&<>"']/g, m => map[m]);
}

function formatDate(dateString) {
    const date = new Date(dateString);
    return date.toLocaleDateString('es-ES', {
        year: 'numeric',
        month: 'short',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    });
}
</script>
