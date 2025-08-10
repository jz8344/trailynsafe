<template>
  <div class="min-vh-100 bg-light">
    <nav class="navbar navbar-expand-lg navbar-dark bg-white shadow-sm border-bottom">
      <div class="container-fluid px-4">
        <router-link to="/admin/dashboard" class="navbar-brand d-flex align-items-center text-decoration-none">
          <div class="d-inline-flex align-items-center justify-content-center rounded-3 me-3" 
               style="width: 40px; height: 40px; background: linear-gradient(135deg, #7c3aed, #3b82f6);">
            <i class="bi bi-person-circle text-white"></i>
          </div>
          <div>
            <h5 class="mb-0 text-dark fw-bold">Mi Perfil Admin</h5>
            <small class="text-muted">Configuración de cuenta</small>
          </div>
        </router-link>
        
        <router-link 
          to="/admin/dashboard"
          class="btn btn-secondary d-flex align-items-center"
        >
          <i class="bi bi-arrow-left me-2"></i>
          Volver al Dashboard
        </router-link>
      </div>
    </nav>

    <main class="container py-4">
      <div class="card shadow-lg border-0 rounded-4 overflow-hidden">
        <div class="px-4 py-5 text-white" style="background: linear-gradient(135deg, #7c3aed, #3b82f6);">
          <div class="d-flex align-items-center">
            <div class="d-inline-flex align-items-center justify-content-center rounded-circle bg-white me-4" 
                 style="width: 80px; height: 80px;">
              <i class="bi bi-person-circle fs-1" style="color: #7c3aed;"></i>
            </div>
            <div>
              <h2 class="fw-bold mb-1">{{ adminData?.nombre }} {{ adminData?.apellidos }}</h2>
              <p class="mb-1 opacity-75">Administrador del Sistema</p>
              <p class="mb-0 fs-6 opacity-50">{{ adminData?.correo }}</p>
            </div>
          </div>
        </div>

        <div class="card-body p-5">
          <form @submit.prevent="actualizarPerfil">
            <div v-if="error" class="alert alert-danger d-flex align-items-center" role="alert">
              <i class="bi bi-exclamation-triangle-fill me-2"></i>
              {{ error }}
            </div>

            <div v-if="success" class="alert alert-success d-flex align-items-center" role="alert">
              <i class="bi bi-check-circle-fill me-2"></i>
              {{ success }}
            </div>

            <div class="row mb-4">
              <div class="col-md-6 mb-3">
                <label class="form-label fw-medium">Nombre</label>
                <div class="input-group">
                  <span class="input-group-text">
                    <i class="bi bi-person"></i>
                  </span>
                  <input
                    v-model="form.nombre"
                    type="text"
                    class="form-control form-control-lg"
                    required
                  />
                </div>
              </div>

              <div class="col-md-6 mb-3">
                <label class="form-label fw-medium">Apellidos</label>
                <div class="input-group">
                  <span class="input-group-text">
                    <i class="bi bi-person"></i>
                  </span>
                  <input
                    v-model="form.apellidos"
                    type="text"
                    class="form-control form-control-lg"
                    required
                  />
                </div>
              </div>
            </div>

            <div class="mb-4">
              <label class="form-label fw-medium">Teléfono</label>
              <div class="input-group">
                <span class="input-group-text">
                  <i class="bi bi-telephone"></i>
                </span>
                <input
                  v-model="form.telefono"
                  type="tel"
                  class="form-control form-control-lg"
                />
              </div>
            </div>

            <div class="mb-4">
              <label class="form-label fw-medium">Correo Electrónico</label>
              <div class="input-group">
                <span class="input-group-text">
                  <i class="bi bi-envelope"></i>
                </span>
                <input
                  :value="adminData?.correo"
                  type="email"
                  class="form-control form-control-lg"
                  disabled
                />
              </div>
              <div class="form-text">
                <i class="bi bi-info-circle me-1"></i>
                El correo no puede ser modificado desde aquí
              </div>
            </div>

            <div class="d-flex justify-content-end gap-3 pt-4 border-top">
              <button
                type="button"
                @click="resetForm"
                class="btn btn-outline-secondary"
              >
                Cancelar
              </button>
              <button
                type="submit"
                :disabled="loading"
                class="btn text-white"
                style="background: linear-gradient(135deg, #7c3aed, #3b82f6); border: none;"
              >
                <span v-if="loading" class="d-flex align-items-center">
                  <div class="spinner-border spinner-border-sm me-2" role="status">
                    <span class="visually-hidden">Loading...</span>
                  </div>
                  Actualizando...
                </span>
                <span v-else class="d-flex align-items-center">
                  <i class="bi bi-check-lg me-2"></i>
                  Actualizar Perfil
                </span>
              </button>
            </div>
          </form>
        </div>
      </div>

      <div class="card shadow-lg border-0 rounded-4 overflow-hidden mt-4">
        <div class="card-header bg-danger-subtle border-0 py-4">
          <h5 class="mb-1 text-danger fw-bold">
            <i class="bi bi-shield-lock me-2"></i>
            Cambiar Contraseña
          </h5>
          <p class="mb-0 text-danger-emphasis">Asegúrate de usar una contraseña segura</p>
        </div>
        
  <div class="card-body p-4 password-section">
          <form @submit.prevent="cambiarContrasena">
            <div v-if="passwordError" class="alert alert-danger d-flex align-items-center" role="alert">
              <i class="bi bi-exclamation-triangle-fill me-2"></i>
              {{ passwordError }}
            </div>

            <div v-if="passwordSuccess" class="alert alert-success d-flex align-items-center" role="alert">
              <i class="bi bi-check-circle-fill me-2"></i>
              {{ passwordSuccess }}
            </div>

            <div class="row g-3">
              <div class="col-md-4">
                <label class="form-label fw-medium">Contraseña Actual</label>
                <div class="input-group advanced-input" :class="{'has-error': passwordError}">
                  <span class="input-group-text icon">
                    <i class="bi bi-lock"></i>
                  </span>
                  <input
                    v-model="passwordForm.password_actual"
                    :type="showCurrent ? 'text' : 'password'"
                    class="form-control"
                    required
                    @input="passwordError = null"
                  />
                  <button type="button" class="toggle-btn" @click="showCurrent = !showCurrent" :aria-label="showCurrent ? 'Ocultar contraseña actual' : 'Mostrar contraseña actual'">
                    <i :class="showCurrent ? 'bi bi-eye-slash' : 'bi bi-eye'"></i>
                  </button>
                </div>
              </div>
              <div class="col-md-4">
                <label class="form-label fw-medium">Nueva Contraseña</label>
                <div class="input-group advanced-input" :class="{'has-error': passwordError}">
                  <span class="input-group-text icon">
                    <i class="bi bi-key"></i>
                  </span>
                  <input
                    v-model="passwordForm.nueva_password"
                    :type="showNew ? 'text' : 'password'"
                    class="form-control"
                    minlength="6"
                    required
                    @input="passwordError = null; evaluarFuerza()"
                  />
                  <button type="button" class="toggle-btn" @click="showNew = !showNew" :aria-label="showNew ? 'Ocultar nueva contraseña' : 'Mostrar nueva contraseña'">
                    <i :class="showNew ? 'bi bi-eye-slash' : 'bi bi-eye'"></i>
                  </button>
                </div>
                <div class="password-meter mt-2" v-if="passwordForm.nueva_password">
                  <div class="meter-bar" :class="strength.class" :style="{width: strength.percent + '%'}"></div>
                  <small class="strength-label" :class="strength.class">{{ strength.label }}</small>
                </div>
              </div>
              <div class="col-md-4">
                <label class="form-label fw-medium">Confirmar Nueva</label>
                <div class="input-group advanced-input" :class="{'has-error': passwordMismatch}">
                  <span class="input-group-text icon">
                    <i class="bi bi-key-fill"></i>
                  </span>
                  <input
                    v-model="passwordForm.nueva_password_confirmation"
                    :type="showConfirm ? 'text' : 'password'"
                    class="form-control"
                    minlength="6"
                    required
                    @input="passwordError = null"
                  />
                  <button type="button" class="toggle-btn" @click="showConfirm = !showConfirm" :aria-label="showConfirm ? 'Ocultar confirmación' : 'Mostrar confirmación'">
                    <i :class="showConfirm ? 'bi bi-eye-slash' : 'bi bi-eye'"></i>
                  </button>
                </div>
                <small v-if="passwordMismatch" class="text-danger d-block mt-1">No coincide</small>
              </div>
            </div>
            <div class="security-hints mt-3" v-if="passwordForm.nueva_password">
              <ul class="list-unstyled small m-0 d-flex flex-wrap gap-3">
                <li :class="{'ok': reglas.longitud}"><i class="bi" :class="reglas.longitud ? 'bi-check-circle-fill' : 'bi-x-circle'"/> >= 6</li>
                <li :class="{'ok': reglas.mayus}"><i class="bi" :class="reglas.mayus ? 'bi-check-circle-fill' : 'bi-x-circle'"/> Mayúscula</li>
                <li :class="{'ok': reglas.numero}"><i class="bi" :class="reglas.numero ? 'bi-check-circle-fill' : 'bi-x-circle'"/> Número</li>
                <li :class="{'ok': reglas.especial}"><i class="bi" :class="reglas.especial ? 'bi-check-circle-fill' : 'bi-x-circle'"/> Especial</li>
              </ul>
            </div>

            <div class="d-flex justify-content-end pt-3">
              <button
                type="submit"
                :disabled="passwordLoading"
                class="btn btn-danger d-flex align-items-center"
              >
                <span v-if="passwordLoading" class="d-flex align-items-center">
                  <div class="spinner-border spinner-border-sm me-2" role="status">
                    <span class="visually-hidden">Loading...</span>
                  </div>
                  Cambiando...
                </span>
                <span v-else class="d-flex align-items-center">
                  <i class="bi bi-shield-check me-2"></i>
                  Cambiar Contraseña
                </span>
              </button>
            </div>
          </form>
        </div>
      </div>
    </main>
  </div>
</template>

<script>
import axios from 'axios'
import { admin, loginAdmin } from '@/store/session.js'

export default {
  name: 'AdminPerfil',
  data() {
    return {
      form: {
        nombre: '',
        apellidos: '',
        telefono: ''
      },
      passwordForm: {
        password_actual: '',
        nueva_password: '',
        nueva_password_confirmation: ''
      },
      loading: false,
      passwordLoading: false,
      error: null,
      success: null,
      passwordError: null,
      passwordSuccess: null
    }
  },
  computed: {
    passwordMismatch() {
      return this.passwordForm.nueva_password && this.passwordForm.nueva_password_confirmation && this.passwordForm.nueva_password !== this.passwordForm.nueva_password_confirmation
    },
    strength() {
      const pwd = this.passwordForm.nueva_password || ''
      let score = 0
      if (pwd.length >= 6) score++
      if (/[A-Z]/.test(pwd)) score++
      if (/[0-9]/.test(pwd)) score++
      if (/[^A-Za-z0-9]/.test(pwd)) score++
      const labels = ['Débil', 'Regular', 'Buena', 'Fuerte']
      const classes = ['weak', 'fair', 'good', 'strong']
      return {
        label: labels[Math.max(0, score - 1)] || 'Débil',
        class: classes[Math.max(0, score - 1)] || 'weak',
        percent: (score / 4) * 100
      }
    }
  },
  computed: {
    adminData() {
      return admin.value
    }
  },
  mounted() {
    this.loadAdminData()
  },
  methods: {
    evaluarFuerza() {
      // trigger computed
    },
    loadAdminData() {
      if (this.adminData) {
        this.form.nombre = this.adminData.nombre || ''
        this.form.apellidos = this.adminData.apellidos || ''
        this.form.telefono = this.adminData.telefono || ''
      }
    },
    async actualizarPerfil() {
      this.loading = true
      this.error = null
      this.success = null

      try {
        const token = localStorage.getItem('admin_token')
        const response = await axios.post(
          'http://127.0.0.1:8000/api/admin/editar-perfil',
          this.form,
          {
            headers: { Authorization: `Bearer ${token}` }
          }
        )

        this.success = response.data.message
        loginAdmin(response.data.admin)
      } catch (error) {
        this.error = error.response?.data?.error || 'Error al actualizar perfil'
      } finally {
        this.loading = false
      }
    },
    async cambiarContrasena() {
      if (this.passwordForm.nueva_password !== this.passwordForm.nueva_password_confirmation) {
        this.passwordError = 'Las contraseñas no coinciden'
        return
      }

      this.passwordLoading = true
      this.passwordError = null
      this.passwordSuccess = null

      try {
        const token = localStorage.getItem('admin_token')
        const response = await axios.post(
          'http://127.0.0.1:8000/api/admin/actualizar-contrasena',
          this.passwordForm,
          {
            headers: { Authorization: `Bearer ${token}` }
          }
        )

        this.passwordSuccess = response.data.message
        this.passwordForm = {
          password_actual: '',
          nueva_password: '',
          nueva_password_confirmation: ''
        }
      } catch (error) {
        this.passwordError = error.response?.data?.error || 'Error al cambiar contraseña'
      } finally {
        this.passwordLoading = false
      }
    },
    resetForm() {
      this.loadAdminData()
      this.error = null
      this.success = null
    }
  }
}
</script>

<style scoped>
.password-section {
  background: linear-gradient(135deg, #ffffff 0%, #f8f5ff 100%);
}
.advanced-input {
  position: relative;
  display: flex;
  border: 2px solid #e5e0fa;
  border-radius: 14px;
  overflow: hidden;
  background: #faf9fe;
  transition: all .25s ease;
}
.advanced-input:focus-within {
  border-color: #7c3aed;
  box-shadow: 0 0 0 3px rgba(124,58,237,.15);
  background: #fff;
}
.advanced-input.has-error {
  border-color: #dc3545;
  box-shadow: 0 0 0 3px rgba(220,53,69,.15);
}
.advanced-input .icon {
  background: transparent;
  border: none;
  font-size: 1.05rem;
  color: #7c3aed;
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 10px 14px;
}
.advanced-input .form-control {
  border: none;
  background: transparent;
  padding: 12px 14px;
  font-size: .95rem;
  font-weight: 500;
}
.advanced-input .form-control:focus {
  box-shadow: none;
}
.toggle-btn {
  background: none;
  border: none;
  padding: 0 14px;
  cursor: pointer;
  color: #6b5fa5;
  display: flex;
  align-items: center;
  transition: color .25s ease, background .25s ease;
}
.toggle-btn:hover {
  color: #7c3aed;
  background: rgba(124,58,237,.08);
}
.password-meter {
  position: relative;
  height: 8px;
  background: #ece7fb;
  border-radius: 6px;
  overflow: hidden;
}
.password-meter .meter-bar {
  height: 100%;
  transition: width .35s ease;
  border-radius: 6px;
  background: linear-gradient(90deg,#f87171,#fb923c,#fbbf24,#34d399,#4ade80);
}
.strength-label {
  display: inline-block;
  margin-top: 4px;
  font-size: .7rem;
  font-weight: 600;
  letter-spacing: .5px;
  text-transform: uppercase;
}
.strength-label.weak { color: #dc2626; }
.strength-label.fair { color: #d97706; }
.strength-label.good { color: #0d9488; }
.strength-label.strong { color: #059669; }
.security-hints li {
  display: flex;
  align-items: center;
  gap: 4px;
  padding: 6px 10px;
  background: #efeafc;
  border-radius: 10px;
  color: #6b5fa5;
  font-weight: 500;
}
.security-hints li.ok {
  background: #e1f8ef;
  color: #0d6846;
}
.security-hints li i { font-size: .8rem; }
@media (max-width: 992px) {
  .row.g-3 > div { flex: 0 0 100%; max-width: 100%; }
}
</style>
