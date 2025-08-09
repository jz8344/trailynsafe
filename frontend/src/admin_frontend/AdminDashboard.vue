<template>
  <div class="min-vh-100 bg-light">
    <!-- Header -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-white shadow-sm border-bottom">
      <div class="container-fluid px-4">
        <div class="d-flex align-items-center">
          <div class="d-inline-flex align-items-center justify-content-center rounded-3 me-3" 
               style="width: 40px; height: 40px; background: linear-gradient(135deg, #7c3aed, #3b82f6);">
            <i class="bi bi-speedometer2 text-white"></i>
          </div>
          <div>
            <h5 class="mb-0 text-dark fw-bold">Panel de Administración</h5>
            <small class="text-muted">TrailynSafe Admin Dashboard</small>
          </div>
        </div>
        
        <div class="d-flex align-items-center">
          <div class="text-end me-3">
            <div class="fw-medium text-dark">{{ adminData?.nombre }} {{ adminData?.apellidos }}</div>
            <small class="text-muted">Administrador</small>
          </div>
          <button 
            @click="logout"
            class="btn btn-danger d-flex align-items-center"
          >
            <i class="bi bi-box-arrow-right me-2"></i>
            Salir
          </button>
        </div>
      </div>
    </nav>

    <!-- Main Content -->
    <main class="container-fluid px-4 py-4">
      <!-- Welcome Section -->
      <div class="mb-4">
        <h2 class="fw-bold text-dark mb-2">¡Bienvenido al Panel!</h2>
        <p class="text-muted">Administra el sistema TrailynSafe desde aquí</p>
      </div>

      <!-- Stats Cards -->
      <div class="row g-4 mb-4">
        <div class="col-lg-3 col-md-6">
          <div class="card h-100 shadow-sm border-0">
            <div class="card-body">
              <div class="d-flex align-items-center">
                <div class="p-3 rounded-circle me-3" style="background-color: #dbeafe;">
                  <i class="bi bi-people-fill fs-4" style="color: #2563eb;"></i>
                </div>
                <div>
                  <h6 class="text-muted mb-1">Total Usuarios</h6>
                  <h4 class="fw-bold mb-0">{{ stats.totalUsuarios }}</h4>
                </div>
              </div>
            </div>
            <div class="border-start border-4 border-primary position-absolute top-0 bottom-0" style="left: 0; width: 4px;"></div>
          </div>
        </div>

        <div class="col-lg-3 col-md-6">
          <div class="card h-100 shadow-sm border-0">
            <div class="card-body">
              <div class="d-flex align-items-center">
                <div class="p-3 rounded-circle me-3" style="background-color: #dcfce7;">
                  <i class="bi bi-check-circle-fill fs-4" style="color: #16a34a;"></i>
                </div>
                <div>
                  <h6 class="text-muted mb-1">Usuarios Activos</h6>
                  <h4 class="fw-bold mb-0">{{ stats.usuariosActivos }}</h4>
                </div>
              </div>
            </div>
            <div class="border-start border-4 border-success position-absolute top-0 bottom-0" style="left: 0; width: 4px;"></div>
          </div>
        </div>

        <div class="col-lg-3 col-md-6">
          <div class="card h-100 shadow-sm border-0">
            <div class="card-body">
              <div class="d-flex align-items-center">
                <div class="p-3 rounded-circle me-3" style="background-color: #f3e8ff;">
                  <i class="bi bi-building fs-4" style="color: #7c3aed;"></i>
                </div>
                <div>
                  <h6 class="text-muted mb-1">Administradores</h6>
                  <h4 class="fw-bold mb-0">{{ stats.totalAdmins }}</h4>
                </div>
              </div>
            </div>
            <div class="border-start border-4 position-absolute top-0 bottom-0" style="left: 0; width: 4px; border-color: #7c3aed !important;"></div>
          </div>
        </div>

        <div class="col-lg-3 col-md-6">
          <div class="card h-100 shadow-sm border-0">
            <div class="card-body">
              <div class="d-flex align-items-center">
                <div class="p-3 rounded-circle me-3" style="background-color: #fef3c7;">
                  <i class="bi bi-server fs-4" style="color: #d97706;"></i>
                </div>
                <div>
                  <h6 class="text-muted mb-1">Sistema</h6>
                  <h4 class="fw-bold mb-0">Activo</h4>
                </div>
              </div>
            </div>
            <div class="border-start border-4 border-warning position-absolute top-0 bottom-0" style="left: 0; width: 4px;"></div>
          </div>
        </div>
      </div>

      <!-- Management Cards -->
      <div class="row g-4">
        <!-- Gestión de Usuarios -->
        <div class="col-lg-4 col-md-6">
          <div class="card h-100 shadow-sm border-0 overflow-hidden">
            <div class="card-header text-white border-0" style="background: linear-gradient(135deg, #3b82f6, #2563eb);">
              <div class="d-flex align-items-center">
                <i class="bi bi-people fs-4 me-3"></i>
                <h5 class="mb-0 fw-bold">Gestión de Usuarios</h5>
              </div>
            </div>
            <div class="card-body">
              <p class="text-muted mb-4">Administra todos los usuarios del sistema, revisa perfiles y gestiona permisos.</p>
              <button 
                @click="gestionarUsuarios"
                class="btn btn-outline-primary w-100 d-flex align-items-center justify-content-center"
              >
                <i class="bi bi-people me-2"></i>
                Ver Usuarios
              </button>
            </div>
          </div>
        </div>

        <!-- Base de Datos y Respaldos -->
        <div class="col-lg-4 col-md-6">
          <div class="card h-100 shadow-sm border-0 overflow-hidden">
            <div class="card-header text-white border-0" style="background: linear-gradient(135deg, #16a34a, #15803d);">
              <div class="d-flex align-items-center">
                <i class="bi bi-database fs-4 me-3"></i>
                <h5 class="mb-0 fw-bold">Base de Datos</h5>
              </div>
            </div>
            <div class="card-body">
              <p class="text-muted mb-4">Gestiona respaldos de la base de datos y monitorea el estado del sistema.</p>
              <div class="d-grid gap-2">
                <button 
                  @click="crearRespaldo"
                  class="btn btn-outline-success d-flex align-items-center justify-content-center"
                >
                  <i class="bi bi-download me-2"></i>
                  Crear Respaldo
                </button>
                <button 
                  @click="verRespaldos"
                  class="btn btn-outline-success d-flex align-items-center justify-content-center"
                >
                  <i class="bi bi-list-ul me-2"></i>
                  Ver Respaldos
                </button>
              </div>
            </div>
          </div>
        </div>

        <!-- Mi Perfil de Admin -->
        <div class="col-lg-4 col-md-6">
          <div class="card h-100 shadow-sm border-0 overflow-hidden">
            <div class="card-header text-white border-0" style="background: linear-gradient(135deg, #7c3aed, #6d28d9);">
              <div class="d-flex align-items-center">
                <i class="bi bi-person-circle fs-4 me-3"></i>
                <h5 class="mb-0 fw-bold">Mi Perfil</h5>
              </div>
            </div>
            <div class="card-body">
              <p class="text-muted mb-4">Gestiona tu información personal y configuración de cuenta administrativa.</p>
              <div class="d-grid gap-2">
                <router-link 
                  to="/admin/perfil"
                  class="btn btn-outline-primary d-flex align-items-center justify-content-center text-decoration-none"
                >
                  <i class="bi bi-pencil me-2"></i>
                  Editar Perfil
                </router-link>
                <button 
                  @click="cambiarContrasena"
                  class="btn btn-outline-primary d-flex align-items-center justify-content-center"
                >
                  <i class="bi bi-lock me-2"></i>
                  Cambiar Contraseña
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </main>
  </div>
</template>

<script>
import axios from 'axios'
import { admin, logoutAdmin } from '@/store/session.js'

export default {
  name: 'AdminDashboard',
  data() {
    return {
      stats: {
        totalUsuarios: 0,
        usuariosActivos: 0,
        totalAdmins: 0
      }
    }
  },
  computed: {
    adminData() {
      return admin.value
    }
  },
  async mounted() {
    await this.cargarEstadisticas()
  },
  methods: {
    async cargarEstadisticas() {
      try {
        const token = localStorage.getItem('admin_token')
        const response = await axios.get('http://127.0.0.1:8000/api/usuarios', {
          headers: { Authorization: `Bearer ${token}` }
        })
        
        const usuarios = response.data
        this.stats.totalUsuarios = usuarios.filter(u => u.rol === 'usuario').length
        this.stats.totalAdmins = usuarios.filter(u => u.rol === 'admin').length
        this.stats.usuariosActivos = usuarios.length
      } catch (error) {
        console.error('Error al cargar estadísticas:', error)
      }
    },
    gestionarUsuarios() {
      // Implementar modal o vista de gestión de usuarios
      alert('Función de gestión de usuarios en desarrollo')
    },
    crearRespaldo() {
      // Implementar función de respaldo
      alert('Función de respaldo en desarrollo')
    },
    verRespaldos() {
      // Implementar vista de respaldos
      alert('Función de ver respaldos en desarrollo')
    },
    cambiarContrasena() {
      // Implementar cambio de contraseña
      alert('Función de cambio de contraseña en desarrollo')
    },
    logout() {
      logoutAdmin()
      localStorage.removeItem('admin_token')
      this.$router.push('/admin/login')
    }
  }
}
</script>
