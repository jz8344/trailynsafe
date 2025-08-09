import axios from 'axios';
import { logoutUsuario } from '@/store/session.js';

export async function validarSesion() {
  const token = localStorage.getItem('token');
  
  if (!token) {
    limpiarSesion();
    return false;
  }

  try {
    const response = await axios.get(
      'http://127.0.0.1:8000/api/validar-sesion',
      {
        headers: { Authorization: `Bearer ${token}` },
        timeout: 5000 // 5 segundos timeout
      }
    );

    if (response.data.valida) {
      return true;
    } else {
      limpiarSesion();
      return false;
    }
  } catch (error) {
    console.log('Sesión inválida:', error.response?.data?.mensaje);
    limpiarSesion();
    return false;
  }
}

export function limpiarSesion() {
  localStorage.clear();
  sessionStorage.clear();
  
  // Limpiar cookies si las hay
  document.cookie.split(";").forEach(function(c) { 
    document.cookie = c.replace(/^ +/, "").replace(/=.*/, "=;expires=" + new Date().toUTCString() + ";path=/"); 
  });
  
  // Limpiar el store
  logoutUsuario();
  
  // Redirigir al login INMEDIATAMENTE
  if (window.location.pathname !== '/login' && 
      window.location.pathname !== '/register' && 
      window.location.pathname !== '/recuperar-password') {
    window.location.href = '/login';
    setTimeout(() => {
      window.location.reload();
    }, 100);
  }
}

// Interceptor para axios que valida automáticamente las respuestas
axios.interceptors.response.use(
  (response) => response,
  (error) => {
    if (error.response?.status === 401) {
      // Si el servidor responde con 401, la sesión es inválida
      console.log('401 detectado - limpiando sesión');
      limpiarSesion();
    }
    return Promise.reject(error);
  }
);

// Función para verificar sesión cada 30 segundos (más agresivo)
export function iniciarValidacionPeriodica() {
  // Validar inmediatamente al cargar
  setTimeout(() => {
    const token = localStorage.getItem('token');
    if (token) {
      validarSesion();
    }
  }, 1000);

  // Validar cada 30 segundos
  setInterval(async () => {
    const token = localStorage.getItem('token');
    if (token) {
      await validarSesion();
    }
  }, 30 * 1000); // 30 segundos
}

// Función para validar al cambiar de pestaña/ventana
export function iniciarValidacionVisibilidad() {
  document.addEventListener('visibilitychange', async () => {
    if (!document.hidden) {
      // Cuando el usuario vuelve a la pestaña, validar sesión
      const token = localStorage.getItem('token');
      if (token) {
        await validarSesion();
      }
    }
  });
}
