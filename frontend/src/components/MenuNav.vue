<template>
  <!-- Alerta de conexión perdida -->
  <div v-if="conexionPerdida" class="alert alert-warning text-center conexion-perdida">
    <i class="bi bi-wifi-off"></i>
    No se pudo conectar con el servidor. Verifica tu conexión.
  </div>
  
  <nav class="navbar">
    <div class="navbar-logo">
      <router-link to="/" class="flex items-center no-underline">
        <img :src="logo" width="120" alt="Trailyn Safe Logo" class="mr-2">
        <h1 class="m-0">Trailyn Safe</h1>
      </router-link>
    </div>
    <div class="navbar-toggle" @click="toggleMobileMenu">
      <i class="bi bi-list" v-if="!showMobileMenu"></i>
      <i class="bi bi-x" v-if="showMobileMenu"></i>
    </div>
    <div class="navbar-links" :class="{ 'mobile-menu-open': showMobileMenu }">
      <router-link to="/" @click="closeMobileMenu">Inicio</router-link>
      <router-link to="/about" @click="closeMobileMenu">Sobre nosotros</router-link>
      <router-link to="/aliados" @click="closeMobileMenu">Aliados</router-link>
      <router-link to="/rutas" @click="closeMobileMenu">Rutas</router-link>
      <template v-if="!usuario || !usuario.id">
        <router-link to="/login" @click="closeMobileMenu">Iniciar sesión</router-link>
        <router-link to="/register" @click="closeMobileMenu">Registrarte</router-link>
      </template>
      <template v-else>
        <button @click="openProfile" class="btn btn-primary">Perfil</button>
      </template>
    </div>
    <div v-if="showProfile && usuario" class="modal-perfil">
      <div class="modal-content">
        <div class="modal-header">
          <h2>Mi perfil</h2>
          <button @click="closeProfile" class="close-btn">×</button>
        </div>
        <div class="profile-info">
          <div class="info-card">
            <h3>Información personal</h3>
            <div class="info-row">
              <span class="info-label">Nombre:</span>
              <span class="info-value">{{ usuario.nombre }} {{ usuario.apellidos }}</span>
            </div>
            <div class="info-row">
              <span class="info-label">Correo:</span>
              <span class="info-value">{{ usuario.correo }}</span>
            </div>
            <div class="info-row">
              <span class="info-label">Teléfono:</span>
              <span class="info-value">{{ usuario.telefono }}</span>
            </div>
            <div class="info-row">
              <span class="info-label">Fecha de registro:</span>
              <span class="info-value">{{ usuario.fecha_registro }}</span>
            </div>
            <router-link to="/editar-perfil" class="btn btn-primary">Editar perfil</router-link>
          </div>
        </div>
        <div class="modal-footer">
          <button @click="cerrarSesion" class="btn btn-danger">Cerrar sesión</button>
          <button @click="closeProfile" class="btn btn-outline">Cerrar</button>
        </div>
      </div>
    </div>
  </nav>
</template>

<script setup>
import logo from '/img/logo.png';
import { ref, onMounted, onUnmounted } from 'vue';
import axios from 'axios';
import { useRouter } from 'vue-router';
import { usuario, logoutUsuario } from '@/store/session.js';

const router = useRouter();
const showProfile = ref(false);
const showMobileMenu = ref(false);
const conexionPerdida = ref(false);
let sesionIntervalId = null;

function toggleMobileMenu() {
  showMobileMenu.value = !showMobileMenu.value;
}

function closeMobileMenu() {
  showMobileMenu.value = false;
}

function openProfile() {
  showProfile.value = true;
  closeMobileMenu();
  consultarPerfil();
}

function closeProfile() {
  showProfile.value = false;
}

function cerrarSesion() {
  const token = localStorage.getItem('token');
  if (token) {
    axios.post('http://127.0.0.1:8000/api/sesiones/cerrar-actual', {}, {
      headers: { Authorization: 'Bearer ' + token }
    }).finally(() => {
      logoutUsuario();
      localStorage.clear();
      sessionStorage.clear();
      router.push('/login');
    });
  } else {
    logoutUsuario();
    localStorage.clear();
    sessionStorage.clear();
    router.push('/login');
  }
}

// Consulta el perfil actualizado desde la API
async function consultarPerfil() {
  const token = localStorage.getItem('token');
  if (!token) return;
  try {
    const res = await axios.get('http://127.0.0.1:8000/api/sesion', {
      headers: { Authorization: 'Bearer ' + token }
    });
    // Si la API devuelve datos de usuario, actualiza el store
    if (res.data && res.data.usuario) {
      usuario.value = res.data.usuario;
      localStorage.setItem('usuario', JSON.stringify(res.data.usuario));
    }
  } catch (e) {
    // Si falla, cierra sesión
    cerrarSesion();
  }
}

// Verificación de sesión cada 8 segundos
async function verificarSesionPeriodicamente() {
  sesionIntervalId = setInterval(async () => {
    const token = localStorage.getItem('token');
    if (!token) return;

    try {
      await axios.get('http://127.0.0.1:8000/api/validar-sesion', {
        headers: { Authorization: 'Bearer ' + token },
        timeout: 9000 
      });

      // Si la respuesta es exitosa, quitar mensaje de conexión perdida
      if (conexionPerdida.value) {
        conexionPerdida.value = false;
      }

    } catch (e) {
      // Error 401 = Token inválido o sesión expirada
      if (e.response && e.response.status === 401) {
        localStorage.clear();
        sessionStorage.clear();
        alert('Por seguridad, tu sesión ha finalizado. Serás redirigido al inicio de sesión para continuar.');
        window.location.replace('/login');
      } 
      // Otros errores = problemas de conexión
      else if (e.code === 'ECONNABORTED' || !e.response) {
        // Solo mostrar mensaje de conexión perdida, NO cerrar sesión
        conexionPerdida.value = true;
        console.warn('Error de conexión con el servidor:', e.message);
      }
      // Error 500, 503, etc. = servidor tiene problemas
      else if (e.response && e.response.status >= 500) {
        conexionPerdida.value = true;
        console.warn('El servidor está experimentando problemas:', e.response.status);
      }
    }
  }, 8000);
}

onMounted(() => {
  // Solo verificar si hay usuario logueado
  if (usuario.value && usuario.value.id) {
    verificarSesionPeriodicamente();
  }
});

onUnmounted(() => {
  if (sesionIntervalId) {
    clearInterval(sesionIntervalId);
  }
});
</script>

<style scoped>
body {
  margin: 0;
  font-family: 'Montserrat', sans-serif;
}

.navbar {
  background-color: #3498db;
  color: white;
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 10px 40px;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
}

.navbar-logo {
  display: flex;
  align-items: center;
}

.navbar-logo img {
  height: 70px;
  margin-right: 0;
}

.navbar-logo h1 {
  display: none;
}

.navbar-toggle {
  display: none;
  background: none;
  border: none;
  color: white;
  font-size: 1.5rem;
  cursor: pointer;
  padding: 5px;
  border-radius: 4px;
  transition: background-color 0.3s;
}

.navbar-toggle:hover {
  background-color: rgba(255, 255, 255, 0.1);
}

.navbar-links {
  display: flex;
  align-items: center;
}

.navbar-links a {
  margin-left: 25px;
  color: white;
  text-decoration: none;
  font-weight: 600;
  font-size: 1.1rem;
  transition: color 0.3s;
}

.navbar-links a:hover {
  color: #bbdefb;
}

.navbar-logo a {
  text-decoration: none;
  color: inherit;
}

.btn {
  padding: 10px 20px;
  font-size: 1rem;
  font-weight: 600;
  border-radius: 8px;
  cursor: pointer;
  transition: all 0.3s ease;
  border: none;
}

.btn-primary {
  background-color: #2196f3;
  color: white;
}

.btn-primary:hover {
  background-color: #1976d2;
  transform: translateY(-1px);
}

.btn-outline {
  background-color: transparent;
  color: #2196f3;
  border: 2px solid #2196f3;
}

.btn-outline:hover {
  background-color: #e3f2fd;
  transform: translateY(-1px);
}

.btn-secondary {
  background-color: #90caf9;
  color: white;
}

.btn-secondary:hover {
  background-color: #64b5f6;
  transform: translateY(-1px);
}

.btn-danger {
  background-color: #f53535;
  color: white;
}

.btn-danger:hover {
  background-color: #f98585;
  transform: translateY(-1px);
}

.modal-perfil {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: rgba(0, 0, 0, 0.85);
  display: flex;
  justify-content: center;
  align-items: center;
  z-index: 1000;
  animation: fadeIn 0.3s ease-out;
}

.modal-content {
  background: #ffffff;
  border-radius: 12px;
  max-width: 600px;
  width: 90%;
  max-height: 85vh;
  overflow-y: auto;
  box-shadow: 0 4px 20px rgba(13, 71, 161, 0.15);
  padding: 0;
}

.modal-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 20px 30px;
  border-bottom: 1px solid #bbdefb;
  background: #e3f2fd;
  border-radius: 12px 12px 0 0;
}

.modal-header h2 {
  font-size: 1.8rem;
  margin: 0;
  color: #0d47a1;
}

.close-btn {
  background: none;
  border: none;
  font-size: 1.5rem;
  color: #1976d2;
  cursor: pointer;
  transition: color 0.2s;
}

.close-btn:hover {
  color: #0d47a1;
}

.profile-info {
  padding: 30px;
}

.info-card {
  background: #e3f2fd;
  border-radius: 8px;
  padding: 20px;
  margin-bottom: 20px;
}

.info-card h3 {
  font-size: 1.3rem;
  color: #0d47a1;
  margin: 0 0 15px;
}

.info-row {
  display: flex;
  justify-content: space-between;
  margin-bottom: 12px;
  padding: 8px 0;
  border-bottom: 1px solid #bbdefb;
}

.info-label {
  font-weight: 600;
  color: #1976d2;
  width: 30%;
}

.info-value {
  color: #0d47a1;
  width: 70%;
  text-align: right;
}

.modal-footer {
  padding: 20px 30px;
  border-top: 1px solid #bbdefb;
  display: flex;
  gap: 10px;
  justify-content: flex-end;
  background: #e3f2fd;
  border-radius: 0 0 12px 12px;
}

.noti-bell {
  color: white;
  font-size: 32px;
  display: flex;
  align-items: center;
  position: relative;
}

.noti-count {
  position: absolute;
  top: -6px;
  right: -6px;
  background: #ffcc80;
  color: #0d47a1;
  border-radius: 50%;
  font-size: 0.85em;
  padding: 2px 7px;
  font-weight: bold;
  min-width: 22px;
  text-align: center;
  box-shadow: 0 1px 4px rgba(0,0,0,0.15);
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

@keyframes fadeIn {
  from {
    opacity: 0;
  }
  to {
    opacity: 1;
  }
}

/* Alerta de conexión perdida */
.conexion-perdida {
  position: fixed;
  top: 90px;
  left: 50%;
  transform: translateX(-50%);
  z-index: 1001;
  background: #fff3cd;
  color: #856404;
  border: 1px solid #ffeaa7;
  border-radius: 8px;
  padding: 12px 20px;
  font-weight: 600;
  box-shadow: 0 4px 12px rgba(0,0,0,0.15);
  animation: fadeIn 0.3s ease-out;
}

.conexion-perdida i {
  margin-right: 8px;
}

/* Media queries para responsividad */
@media (max-width: 768px) {
  .navbar {
    padding: 8px 20px;
    position: relative;
  }
  
  .navbar-toggle {
    display: block;
  }
  
  .navbar-links {
    display: none;
    position: absolute;
    top: 100%;
    left: 0;
    right: 0;
    background-color: #3498db;
    flex-direction: column;
    padding: 20px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    z-index: 999;
  }
  
  .navbar-links.mobile-menu-open {
    display: flex;
  }
  
  .navbar-links a {
    margin: 8px 0;
    padding: 12px 0;
    border-bottom: 1px solid rgba(255, 255, 255, 0.1);
    text-align: center;
    width: 100%;
  }
  
  .navbar-links .btn {
    margin: 10px 0;
    width: 100%;
    text-align: center;
  }
  
  .modal-content {
    width: 95%;
    max-width: none;
    margin: 10px;
    max-height: 90vh;
  }
  
  .modal-header {
    padding: 15px 20px;
  }
  
  .modal-header h2 {
    font-size: 1.5rem;
  }
  
  .profile-info {
    padding: 20px;
  }
  
  .info-row {
    flex-direction: column;
    align-items: flex-start;
    gap: 5px;
  }
  
  .info-label {
    width: 100%;
    font-size: 0.9rem;
  }
  
  .info-value {
    width: 100%;
    text-align: left;
    font-size: 0.95rem;
    color: #555;
  }
  
  .modal-footer {
    padding: 15px 20px;
    flex-direction: column;
    gap: 10px;
  }
  
  .modal-footer .btn {
    width: 100%;
    text-align: center;
  }
  
  .btn {
    padding: 12px 16px;
    font-size: 0.95rem;
  }
}

@media (max-width: 480px) {
  .navbar {
    padding: 8px 15px;
  }
  
  .navbar-logo img {
    height: 60px;
  }
  
  .navbar-links {
    gap: 10px;
  }
  
  .navbar-links a {
    font-size: 0.9rem;
  }
  
  .modal-content {
    width: 98%;
    margin: 5px;
    border-radius: 8px;
  }
  
  .modal-header {
    padding: 12px 15px;
    border-radius: 8px 8px 0 0;
  }
  
  .modal-header h2 {
    font-size: 1.3rem;
  }
  
  .close-btn {
    font-size: 1.3rem;
  }
  
  .profile-info {
    padding: 15px;
  }
  
  .info-card {
    padding: 15px;
  }
  
  .info-card h3 {
    font-size: 1.1rem;
  }
  
  .info-row {
    padding: 6px 0;
    margin-bottom: 8px;
  }
  
  .info-label {
    font-size: 0.85rem;
  }
  
  .info-value {
    font-size: 0.9rem;
  }
  
  .modal-footer {
    padding: 12px 15px;
    border-radius: 0 0 8px 8px;
  }
  
  .btn {
    padding: 10px 14px;
    font-size: 0.9rem;
  }
}

@media (max-width: 360px) {
  .navbar-toggle {
    font-size: 1.3rem;
  }
  
  .modal-content {
    height: 95vh;
    margin: 2.5vh auto;
  }
  
  .modal-header h2 {
    font-size: 1.2rem;
  }
  
  .info-card h3 {
    font-size: 1rem;
  }
  
  .info-label {
    font-size: 0.8rem;
  }
  
  .info-value {
    font-size: 0.85rem;
  }
}
</style>
