<script setup>
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import AdminLayout from './layouts/AdminLayout.vue'

/**
 * AdminDashboard.vue
 * - Dashboard administrativo usando Bootstrap 5
 * - Navbar unificado reutilizable en todas las apps del admin
 * - Grid de tarjetas: Base de datos, Usuarios, Choferes, Rutas, Unidades, Estadísticas
 */

const router = useRouter()

// Estado reactivo
const loading = ref(false)
const searchQuery = ref('')

// Función para manejar cambios de ruta
router.afterEach(() => {
  loading.value = false
})

const cards = [
  {
    key: 'bd',
    title: 'Base de datos',
    description: 'Gestión y consulta de datos',
    icon: 'bi-database-fill',
    color: 'primary',
    img: 'https://raw.githubusercontent.com/odoo/odoo/18.0/addons/sale/static/description/icon.png',
  },
  {
    key: 'usuarios',
    title: 'Usuarios',
    description: 'Administrar usuarios del sistema',
    icon: 'bi-people-fill',
    color: 'success',
    img: '/img/USUARIOS.png',
    badge: 'Nuevo',
  },
  {
    key: 'hijos',
    title: 'Hijos',
    description: 'Registro de estudiantes',
    icon: 'bi-person-heart',
    color: 'info',
    img: '/src/assets/icons/hijos.png',
  },
  {
    key: 'choferes',
    title: 'Choferes',
    description: 'Gestión de conductores',
    icon: 'bi-person-vcard',
    color: 'warning',
    img: '/src/assets/icons/choferes.png',
  },
  {
    key: 'rutas',
    title: 'Rutas',
    description: 'Planificación de recorridos',
    icon: 'bi-geo-alt-fill',
    color: 'danger',
    img: '/src/assets/icons/rutas.png',
  },
  {
    key: 'unidades',
    title: 'Unidades',
    description: 'Control de vehículos',
    icon: 'bi-bus-front-fill',
    color: 'secondary',
    img: '/src/assets/icons/unidades.png',
  },
  {
    key: 'estadisticas',
    title: 'Estadísticas',
    description: 'Reportes y métricas',
    icon: 'bi-bar-chart-fill',
    color: 'dark',
    img: '/src/assets/icons/estadisticas.png',
  },
]

// Mapeo de las claves a rutas
const routeMap = {
  'dashboard': '/admin/dashboard',
  'bd': '/admin/dashboard',
  'usuarios': '/admin/app/usuarios',
  'hijos': '/admin/app/hijos',
  'choferes': '/admin/app/choferes',
  'rutas': '/admin/app/rutas',
  'unidades': '/admin/app/unidades',
  'estadisticas': '/admin/estadisticas',
  'ajustes': '/admin/configuracion',
}

// Métodos
async function onCardClick(key) {
  console.log('Clicked card:', key)
  const route = routeMap[key]
  console.log('Route to navigate:', route)
  
  if (route) {
    loading.value = true
    console.log('Navigating to:', route)
    
    try {
      await router.push(route)
      console.log('Navigation successful to:', route)
    } catch (error) {
      console.error('Navigation error:', error)
    } finally {
      loading.value = false
    }
  } else {
    console.error('No route found for key:', key)
  }
}

function handleSearch(query) {
  searchQuery.value = query
  console.log('Buscar:', query)
  // Aquí implementarías la lógica de búsqueda
}

function handleNotifications() {
  console.log('Mostrar notificaciones')
  // Aquí implementarías la lógica de notificaciones
}

function handleHistory() {
  console.log('Mostrar historial')
  // Aquí implementarías la lógica del historial
}
</script>

<template>
  <AdminLayout 
    page-title="Dashboard Administrativo"
    page-description="Bienvenido al panel de control de TrailynSafe"
    :notification-count="3"
    :loading="loading"
    @search="handleSearch"
    @showNotifications="handleNotifications"
    @showHistory="handleHistory"
  >
    <!-- Cards grid -->
    <div class="row g-4">
      <div v-for="card in cards" :key="card.key" class="col-12 col-sm-6 col-lg-4 col-xl-3">
        <div 
          class="card h-100 shadow-sm card-hover" 
          @click="onCardClick(card.key)"
          style="cursor: pointer; transition: all 0.3s ease;"
        >
          <div class="card-body text-center position-relative">
            <!-- Badge -->
            <span 
              v-if="card.badge" 
              class="position-absolute top-0 start-100 translate-middle badge bg-success"
            >
              {{ card.badge }}
            </span>

            <!-- Icon or Image -->
            <div class="mb-3">
              <div 
                v-if="card.icon" 
                :class="`bg-${card.color} bg-gradient`"
                class="rounded-circle d-flex align-items-center justify-content-center mx-auto"
                style="width: 64px; height: 64px;"
              >
                <i :class="card.icon" class="text-white fs-2"></i>
              </div>
              <img 
                v-else-if="card.img" 
                :src="card.img" 
                :alt="card.title" 
                class="img-fluid rounded"
                style="width: 64px; height: 64px; object-fit: contain;"
              >
            </div>

            <!-- Content -->
            <h5 class="card-title mb-2">{{ card.title }}</h5>
            <p class="card-text text-muted small mb-0">{{ card.description }}</p>
          </div>
          
          <div class="card-footer bg-transparent border-0 pt-0">
            <button :class="`btn btn-outline-${card.color} btn-sm w-100`">
              <i class="bi bi-arrow-right me-1"></i>
              Acceder
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Quick stats -->
    <div class="row mt-5">
      <div class="col">
        <div class="card bg-light border-0 shadow-sm">
          <div class="card-body">
            <h5 class="card-title mb-4">
              <i class="bi bi-graph-up text-primary me-2"></i>
              Estadísticas rápidas
            </h5>
            <div class="row text-center">
              <div class="col-6 col-md-3 mb-3 mb-md-0">
                <div class="border-end border-md-end-0 border-bottom border-md-bottom-0 pb-3 pb-md-0 pe-md-3">
                  <div class="d-flex align-items-center justify-content-center mb-2">
                    <div class="bg-primary bg-gradient rounded-circle p-2 me-2">
                      <i class="bi bi-people text-white"></i>
                    </div>
                    <h4 class="text-primary mb-0">156</h4>
                  </div>
                  <small class="text-muted">Usuarios activos</small>
                </div>
              </div>
              <div class="col-6 col-md-3 mb-3 mb-md-0">
                <div class="border-end border-md-end-0 border-bottom border-md-bottom-0 pb-3 pb-md-0 pe-md-3">
                  <div class="d-flex align-items-center justify-content-center mb-2">
                    <div class="bg-success bg-gradient rounded-circle p-2 me-2">
                      <i class="bi bi-person-vcard text-white"></i>
                    </div>
                    <h4 class="text-success mb-0">23</h4>
                  </div>
                  <small class="text-muted">Choferes</small>
                </div>
              </div>
              <div class="col-6 col-md-3 mb-3 mb-md-0">
                <div class="border-end border-md-end-0 border-bottom border-md-bottom-0 pb-3 pb-md-0 pe-md-3">
                  <div class="d-flex align-items-center justify-content-center mb-2">
                    <div class="bg-warning bg-gradient rounded-circle p-2 me-2">
                      <i class="bi bi-geo-alt text-white"></i>
                    </div>
                    <h4 class="text-warning mb-0">12</h4>
                  </div>
                  <small class="text-muted">Rutas activas</small>
                </div>
              </div>
              <div class="col-6 col-md-3">
                <div class="d-flex align-items-center justify-content-center mb-2">
                  <div class="bg-info bg-gradient rounded-circle p-2 me-2">
                    <i class="bi bi-bus-front text-white"></i>
                  </div>
                  <h4 class="text-info mb-0">8</h4>
                </div>
                <small class="text-muted">Unidades</small>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Recent activity -->
    <div class="row mt-4">
      <div class="col-lg-8">
        <div class="card shadow-sm">
          <div class="card-header bg-transparent border-bottom-0">
            <h6 class="card-title mb-0">
              <i class="bi bi-clock-history text-primary me-2"></i>
              Actividad reciente
            </h6>
          </div>
          <div class="card-body">
            <div class="list-group list-group-flush">
              <div class="list-group-item border-0 px-0">
                <div class="d-flex align-items-center">
                  <div class="bg-success bg-gradient rounded-circle p-2 me-3">
                    <i class="bi bi-person-plus text-white small"></i>
                  </div>
                  <div class="flex-grow-1">
                    <p class="mb-1 small">Nuevo usuario registrado</p>
                    <small class="text-muted">hace 2 minutos</small>
                  </div>
                </div>
              </div>
              <div class="list-group-item border-0 px-0">
                <div class="d-flex align-items-center">
                  <div class="bg-primary bg-gradient rounded-circle p-2 me-3">
                    <i class="bi bi-geo-alt text-white small"></i>
                  </div>
                  <div class="flex-grow-1">
                    <p class="mb-1 small">Ruta actualizada: Centro - Norte</p>
                    <small class="text-muted">hace 15 minutos</small>
                  </div>
                </div>
              </div>
              <div class="list-group-item border-0 px-0">
                <div class="d-flex align-items-center">
                  <div class="bg-warning bg-gradient rounded-circle p-2 me-3">
                    <i class="bi bi-bus-front text-white small"></i>
                  </div>
                  <div class="flex-grow-1">
                    <p class="mb-1 small">Unidad 005 en mantenimiento</p>
                    <small class="text-muted">hace 1 hora</small>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="col-lg-4 mt-4 mt-lg-0">
        <div class="card shadow-sm">
          <div class="card-header bg-transparent border-bottom-0">
            <h6 class="card-title mb-0">
              <i class="bi bi-exclamation-triangle text-warning me-2"></i>
              Alertas del sistema
            </h6>
          </div>
          <div class="card-body">
            <div class="alert alert-warning alert-sm" role="alert">
              <small>
                <i class="bi bi-fuel-pump me-1"></i>
                Unidad 003 necesita combustible
              </small>
            </div>
            <div class="alert alert-info alert-sm" role="alert">
              <small>
                <i class="bi bi-calendar-event me-1"></i>
                Mantenimiento programado mañana
              </small>
            </div>
            <div class="alert alert-success alert-sm mb-0" role="alert">
              <small>
                <i class="bi bi-check-circle me-1"></i>
                Todos los sistemas operativos
              </small>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AdminLayout>
</template>

<style scoped>
/* Hover effects para tarjetas */
.card-hover:hover {
  transform: translateY(-4px);
  box-shadow: 0 8px 25px rgba(0,0,0,0.15) !important;
}

.card-hover:hover .btn {
  background-color: var(--bs-primary);
  color: white !important;
  border-color: var(--bs-primary);
}

/* Dark mode specific hover effects */
[data-bs-theme="dark"] .card-hover:hover .btn {
  background-color: var(--bs-primary);
  color: white !important;
  border-color: var(--bs-primary);
}

/* Alertas más pequeñas */
.alert-sm {
  padding: 0.375rem 0.75rem;
  font-size: 0.875rem;
}

/* Cards con gradiente sutil - Modo claro */
.card {
  background: linear-gradient(145deg, #ffffff, #f8f9fa);
  border: 1px solid rgba(0,0,0,0.1);
  color: var(--bs-body-color);
}

/* Cards en modo oscuro */
[data-bs-theme="dark"] .card {
  background: linear-gradient(145deg, #2d3748, #1a202c);
  border: 1px solid rgba(255,255,255,0.1);
  color: var(--bs-body-color);
}

[data-bs-theme="dark"] .card-body {
  color: var(--bs-body-color);
}

[data-bs-theme="dark"] .card-title {
  color: var(--bs-body-color);
}

[data-bs-theme="dark"] .text-muted {
  color: rgba(255,255,255,0.6) !important;
}

/* Botones en modo oscuro */
[data-bs-theme="dark"] .btn-outline-primary {
  color: var(--bs-primary);
  border-color: var(--bs-primary);
  background-color: transparent;
}

[data-bs-theme="dark"] .btn-outline-success {
  color: var(--bs-success);
  border-color: var(--bs-success);
}

[data-bs-theme="dark"] .btn-outline-warning {
  color: var(--bs-warning);
  border-color: var(--bs-warning);
}

[data-bs-theme="dark"] .btn-outline-danger {
  color: var(--bs-danger);
  border-color: var(--bs-danger);
}

[data-bs-theme="dark"] .btn-outline-info {
  color: var(--bs-info);
  border-color: var(--bs-info);
}

[data-bs-theme="dark"] .btn-outline-secondary {
  color: var(--bs-secondary);
  border-color: var(--bs-secondary);
}

/* Card footer en modo oscuro */
[data-bs-theme="dark"] .card-footer {
  background-color: transparent;
  border-color: rgba(255,255,255,0.1);
}

/* Background para modo oscuro */
[data-bs-theme="dark"] .bg-light {
  background-color: var(--bs-dark) !important;
  color: var(--bs-light);
}

/* Animaciones suaves */
.btn, .card {
  transition: all 0.3s ease;
}
</style>
