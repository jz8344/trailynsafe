<template>
  <div>
    <MenuNav />

    <section class="banner">
      <div class="banner-content">
        <h1>Monitoreo en Tiempo Real</h1>
        <p>Visualiza el recorrido del autobús escolar, junto con notificaciones de eventos importantes.</p>
      </div>
    </section>

    <section class="map-section">
      <div id="map" class="map-container"></div>
    </section>

    <section class="notifications-section">
      <h2 class="section-title">Notificaciones e Incidentes</h2>
      <table class="notifications-table">
        <thead>
          <tr>
            <th>Hora</th>
            <th>Evento</th>
            <th>Prioridad</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="n in notifications" :key="n.time">
            <td>{{ n.time }}</td>
            <td>{{ n.message }}</td>
            <td :class="n.priority">{{ n.priority }}</td>
          </tr>
        </tbody>
      </table>
    </section>

    <FooterSection />
  </div>
</template>

<script setup>
import { onMounted, ref } from 'vue';
import L from 'leaflet';
import 'leaflet/dist/leaflet.css';
import MenuNav from '@/components/MenuNav.vue';
import FooterSection from '@/components/FooterSection.vue';

const map = ref(null);
const busMarker = ref(null);
const route = [
  [20.5938, -100.3899], // Inicio
  [20.5940, -100.3890],
  [20.5950, -100.3880], // Se acerca a casa
  [20.5965, -100.3875], // Junior subió
  [20.5978, -100.3860], // Aceleración
  [20.5990, -100.3850], // Freno brusco
  [20.6000, -100.3840], // Cerca de escuela
  [20.6010, -100.3830]  // Llegada a escuela
];

const notifications = ref([]);

const steps = [
  'Se inició el viaje',
  'El autobús se acerca a tu casa',
  'Junior subió al camión',
  'El transporte va a la mitad de su trayecto',
  '¡Aceleración detectada!',
  'Freno brusco detectado',
  'El camión se está acercando a la escuela',
  'Junior bajó del camión. Llegada a la escuela.'
];

function addNotification(message, priority = 'media') {
  const now = new Date().toLocaleTimeString();
  notifications.value.unshift({ time: now, message, priority });

  if (priority === 'alta') {
    alert('⚠️ Notificación crítica: ' + message);
  }
}

onMounted(() => {

  map.value = L.map('map').setView(route[0], 16);
  L.tileLayer(
    'https://api.mapbox.com/styles/v1/mapbox/streets-v11/tiles/{z}/{x}/{y}?access_token=sk.eyJ1Ijoic3RlcGFuMSIsImEiOiJjbWRoaTR1c3kwMXhyMmtwcHp6eG56YjhjIn0.3ge5OKPKVnhlRAAtmOUeLA',
    {
      attribution: 'Map data &copy; <a href="https://www.openstreetmap.org/">OpenStreetMap</a> contributors, Imagery © <a href="https://www.mapbox.com/">Mapbox</a>',
      tileSize: 512,
      zoomOffset: -1,
      accessToken: 'sk.eyJ1Ijoic3RlcGFuMSIsImEiOiJjbWRoaTR1c3kwMXhyMmtwcHp6eG56YjhjIn0.3ge5OKPKVnhlRAAtmOUeLA'
    }
  ).addTo(map.value);

  busMarker.value = L.marker(route[0], {
    icon: L.icon({
      iconUrl: 'https://cdn-icons-png.flaticon.com/512/3082/3082383.png',
      iconSize: [40, 40],
      iconAnchor: [20, 40]
    })
  }).addTo(map.value);

  simulateRoute();
});

function simulateRoute() {
  let step = 0;
  const interval = setInterval(() => {
    if (step >= route.length) {
      clearInterval(interval);
      return;
    }
    busMarker.value.setLatLng(route[step]);
    map.value.setView(route[step], 16);

    let priority = 'media';
    if (steps[step].includes('Aceleración') || steps[step].includes('Freno')) {
      priority = 'alta';
    }

    addNotification(steps[step], priority);
    step++;
  }, 3000);
}
</script>

<style scoped>
.banner {
  background: linear-gradient(90deg, #4facfe, #00f2fe);
  color: white;
  padding: 40px 20px;
  text-align: center;
}
.map-section {
  height: 50vh;
}
.map-container {
  height: 100%;
  width: 100%;
}
.notifications-section {
  padding: 40px 20px;
}
.section-title {
  font-size: 1.8rem;
  color: #1976d2;
  margin-bottom: 20px;
}
.notifications-table {
  width: 100%;
  border-collapse: collapse;
  background: #fff;
  box-shadow: 0 4px 10px rgba(0, 0, 0, 0.05);
}
.notifications-table th,
.notifications-table td {
  padding: 12px 15px;
  border-bottom: 1px solid #eee;
  text-align: left;
}
.notifications-table .alta {
  color: #d32f2f;
  font-weight: bold;
}
.notifications-table .media {
  color: #1976d2;
}
</style>
