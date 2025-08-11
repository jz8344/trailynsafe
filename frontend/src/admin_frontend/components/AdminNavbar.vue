<template>
  <nav class="navbar navbar-expand-lg navbar-dark bg-primary shadow-sm fixed-top">
    <div class="container-fluid">
      <!-- Sidebar toggle -->
      <button 
        @click="toggleSidebar" 
        class="btn btn-outline-light me-3 d-lg-none" 
        type="button"
        data-bs-toggle="tooltip" 
        data-bs-placement="bottom" 
        title="Menú (Ctrl+Alt+A)"
      >
        <i class="bi bi-list"></i>
      </button>

      <!-- Brand -->
      <router-link to="/admin/dashboard" class="navbar-brand fw-bold text-decoration-none">
        <i class="bi bi-shield-check me-2"></i>
        TrailynSafe Admin
      </router-link>

      <!-- Breadcrumb -->
      <nav aria-label="breadcrumb" class="d-none d-md-block">
        <ol class="breadcrumb mb-0 bg-transparent">
          <li class="breadcrumb-item">
            <router-link to="/admin/dashboard" class="text-light text-decoration-none">
              Dashboard
            </router-link>
          </li>
          <li class="breadcrumb-item active text-light" aria-current="page">
            {{ currentPageTitle }}
          </li>
        </ol>
      </nav>

      <!-- Right side actions -->
      <div class="navbar-nav ms-auto d-flex flex-row align-items-center">
        <!-- Search -->
        <div class="nav-item me-3 d-none d-md-block">
          <div class="input-group input-group-sm">
            <input 
              type="text" 
              class="form-control form-control-sm" 
              placeholder="Buscar..." 
              id="globalSearch"
              style="width: 200px;"
              v-model="searchQuery"
              @keyup.enter="performSearch"
            >
            <button class="btn btn-outline-light" type="button" @click="performSearch">
              <i class="bi bi-search"></i>
            </button>
          </div>
        </div>

        <!-- Notifications -->
        <div class="nav-item me-3">
          <button 
            class="btn btn-outline-light position-relative" 
            data-bs-toggle="tooltip" 
            data-bs-placement="bottom" 
            title="Notificaciones"
            @click="showNotifications"
          >
            <i class="bi bi-bell"></i>
            <span 
              v-if="notificationCount > 0"
              class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger"
            >
              {{ notificationCount }}
              <span class="visually-hidden">notificaciones sin leer</span>
            </span>
          </button>
        </div>

        <!-- Dark mode toggle -->
        <div class="nav-item me-3">
          <button 
            @click="toggleDark" 
            class="btn btn-outline-light" 
            :title="darkMode ? 'Modo claro' : 'Modo oscuro'"
            data-bs-toggle="tooltip" 
            data-bs-placement="bottom"
          >
            <i :class="darkMode ? 'bi bi-sun' : 'bi bi-moon'"></i>
          </button>
        </div>

        <!-- User menu -->
        <div class="nav-item dropdown">
          <button 
            class="btn btn-outline-light dropdown-toggle d-flex align-items-center" 
            data-bs-toggle="dropdown" 
            aria-expanded="false"
            id="userDropdown"
          >
            <div class="bg-white text-primary rounded-circle d-flex align-items-center justify-content-center me-2" 
                 style="width: 32px; height: 32px; font-weight: bold; font-size: 14px;">
              {{ userInitials }}
            </div>
            <span class="d-none d-md-inline">{{ userName }}</span>
          </button>
          <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
            <li><h6 class="dropdown-header">Sesión</h6></li>
            <li>
              <router-link to="/admin/perfil" class="dropdown-item">
                <i class="bi bi-person me-2"></i>Perfil
              </router-link>
            </li>
            <li>
              <router-link to="/admin/configuracion" class="dropdown-item">
                <i class="bi bi-gear me-2"></i>Configuración
              </router-link>
            </li>
            <li>
              <a href="#" @click.prevent="showHistory" class="dropdown-item">
                <i class="bi bi-clock-history me-2"></i>Historial
              </a>
            </li>
            <li><hr class="dropdown-divider"></li>
            <li>
              <button @click="logout" class="dropdown-item text-danger">
                <i class="bi bi-box-arrow-right me-2"></i>Salir
              </button>
            </li>
          </ul>
        </div>
      </div>
    </div>
  </nav>

  <!-- Sidebar para móvil -->
  <div 
    v-if="sidebarOpen" 
    class="offcanvas offcanvas-start show d-lg-none" 
    tabindex="-1" 
    @click.self="toggleSidebar"
  >
    <div class="offcanvas-header bg-primary text-white">
      <h5 class="offcanvas-title">
        <i class="bi bi-grid-3x3-gap me-2"></i>
        Aplicaciones
      </h5>
      <button 
        @click="toggleSidebar" 
        type="button" 
        class="btn-close btn-close-white" 
        aria-label="Cerrar"
      ></button>
    </div>
    <div class="offcanvas-body">
      <div class="row g-3">
        <div v-for="app in apps" :key="app.key" class="col-4">
          <button 
            @click="gotoApp(app.key)" 
            class="btn btn-outline-primary d-flex flex-column align-items-center p-3 w-100 h-100"
          >
            <i :class="app.icon" class="fs-3 mb-2"></i>
            <small class="text-center">{{ app.name }}</small>
          </button>
        </div>
      </div>
    </div>
  </div>

  <!-- Sidebar aplicaciones para desktop -->
  <div 
    v-if="sidebarOpen" 
    class="sidebar-overlay d-none d-lg-block"
    @click="toggleSidebar"
  ></div>
  
  <div 
    v-if="sidebarOpen" 
    class="sidebar-apps d-none d-lg-block"
  >
    <div class="p-4">
      <div class="d-flex justify-content-between align-items-center mb-4">
        <h5 class="mb-0">
          <i class="bi bi-grid-3x3-gap me-2"></i>
          Aplicaciones
        </h5>
        <button @click="toggleSidebar" class="btn btn-sm btn-outline-secondary">
          <i class="bi bi-x-lg"></i>
        </button>
      </div>
      
      <div class="row g-3">
        <div v-for="app in apps" :key="app.key" class="col-4">
          <button 
            @click="gotoApp(app.key)" 
            class="btn btn-outline-primary d-flex flex-column align-items-center p-3 w-100"
            style="min-height: 100px;"
          >
            <i :class="app.icon" class="fs-2 mb-2"></i>
            <small class="text-center lh-1">{{ app.name }}</small>
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue'
import { useRouter, useRoute } from 'vue-router'

// Props
const props = defineProps({
  pageTitle: {
    type: String,
    default: 'Dashboard'
  },
  userName: {
    type: String,
    default: 'Demo Company'
  },
  notificationCount: {
    type: Number,
    default: 3
  }
})

// Emits
const emit = defineEmits(['search', 'showNotifications', 'showHistory', 'logout'])

const router = useRouter()
const route = useRoute()

// Estado reactivo
const darkMode = ref(false)
const sidebarOpen = ref(false)
const searchQuery = ref('')

// Computed
const currentPageTitle = computed(() => props.pageTitle)
const userInitials = computed(() => {
  return props.userName.split(' ').map(word => word[0]).join('').substring(0, 2).toUpperCase()
})

// Apps disponibles
const apps = [
  { name: 'Dashboard', icon: 'bi-house-fill', key: 'dashboard' },
  { name: 'Base de datos', icon: 'bi-database-fill', key: 'bd' },
  { name: 'Usuarios', icon: 'bi-people-fill', key: 'usuarios' },
  { name: 'Hijos', icon: 'bi-person-heart', key: 'hijos' },
  { name: 'Choferes', icon: 'bi-person-vcard', key: 'choferes' },
  { name: 'Unidades', icon: 'bi-bus-front-fill', key: 'unidades' },
  { name: 'Rutas', icon: 'bi-geo-alt-fill', key: 'rutas' },
  { name: 'Estadísticas', icon: 'bi-bar-chart-fill', key: 'estadisticas' },
  { name: 'Configuración', icon: 'bi-gear-fill', key: 'ajustes' },
]

// Mapeo de rutas
const routeMap = {
  'dashboard': '/admin/dashboard',
  'bd': '/admin/dashboard',
  'usuarios': '/admin/usuarios',
  'hijos': '/admin/hijos',
  'choferes': '/admin/choferes',
  'rutas': '/admin/rutas',
  'unidades': '/admin/unidades',
  'estadisticas': '/admin/estadisticas',
  'ajustes': '/admin/configuracion',
}

// Métodos
function toggleDark() {
  darkMode.value = !darkMode.value
  localStorage.setItem('darkMode', String(darkMode.value))
  document.body.setAttribute('data-bs-theme', darkMode.value ? 'dark' : 'light')
}

function toggleSidebar() {
  sidebarOpen.value = !sidebarOpen.value
}

function gotoApp(key) {
  const route = routeMap[key]
  if (route) {
    router.push(route)
  }
  sidebarOpen.value = false
}

function performSearch() {
  if (searchQuery.value.trim()) {
    emit('search', searchQuery.value)
  }
}

function showNotifications() {
  emit('showNotifications')
}

function showHistory() {
  emit('showHistory')
}

function logout() {
  emit('logout')
}

// Lifecycle
onMounted(() => {
  const stored = localStorage.getItem('darkMode') === 'true'
  darkMode.value = stored
  document.body.setAttribute('data-bs-theme', stored ? 'dark' : 'light')

  // Bootstrap tooltips initialization (with error handling)
  try {
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
    tooltipTriggerList.map(function (tooltipTriggerEl) {
      if (typeof window !== 'undefined' && window.bootstrap && window.bootstrap.Tooltip) {
        return new window.bootstrap.Tooltip(tooltipTriggerEl)
      }
    })
  } catch (error) {
    console.warn('Bootstrap tooltips not available:', error)
  }

  // Keyboard shortcuts
  window.addEventListener('keydown', e => {
    if ((e.ctrlKey || e.metaKey) && e.key.toLowerCase() === 'k') {
      e.preventDefault()
      const el = document.getElementById('globalSearch')
      if (el) el.focus()
    }
    if (e.altKey && e.ctrlKey && e.key.toLowerCase() === 'a') {
      e.preventDefault()
      toggleSidebar()
    }
  })
})
</script>

<style scoped>
/* Sidebar overlay */
.sidebar-overlay {
  position: fixed;
  top: 0;
  left: 0;
  width: 100vw;
  height: 100vh;
  background: rgba(0, 0, 0, 0.5);
  z-index: 1040;
}

/* Sidebar aplicaciones */
.sidebar-apps {
  position: fixed;
  top: 76px;
  right: 20px;
  width: 400px;
  max-height: calc(100vh - 100px);
  background: white;
  border-radius: 12px;
  box-shadow: 0 10px 40px rgba(0,0,0,0.2);
  z-index: 1050;
  overflow-y: auto;
}

[data-bs-theme="dark"] .sidebar-apps {
  background: var(--bs-dark);
  color: var(--bs-light);
}

/* Navbar brand gradient */
.navbar-brand {
  background: linear-gradient(45deg, #fff, #e3f2fd);
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
  background-clip: text;
}

/* Responsive adjustments */
@media (max-width: 768px) {
  .sidebar-apps {
    top: 60px;
    right: 10px;
    left: 10px;
    width: auto;
  }
}
</style>
