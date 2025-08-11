<template>
  <div class="container py-4">
    <h2 class="mb-4">Estadísticas del Sistema</h2>
    
    <!-- Resumen de tarjetas -->
    <div class="row mb-4">
      <div class="col-md-3 mb-3">
        <div class="card bg-primary text-white">
          <div class="card-body">
            <div class="d-flex justify-content-between">
              <div>
                <h4 class="card-title mb-0">{{ totales.usuarios }}</h4>
                <p class="card-text">Usuarios</p>
              </div>
              <div class="align-self-center">
                <i class="fas fa-users fa-2x"></i>
              </div>
            </div>
          </div>
        </div>
      </div>
      
      <div class="col-md-3 mb-3">
        <div class="card bg-success text-white">
          <div class="card-body">
            <div class="d-flex justify-content-between">
              <div>
                <h4 class="card-title mb-0">{{ totales.hijos }}</h4>
                <p class="card-text">Hijos</p>
              </div>
              <div class="align-self-center">
                <i class="fas fa-child fa-2x"></i>
              </div>
            </div>
          </div>
        </div>
      </div>
      
      <div class="col-md-3 mb-3">
        <div class="card bg-warning text-white">
          <div class="card-body">
            <div class="d-flex justify-content-between">
              <div>
                <h4 class="card-title mb-0">{{ totales.choferes }}</h4>
                <p class="card-text">Choferes</p>
              </div>
              <div class="align-self-center">
                <i class="fas fa-id-card fa-2x"></i>
              </div>
            </div>
          </div>
        </div>
      </div>
      
      <div class="col-md-3 mb-3">
        <div class="card bg-info text-white">
          <div class="card-body">
            <div class="d-flex justify-content-between">
              <div>
                <h4 class="card-title mb-0">{{ totales.unidades }}</h4>
                <p class="card-text">Unidades</p>
              </div>
              <div class="align-self-center">
                <i class="fas fa-bus fa-2x"></i>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Gráficos y tablas -->
    <div class="row">
      <div class="col-md-6 mb-4">
        <div class="card">
          <div class="card-header">
            <h5>Rutas por Estado</h5>
          </div>
          <div class="card-body">
            <table class="table table-sm">
              <thead>
                <tr>
                  <th>Estado</th>
                  <th>Cantidad</th>
                  <th>Porcentaje</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="estado in estadosRutas" :key="estado.nombre">
                  <td>{{ estado.nombre }}</td>
                  <td>{{ estado.cantidad }}</td>
                  <td>{{ estado.porcentaje }}%</td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>
      
      <div class="col-md-6 mb-4">
        <div class="card">
          <div class="card-header">
            <h5>Actividad Reciente</h5>
          </div>
          <div class="card-body">
            <div class="list-group list-group-flush">
              <div class="list-group-item d-flex justify-content-between align-items-center">
                <span>Nuevos usuarios registrados hoy</span>
                <span class="badge bg-primary rounded-pill">{{ actividadHoy.usuarios }}</span>
              </div>
              <div class="list-group-item d-flex justify-content-between align-items-center">
                <span>Rutas activas</span>
                <span class="badge bg-success rounded-pill">{{ actividadHoy.rutasActivas }}</span>
              </div>
              <div class="list-group-item d-flex justify-content-between align-items-center">
                <span>Unidades en servicio</span>
                <span class="badge bg-info rounded-pill">{{ actividadHoy.unidadesActivas }}</span>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Botón para refrescar datos -->
    <div class="text-end">
      <button class="btn btn-outline-primary" @click="cargarDatos" :disabled="cargando">
        <span v-if="cargando" class="spinner-border spinner-border-sm me-2"></span>
        {{ cargando ? 'Cargando...' : 'Refrescar Datos' }}
      </button>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive, onMounted } from 'vue';
import axios from 'axios';

const cargando = ref(false);
const totales = reactive({
  usuarios: 0,
  hijos: 0,
  choferes: 0,
  unidades: 0,
  rutas: 0
});

const estadosRutas = ref([]);
const actividadHoy = reactive({
  usuarios: 0,
  rutasActivas: 0,
  unidadesActivas: 0
});

function headers() {
  return { Authorization: `Bearer ${localStorage.getItem('admin_token')}` };
}

async function cargarDatos() {
  cargando.value = true;
  try {
    // Cargar datos de todas las entidades
    const [usuariosRes, hijosRes, choferesRes, unidadesRes, rutasRes] = await Promise.all([
      axios.get('http://127.0.0.1:8000/api/admin/usuarios', { headers: headers() }),
      axios.get('http://127.0.0.1:8000/api/admin/hijos', { headers: headers() }),
      axios.get('http://127.0.0.1:8000/api/admin/choferes', { headers: headers() }),
      axios.get('http://127.0.0.1:8000/api/admin/unidades', { headers: headers() }),
      axios.get('http://127.0.0.1:8000/api/admin/rutas', { headers: headers() })
    ]);

    // Actualizar totales
    totales.usuarios = usuariosRes.data.length;
    totales.hijos = hijosRes.data.length;
    totales.choferes = choferesRes.data.length;
    totales.unidades = unidadesRes.data.length;
    totales.rutas = rutasRes.data.length;

    // Calcular estadísticas de rutas por estado
    const rutas = rutasRes.data;
    const estadosCount = {};
    rutas.forEach(ruta => {
      estadosCount[ruta.estado] = (estadosCount[ruta.estado] || 0) + 1;
    });

    estadosRutas.value = Object.entries(estadosCount).map(([nombre, cantidad]) => ({
      nombre: nombre.charAt(0).toUpperCase() + nombre.slice(1),
      cantidad,
      porcentaje: Math.round((cantidad / rutas.length) * 100)
    }));

    // Actividad de hoy (simulada por ahora)
    actividadHoy.usuarios = Math.floor(Math.random() * 5);
    actividadHoy.rutasActivas = rutas.filter(r => r.estado === 'activa').length;
    actividadHoy.unidadesActivas = Math.floor(totales.unidades * 0.7);

  } catch (error) {
    console.error('Error al cargar estadísticas:', error);
  } finally {
    cargando.value = false;
  }
}

onMounted(cargarDatos);
</script>

<style scoped>
.card {
  border: none;
  box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
}

.card-header {
  background-color: #f8f9fa;
  border-bottom: 1px solid #dee2e6;
  font-weight: 600;
}
</style>
