<template>
  <MenuNav />
  <section class="auth-section">
    <div class="auth-container">
      <form id="login-form" class="auth-form" @submit.prevent="login">
        <h2>Iniciar Sesión</h2>
        <input 
          v-model="form.correo" 
          type="email" 
          placeholder="Correo Electrónico" 
          required 
          :disabled="loading"
        />
        <div class="input-group password-group">
          <input
            v-model="form.contrasena"
            :type="showPassword ? 'text' : 'password'"
            placeholder="Contraseña"
            required
            :disabled="loading"
          />
          <button type="button" class="btn-eye" @click="showPassword = !showPassword">
            <i :class="showPassword ? 'bi bi-eye-slash' : 'bi bi-eye'" ></i>
          </button>
        </div>
        <button type="submit" class="btn btn-primary" :disabled="loading">
          <span v-if="loading" class="spin"></span>
          {{ loading ? 'Iniciando sesión...' : 'Iniciar Sesión' }}
        </button>
        <p class="mt-2">
          <router-link to="/recuperar-password">¿Olvidaste tu contraseña?</router-link>
        </p>
        <div class="auth-links">
          <span class="auth-text">¿No tienes cuenta?</span>
          <router-link to="/register" class="auth-link">
            ¡Regístrate aquí!
          </router-link>
        </div>
        <div v-if="error" class="error-message">{{ error }}</div>
        <div v-if="success" class="success-message">{{ success }}</div>
      </form>
    </div>
  </section>
</template>

<script setup>
import { reactive, ref } from 'vue';
import { useRouter } from 'vue-router';
import axios from 'axios';
import MenuNav from '@/components/MenuNav.vue';
import { loginUsuario } from '@/store/session.js';

const router = useRouter();
const form = reactive({
  correo: '',
  contrasena: ''
});
const error = ref('');
const success = ref('');
const loading = ref(false);
const showPassword = ref(false);

async function login() {
  error.value = '';
  success.value = '';
  loading.value = true;
  
  try {
    const res = await axios.post('http://127.0.0.1:8000/api/login', form);
    
    if (res.data.token && res.data.usuario) {
      localStorage.setItem('token', res.data.token);
      loginUsuario(res.data.usuario);
      
      success.value = 'Inicio de sesión exitoso. Redirigiendo...';
      
      setTimeout(() => {
        router.push('/');
      }, 1000);
    } else {
      error.value = 'Respuesta inesperada del servidor';
    }
  } catch (e) {
    console.error('Error en login:', e);
    error.value = e.response?.data?.error || 'Error al iniciar sesión. Verifica tus credenciales.';
  } finally {
    loading.value = false;
  }
}
</script>

<style scoped>
body {
  margin: 0;
  font-family: 'Montserrat', sans-serif;
  background-color: #111;
}

.auth-section {
  background: url('../img/school.png') no-repeat center center/cover;
  min-height: 100vh;
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 20px;
  position: relative;
}

.auth-section::before {
  content: '';
  position: absolute;
  inset: 0;
  background: rgba(0, 0, 0, 0.6);
  z-index: 1;
}

.auth-container {
  background-color: white;
  padding: 40px;
  border-radius: 20px;
  max-width: 500px;
  width: 100%;
  box-shadow: 0 4px 20px rgba(0,0,0,0.2);
  position: relative;
  z-index: 2;
}

.auth-form {
  display: flex;
  flex-direction: column;
  gap: 15px;
}

.auth-form h2 {
  font-size: 1.8rem;
  margin-bottom: 20px;
  color: #3582ff;
  text-align: center;
}

.auth-form input {
  padding: 12px;
  border: 1px solid #ddd;
  background-color: #f1f6fb;
  border-radius: 8px;
  font-family: 'Montserrat', sans-serif;
  font-size: 1rem;
  transition: border-color 0.3s;
}

.auth-form input:focus {
  outline: none;
  border-color: #009FE3;
}

.auth-form input:disabled {
  opacity: 0.6;
  cursor: not-allowed;
}

.btn {
  padding: 12px 20px;
  font-size: 1rem;
  font-weight: 600;
  border: none;
  border-radius: 8px;
  cursor: pointer;
  transition: all 0.3s;
  position: relative;
}

.btn-primary {
  background-color: #009FE3;
  color: white;
}

.btn-primary:hover:not(:disabled) {
  background-color: #007cb3;
  transform: translateY(-1px);
}

.btn:disabled {
  opacity: 0.6;
  cursor: not-allowed;
  transform: none;
}

.error-message {
  color: #dc3545;
  font-size: 0.9rem;
  padding: 10px;
  background-color: #f8d7da;
  border: 1px solid #f5c6cb;
  border-radius: 4px;
  text-align: center;
}

.success-message {
  color: #155724;
  font-size: 0.9rem;
  padding: 10px;
  background-color: #d4edda;
  border: 1px solid #c3e6cb;
  border-radius: 4px;
  text-align: center;
}

.input-group {
  display: flex;
  gap: 10px;
}

.password-group {
  display: flex;
  align-items: center;
}
.btn-eye {
  background: none;
  border: none;
  cursor: pointer;
  padding: 0 10px;
  font-size: 1.3rem;
  color: #717501;
  display: flex;
  align-items: center;
}

.btn .spin {
  display: inline-block;
  vertical-align: middle;
  margin-right: 8px;
  width: 1.1em;
  height: 1.1em;
  border: 2.5px solid #fff;
  border-top: 2.5px solid #009FE3;
  border-radius: 50%;
  animation: spin 1s linear infinite;
}
@keyframes spin {
  from { transform: rotate(0deg); }
  to { transform: rotate(360deg); }
}

@media (max-width: 768px) {
  .auth-container {
    padding: 20px;
  }
}
</style>