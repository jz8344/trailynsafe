import axios from 'axios';

// Determina host dinámico: usa variable de entorno VITE_API_HOST si existe, si no el hostname actual.
const host = import.meta.env.VITE_API_HOST || window.location.hostname;
// Permite forzar protocolo si se define VITE_API_PROTOCOL, por defecto http
const protocol = import.meta.env.VITE_API_PROTOCOL || 'http';
export const API_BASE_URL = `${protocol}://${host}:8000`;

// Instancia principal (usuarios / endpoints generales)
export const http = axios.create({
  baseURL: `${API_BASE_URL}/api`,
  timeout: 10000,
});

// Interceptor para adjuntar tokens automáticamente
http.interceptors.request.use((config) => {
  const userToken = localStorage.getItem('token');
  const adminToken = localStorage.getItem('admin_token');
  // Si es endpoint admin usar admin_token, si no token normal
  if (config.url?.startsWith('/admin/')) {
    if (adminToken) config.headers.Authorization = `Bearer ${adminToken}`;
  } else if (userToken) {
    config.headers.Authorization = `Bearer ${userToken}`;
  }
  return config;
});

http.interceptors.response.use(
  r => r,
  (error) => {
    // Si sesión de usuario caduca
    if (error.response?.status === 401) {
      // Dejar que cada vista maneje; opcional: emitir evento
    }
    return Promise.reject(error);
  }
);

export default http;
