<template>
  <MenuNav />
  <section class="multas-section">
    <div class="multas-container">
      <h2>Crear Multa</h2>
      <form @submit.prevent="crearMulta" class="multas-form">
        <div class="form-group">
          <label for="huesped_id">Huésped</label>
          <select v-model="form.huesped_id" required>
            <option value="" disabled>Selecciona un huésped</option>
            <option v-for="h in huespedes" :key="h.id" :value="h.id">
              {{ h.nombre }}
            </option>
          </select>
        </div>
        <div class="form-group">
          <label for="monto">Monto</label>
          <input type="number" v-model="form.monto" required min="1" />
        </div>
        <div class="form-group">
          <label for="motivo">Motivo</label>
          <input type="text" v-model="form.motivo" required />
        </div>
        <div class="form-group">
          <label for="fecha_emision">Fecha de emisión</label>
          <input type="date" v-model="form.fecha_emision" required />
        </div>
        <div class="form-group">
          <label for="estado">Estado</label>
          <select v-model="form.estado" required>
            <option value="pendiente">Pendiente</option>
            <option value="pagada">Pagada</option>
          </select>
        </div>
        <button class="btn btn-primary" type="submit" :disabled="loading">
          {{ loading ? 'Enviando...' : 'Crear Multa' }}
        </button>
        <p v-if="error" class="error">{{ error }}</p>
      </form>
    </div>
  </section>




  <transition name="slide-fade">

    <div v-if="success" class="modal-overlay">

      <div class="modal-content">
        <h3>¡Éxito!</h3>
        <p>Multa creada correctamente</p>
        <button class="btn btn-close" @click="success = false"></button>
      </div>

      
    </div>
  </transition>





</template>

<script setup>
import { ref, reactive, onMounted } from 'vue';
import axios from 'axios';
import MenuNav from '@/components/MenuNav.vue';

const huespedes = ref([]);
const loading = ref(false);
const error = ref('');
const success = ref(false);
const form = reactive({
  huesped_id: '',
  monto: '',
  motivo: '',
  fecha_emision: '',
  estado: 'pendiente'
});

onMounted(async () => {
  try {
    const res = await axios.get('http://127.0.0.1:8000/api/huespedes', {
      headers: {
        Authorization: 'Bearer ' + localStorage.getItem('token')
      }
    });
    huespedes.value = res.data;
  } catch (e) {
    error.value = 'No se pudieron cargar los huéspedes';
  }
});

async function crearMulta() {
  loading.value = true;
  success.value = false;
  error.value = '';
  try {
    await axios.post('http://127.0.0.1:8000/api/multas', { ...form }, {
      headers: {
        'Content-Type': 'application/json',
        Authorization: 'Bearer ' + localStorage.getItem('token')
      }
    });
    success.value = true;
    form.huesped_id = '';
    form.monto = '';
    form.motivo = '';
    form.fecha_emision = '';
    form.estado = 'pendiente';
  } catch (e) {
    error.value = e.response?.data?.error || 'Error al crear la multa';
  } finally {
    loading.value = false;
  }
}
</script>

<style scoped>
.multas-section {
  min-height: 100vh;
  background: #f8f8f8;
  display: flex;
  align-items: center;
  justify-content: center;
}
.multas-container {
  background: white;
  padding: 40px;
  border-radius: 20px;
  max-width: 400px;
  width: 100%;
  box-shadow: 0 4px 20px rgba(0,0,0,0.1);
}
.multas-form {
  display: flex;
  flex-direction: column;
  gap: 15px;
}
.form-group {
  display: flex;
  flex-direction: column;
  gap: 5px;
}
input, select {
  padding: 10px;
  border-radius: 8px;
  border: 1px solid #ccc;
  font-size: 1rem;
}
.btn {
  padding: 12px 20px;
  font-size: 1rem;
  font-weight: 600;
  border: none;
  border-radius: 20px;
  cursor: pointer;
  background-color: #009FE3;
  color: white;
  transition: 0.3s;
}
.btn:disabled {
  background: #aaa;
}
.error {
  color: red;
  margin-top: 10px;
}




.modal-overlay {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: rgba(0, 0, 0, 0.5);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 1000;
}
.modal-content {
  background: white;
  padding: 20px;
  border-radius: 10px;
  max-width: 400px;
  width: 90%;
  text-align: center;
  box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
}
.btn-close {
  background-color: #ff4d4f;
  margin-top: 15px;
}
.slide-fade-enter-active {
  transition: all 0.3s ease;
}
.slide-fade-leave-active {
  transition: all 0.8s cubic-bezier(1.0, 0.5, 0.8, 1.0);
}
.slide-fade-enter, .slide-fade-leave-to {
  transform: translateX(10px);
  opacity: 0;
}
</style>