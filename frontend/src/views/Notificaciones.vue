<template>
  <div>
    <MenuNav />
    <div class="notificaciones-vista">
      <h2>Notificaciones nuevas</h2>
      <ul v-if="notiNoLeidas.length">
        <li v-for="noti in notiNoLeidas" :key="noti.id" class="noti-list-item">
          <div>
            <b>Monto:</b> ${{ noti.monto }}<br>
            <b>Motivo:</b> {{ noti.motivo }}<br>
            <b>Estado:</b> {{ noti.estado }}<br>
            <b>Fecha:</b> {{ noti.fecha_emision }}
          </div>
        </li>
      </ul>
      <div v-else>
        <p>No tienes notificaciones nuevas.</p>
      </div>
      <button v-if="notiNoLeidas.length" @click="marcarTodasComoLeidas" class="btn btn-primary">Marcar todas como leídas</button>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, onUnmounted } from 'vue';
import axios from 'axios';
import { huesped } from '@/store/session.js';
import MenuNav from '@/components/MenuNav.vue';

const notiNoLeidas = ref([]);
let intervalId = null;

async function cargarNoLeidas() {
  const userId = huesped.value?.id || huesped.value?._id;
  if (!userId) {
    notiNoLeidas.value = [];
    return;
  }
  const res = await axios.get(
    `http://127.0.0.1:8000/api/multas/huesped/${userId}`,
    {
      headers: {
        Authorization: 'Bearer ' + localStorage.getItem('token')
      }
    }
  );
  notiNoLeidas.value = res.data
    .filter(m => m.vista === false)
    .sort((a, b) => new Date(b.fecha_notificacion) - new Date(a.fecha_notificacion));
}

async function marcarTodasComoLeidas() {
  try {
    const promises = notiNoLeidas.value.map(async (noti) => {
      if (!noti.id) {
        console.error('ID de notificación no definido:', noti);
        return;
      }
      try {
        const res = await axios.post(
          `http://127.0.0.1:8000/api/multas/${noti.id}/vista`,
          {},
          {
            headers: {
              Authorization: 'Bearer ' + localStorage.getItem('token')
            }
          }
        );
        console.log(`Notificación ${noti.id} marcada como leída`, res.data);
      } catch (error) {
        alert(`Error marcando notificación ${noti.id}: ${error.response?.data?.error || error.message}`);
        console.error(`Error marcando notificación ${noti.id} como leída:`, error.response?.data || error.message);
      }
    });
    await Promise.all(promises);
    await cargarNoLeidas();
  } catch (error) {
    alert('Error marcando todas las notificaciones como leídas.');
    console.error('Error marcando todas las notificaciones como leídas:', error);
  }
}
onMounted(() => {
  cargarNoLeidas();
  intervalId = setInterval(cargarNoLeidas, 8000);
});

onUnmounted(() => {
  if (intervalId) clearInterval(intervalId);
});
</script>

<style scoped>
body {
  margin: 0;
  font-family: 'Montserrat', sans-serif;
}
.navbar {
  background-color: #111;
  color: white;
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 5px 30px;
}
.navbar-logo {
  display: flex;
  align-items: center;
}
.navbar-logo img {
  height: 120px;
  margin-right: 10px;
}
.navbar-logo h1 {
  font-family: 'Great Vibes', cursive;
  font-size: 62px;
  margin: 0;
  color: white;
  line-height: 1;
  display: flex;
  align-items: center;
}
.navbar-links a {
  margin-left: 20px;
  color: white;
  text-decoration: none;
  font-weight: 500;
}
.navbar-logo .flex {
  display: flex;
  align-items: center;
}
.navbar-links a:hover {
  color: #d4af37;
}
.navbar-logo a {
  text-decoration: none;
  color: inherit;
}
.btn {
  padding: 12px 20px;
  font-size: 1rem;
  font-weight: 600;
  border: none;
  border-radius: 20px;
  cursor: pointer;
  transition: 0.3s;
  margin-left: 20px;
}
.btn-primary {
  background-color: #009FE3;
  color: white;
}
.btn-primary:hover {
  background-color: #007cb3;
}
.btn-outline {
  background-color: white;
  color: #009FE3;
  border: 2px solid #009FE3;
  margin-top: 10px;
}

.btn-outline:hover {
  background-color: #f0f0f0;
}

.modal-perfil {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: rgba(0, 0, 0, 0.8);
  display: flex;
  justify-content: center;
  align-items: center;
  z-index: 1000;
}
.modal-content {
  background: white;
  padding: 30px;
  border-radius: 12px;
  max-width: 500px;
  max-height: 80vh;
  width: 100%;
  text-align: center;
  overflow-y: auto;
}
.modal-content h2 {
  font-size: 1.5rem;
  margin-bottom: 20px;
  color: #111;
}
.modal-content p {
  font-size: 1rem;
  margin-bottom: 15px;
  color: #333;
}
.modal-content button {
  width: 100%;
  padding: 12px;
  font-size: 1rem;
  border: none;
  border-radius: 8px;
  cursor: pointer;
  transition: 0.3s;
}
.modal-content .btn-primary {
  background-color: #009FE3;
  color: white;
}
.modal-content .btn-primary:hover {
  background-color: #007cb3;
}
.historial-multas {
  margin-top: 20px;
  padding-top: 20px;
  border-top: 1px solid #eee;
}
.historial-multas h3 {
  color: #000000;
  margin-bottom: 15px;
}
.historial-multas ul {
  list-style: none;
  padding: 0;
  color: #000000;
  text-align: left;
}
.historial-multas li {
  padding: 8px;
  margin-bottom: 5px;
  background: #f8f9fa;
  border-radius: 4px;
  font-size: 0.9rem;
  color: #000000;
}
.notificaciones {
  position: fixed;
  top: 20px;
  right: 20px;
  z-index: 9999;
  max-width: 320px;
}
.noti-header {
  display: flex;
  flex-direction: column;
  align-items: flex-start;
  margin-bottom: 10px;
  gap: 4px;
}
.navbar-noti {
  position: relative;
  margin-left: 20px;
  display: flex;
  align-items: center;
}
.noti-bell {
  color: #0957ff;
  font-size: 32px;
  display: flex;
  align-items: center;
  position: relative;
}
.noti-bell i {
  font-size: 32px;
  margin-right: 4px;
}
.noti-count {
  position: absolute;
  top: -6px;
  right: -6px;
  background: #ecc80f;
  color: white;
  border-radius: 50%;
  font-size: 0.85em;
  padding: 2px 7px;
  font-weight: bold;
  min-width: 22px;
  text-align: center;
  box-shadow: 0 1px 4px rgba(0,0,0,0.15);
}
.noti-tipo {
  background: #ffe58f;
  color: #ad6800;
  font-size: 0.95em;
  padding: 2px 10px;
  border-radius: 6px;
  font-weight: 600;
  margin-top: 2px;
}
.noti-body {
  display: flex;
  flex-direction: column;
  gap: 6px;
}
.noti-row {
  display: flex;
  justify-content: space-between;
  align-items: center;
}
.noti-label {
  min-width: 70px;
  font-weight: 500;
}
.noti-value {
  font-weight: 600;
  color: #ad6800;
  margin-left: 10px;
  word-break: break-word;
}
.notificacion {
  background: #fffbe6;
  border: 1px solid #ffe58f;
  border-radius: 8px;
  padding: 16px 32px 16px 16px;
  margin-bottom: 12px;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
  color: #ad6800;
  font-size: 1rem;
  position: relative;
  animation: slideIn 0.3s ease-out;
}
@keyframes slideIn {
  from {
    transform: translateX(100%);
    opacity: 0;
  }
  to {
    transform: translateX(0);
    opacity: 1;
  }
}
.cerrar-noti {
  position: absolute;
  top: 8px;
  right: 8px;
  background: transparent;
  border: none;
  color: #ad6800;
  font-size: 1.2rem;
  font-weight: bold;
  cursor: pointer;
  transition: color 0.2s;
}
.cerrar-noti:hover {
  color: #d48806;
}
.notificaciones-vista {
  max-width: 500px;
  margin: 40px auto;
  background: #fffbe6;
  border: 1px solid #ffe58f;
  border-radius: 12px;
  padding: 32px 24px;
  color: #ad6800;
  box-shadow: 0 2px 8px rgba(0,0,0,0.08);
}
.noti-list-item {
  background: #fff;
  border-radius: 8px;
  margin-bottom: 16px;
  padding: 16px;
  border: 1px solid #ffe58f;
}
.btn {
  margin-top: 20px;
  background: #009FE3;
  color: white;
  border: none;
  border-radius: 8px;
  padding: 12px 24px;
  font-weight: bold;
  cursor: pointer;
}
.btn:hover {
  background: #007cb3;
}
</style>