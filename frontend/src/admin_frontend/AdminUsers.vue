<template>
  <div class="admin-layout">
    <header class="topbar">
      <div class="left">
        <div class="logo-mini">TS</div>
        <nav class="quick-nav">
          <router-link to="/admin/dashboard">Panel</router-link>
          <router-link to="/admin/usuarios" class="active">Usuarios</router-link>
          <router-link to="/admin/perfil">Perfil</router-link>
        </nav>
      </div>
      <div class="center">
        <span class="datetime">{{ fecha }} · {{ hora }}</span>
      </div>
      <div class="right">
        <button class="icon-btn" @click="refrescar"><i class="bi bi-arrow-clockwise"/></button>
        <button class="icon-btn"><i class="bi bi-bell"/></button>
        <div class="avatar" @click="goPerfil" :title="adminData?.nombre">
          {{ inicialesAdmin }}
        </div>
        <button class="icon-btn" @click="logout" title="Salir"><i class="bi bi-box-arrow-right"/></button>
      </div>
    </header>

    <main class="workspace">
      <section class="toolbar card">
        <div class="actions">
          <button class="btn primary" @click="abrirModalCrear"><i class="bi bi-plus-lg"/> Nuevo Usuario</button>
          <button class="btn outline" :disabled="!seleccionados.length" @click="borrarSeleccionados"><i class="bi bi-trash"/> Eliminar Selección ({{ seleccionados.length }})</button>
        </div>
        <div class="filters">
          <input v-model="filtro" type="text" placeholder="Buscar nombre / correo..." />
          <select v-model="rolFiltro">
            <option value="">Todos</option>
            <option value="usuario">Usuarios</option>
            <option value="admin">Admins</option>
          </select>
        </div>
      </section>

      <section class="card grid-container">
        <div v-if="cargando" class="loading-state">
          <div class="spinner-border" role="status"></div>
          <span>Cargando usuarios...</span>
        </div>
        <div v-else class="grid-users">
          <div
            v-for="u in filtrados"
            :key="u.id"
            class="user-card"
            :class="{ selected: seleccionados.includes(u.id) }"
            @click="toggleSeleccion(u.id)"
          >
            <div class="user-head">
              <div class="circle" :class="u.rol">{{ initials(u) }}</div>
              <div class="info">
                <h5>{{ u.nombre }} {{ u.apellidos }}</h5>
                <small>{{ u.correo }}</small>
              </div>
              <button class="mini-btn" @click.stop="editar(u)" title="Editar"><i class="bi bi-pencil"/></button>
            </div>
            <div class="meta">
              <span class="tag" :class="u.rol">{{ u.rol }}</span>
              <span class="date">{{ formatearFecha(u.fecha_registro) }}</span>
            </div>
          </div>
        </div>
      </section>
    </main>

    <div v-if="mostrarModal" class="modal-overlay">
      <div class="modal">
        <h4 class="mb-3">{{ editando ? 'Editar Usuario' : 'Crear Usuario' }}</h4>
        <form @submit.prevent="guardar">
          <div class="row">
            <div class="col">
              <label>Nombre</label>
              <input v-model="form.nombre" required />
            </div>
            <div class="col">
              <label>Apellidos</label>
              <input v-model="form.apellidos" required />
            </div>
          </div>
          <div class="row">
            <div class="col">
              <label>Correo</label>
              <input v-model="form.correo" type="email" required :disabled="editando" />
            </div>
            <div class="col">
              <label>Teléfono</label>
              <input v-model="form.telefono" />
            </div>
          </div>
          <div class="row" v-if="!editando">
            <div class="col">
              <label>Contraseña</label>
              <div class="pwd-group">
                <input v-model="form.contrasena" :type="showPwd ? 'text':'password'" minlength="6" required />
                <button type="button" @click="showPwd=!showPwd"><i :class="showPwd? 'bi bi-eye-slash':'bi bi-eye'"/></button>
              </div>
            </div>
          </div>
          <div class="error" v-if="error">{{ error }}</div>
          <div class="modal-actions">
            <button type="button" class="btn outline" @click="cerrarModal">Cancelar</button>
            <button type="submit" class="btn primary" :disabled="guardando">
              <span v-if="guardando" class="spinner-border spinner-border-sm me-2"/>
              {{ guardando ? 'Guardando...' : 'Guardar' }}
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script>
import axios from 'axios'
import { admin, logoutAdmin } from '@/store/session.js'

export default {
  name: 'AdminUsers',
  data() {
    return {
      usuarios: [],
      cargando: false,
      error: null,
      filtro: '',
      rolFiltro: '',
      seleccionados: [],
      mostrarModal: false,
      editando: false,
      form: { id:null, nombre:'', apellidos:'', correo:'', telefono:'', contrasena:'' },
      guardando: false,
      showPwd: false,
      fecha: '',
      hora: '',
    }
  },
  computed: {
    adminData() { return admin.value },
    inicialesAdmin() { return this.adminData ? (this.adminData.nombre||'A')[0].toUpperCase() : 'A' },
    filtrados() {
      return this.usuarios.filter(u => {
        const texto = (u.nombre + ' ' + u.apellidos + ' ' + u.correo).toLowerCase();
        const passFiltro = !this.filtro || texto.includes(this.filtro.toLowerCase());
        const passRol = !this.rolFiltro || u.rol === this.rolFiltro;
        return passFiltro && passRol;
      });
    }
  },
  created() {
    this.cargar();
    this.actualizarFechaHora();
    setInterval(this.actualizarFechaHora, 60000);
  },
  methods: {
    actualizarFechaHora() {
      const now = new Date();
      this.fecha = now.toLocaleDateString('es-MX', { weekday:'short', day:'2-digit', month:'short', year:'numeric' });
      this.hora = now.toLocaleTimeString('es-MX', { hour:'2-digit', minute:'2-digit' });
    },
    async cargar() {
      this.cargando = true; this.error = null;
      try {
        const token = localStorage.getItem('admin_token');
        const res = await axios.get('http://127.0.0.1:8000/api/admin/usuarios', { headers:{ Authorization: 'Bearer '+token }});
        this.usuarios = res.data;
      } catch (e) {
        this.error = 'Error cargando usuarios';
      } finally { this.cargando = false; }
    },
    refrescar() { this.cargar(); },
    initials(u){ return (u.nombre?.[0]||'').toUpperCase(); },
    toggleSeleccion(id){
      if (this.seleccionados.includes(id)) this.seleccionados = this.seleccionados.filter(i=>i!==id); else this.seleccionados.push(id);
    },
    abrirModalCrear(){ this.editando=false; this.form={id:null,nombre:'',apellidos:'',correo:'',telefono:'',contrasena:''}; this.showPwd=false; this.mostrarModal=true; this.error=null; },
    editar(u){ this.editando=true; this.form={id:u.id,nombre:u.nombre,apellidos:u.apellidos,correo:u.correo,telefono:u.telefono,contrasena:''}; this.mostrarModal=true; this.error=null; },
    cerrarModal(){ this.mostrarModal=false; },
    async guardar(){
      this.guardando=true; this.error=null;
      try {
        const token = localStorage.getItem('admin_token');
        if (this.editando){
          await axios.put('http://127.0.0.1:8000/api/admin/usuarios/'+this.form.id, this.form, { headers:{ Authorization:'Bearer '+token }});
        } else {
          await axios.post('http://127.0.0.1:8000/api/admin/usuarios', this.form, { headers:{ Authorization:'Bearer '+token }});
        }
        this.cargar();
        this.cerrarModal();
      } catch (e){
        this.error = e.response?.data?.error || 'Error guardando';
      } finally { this.guardando=false; }
    },
    async borrarSeleccionados(){
      if (!confirm('Eliminar usuarios seleccionados?')) return;
      const token = localStorage.getItem('admin_token');
      for (const id of this.seleccionados){
        try { await axios.delete('http://127.0.0.1:8000/api/admin/usuarios/'+id, { headers:{ Authorization:'Bearer '+token }}); } catch {}
      }
      this.seleccionados=[]; this.cargar();
    },
    goPerfil(){ this.$router.push('/admin/perfil'); },
    logout(){ logoutAdmin(); localStorage.removeItem('admin_token'); this.$router.push('/admin/login'); },
    formatearFecha(f){ return f ? new Date(f).toLocaleDateString('es-MX') : ''; }
  }
}
</script>

<style scoped>
.admin-layout { min-height:100vh; display:flex; flex-direction:column; background:#f4f6f9; }
.topbar { position:sticky; top:0; z-index:50; display:flex; align-items:center; justify-content:space-between; padding:6px 16px; background:#1f2532; color:#fff; box-shadow:0 2px 4px rgba(0,0,0,.15); }
.topbar .left{ display:flex; align-items:center; gap:14px; }
.logo-mini { background:linear-gradient(135deg,#7c3aed,#3b82f6); width:42px; height:42px; border-radius:12px; display:flex; align-items:center; justify-content:center; font-weight:700; letter-spacing:.5px; }
.quick-nav a { color:#d0d6e2; text-decoration:none; padding:8px 14px; border-radius:8px; font-size:.85rem; font-weight:600; transition:background .25s; }
.quick-nav a.active, .quick-nav a:hover { background:rgba(255,255,255,.12); color:#fff; }
.topbar .center { font-size:.75rem; font-weight:600; letter-spacing:.5px; text-transform:uppercase; opacity:.85; }
.topbar .right { display:flex; align-items:center; gap:10px; }
.icon-btn { background:none; border:none; color:#e2e8f0; width:38px; height:38px; display:flex; align-items:center; justify-content:center; border-radius:10px; cursor:pointer; transition:background .25s, color .25s; }
.icon-btn:hover { background:rgba(255,255,255,.1); color:#fff; }
.avatar { width:40px; height:40px; border-radius:50%; background:linear-gradient(135deg,#6366f1,#8b5cf6); display:flex; align-items:center; justify-content:center; font-weight:700; box-shadow:0 0 0 2px rgba(255,255,255,.12); cursor:pointer; }
.workspace { width:100%; max-width:1400px; margin:18px auto 40px; padding:0 22px; display:flex; flex-direction:column; gap:22px; }
.card { background:#fff; border-radius:22px; padding:20px 22px; box-shadow:0 4px 14px -4px rgba(15,23,42,.15); position:relative; }
.toolbar { display:flex; flex-wrap:wrap; gap:16px; align-items:center; justify-content:space-between; }
.actions { display:flex; gap:12px; }
.btn { border:none; border-radius:14px; font-size:.8rem; font-weight:600; display:inline-flex; align-items:center; gap:6px; padding:10px 18px; cursor:pointer; letter-spacing:.3px; transition:all .25s; }
.btn.primary { background:linear-gradient(135deg,#7c3aed,#3b82f6); color:#fff; box-shadow:0 4px 12px -2px rgba(99,102,241,.35); }
.btn.primary:hover { filter:brightness(1.08); transform:translateY(-2px); }
.btn.outline { background:#fff; color:#475569; border:2px solid #e2e8f0; }
.btn.outline:hover { background:#f1f5f9; }
.btn:disabled { opacity:.5; cursor:not-allowed; }
.filters { display:flex; gap:10px; align-items:center; margin-left:auto; }
.filters input, .filters select { background:#f1f5f9; border:2px solid #e2e8f0; padding:8px 12px; border-radius:10px; font-size:.8rem; font-weight:500; outline:none; transition:border .25s, background .25s; }
.filters input:focus, .filters select:focus { border-color:#7c3aed; background:#fff; }
.grid-container { min-height:340px; }
.loading-state { display:flex; flex-direction:column; gap:14px; align-items:center; justify-content:center; padding:60px 0; color:#475569; font-weight:600; }
.grid-users { display:grid; gap:18px; grid-template-columns:repeat(auto-fill,minmax(250px,1fr)); }
.user-card { background:#ffffff; border:2px solid #eef0f5; border-radius:20px; padding:16px 18px 14px; display:flex; flex-direction:column; gap:10px; cursor:pointer; transition:all .3s ease; position:relative; }
.user-card:hover { border-color:#cdd4e3; box-shadow:0 6px 18px -6px rgba(15,23,42,.25); transform:translateY(-3px); }
.user-card.selected { border-color:#7c3aed; box-shadow:0 0 0 3px rgba(124,58,237,.25); }
.user-head { display:flex; align-items:center; gap:12px; }
.circle { width:48px; height:48px; border-radius:16px; display:flex; align-items:center; justify-content:center; font-weight:700; font-size:1.1rem; color:#fff; background:linear-gradient(135deg,#64748b,#475569); box-shadow:0 4px 10px -3px rgba(15,23,42,.35); }
.circle.usuario { background:linear-gradient(135deg,#6366f1,#3b82f6); }
.circle.admin { background:linear-gradient(135deg,#f59e0b,#d97706); }
.user-head h5 { font-size:.95rem; font-weight:700; margin:0; letter-spacing:.3px; color:#1e293b; }
.user-head small { font-size:.65rem; font-weight:600; color:#64748b; letter-spacing:.25px; }
.mini-btn { background:none; border:none; color:#6366f1; margin-left:auto; font-size:1rem; padding:6px 10px; border-radius:10px; cursor:pointer; transition:background .25s, color .25s; }
.mini-btn:hover { background:rgba(99,102,241,.12); color:#4f46e5; }
.meta { display:flex; align-items:center; justify-content:space-between; font-size:.65rem; margin-top:2px; }
.tag { padding:4px 10px; border-radius:20px; font-weight:600; letter-spacing:.4px; text-transform:uppercase; background:#e2e8f0; color:#334155; }
.tag.usuario { background:#ede9fe; color:#5b21b6; }
.tag.admin { background:#fff4e6; color:#b45309; }
.date { font-weight:600; color:#64748b; }
.modal-overlay { position:fixed; inset:0; background:rgba(15,23,42,.55); display:flex; align-items:center; justify-content:center; z-index:100; backdrop-filter:blur(3px); }
.modal { background:#fff; width:100%; max-width:640px; padding:28px 30px 32px; border-radius:28px; position:relative; box-shadow:0 20px 40px -10px rgba(15,23,42,.45); animation:fadeIn .4s ease; }
.modal h4 { font-weight:700; letter-spacing:.5px; background:linear-gradient(135deg,#7c3aed,#3b82f6); -webkit-background-clip:text; background-clip:text; color:transparent; }
.modal label { font-size:.7rem; font-weight:700; text-transform:uppercase; letter-spacing:1px; color:#475569; display:block; margin-bottom:6px; }
.modal input { width:100%; background:#f1f5f9; border:2px solid #e2e8f0; padding:10px 14px; border-radius:14px; font-size:.8rem; font-weight:600; outline:none; transition:border .25s, background .25s; }
.modal input:focus { background:#fff; border-color:#7c3aed; }
.row { display:flex; gap:16px; margin-bottom:14px; }
.col { flex:1; }
.pwd-group { display:flex; align-items:stretch; position:relative; }
.pwd-group input { border-top-right-radius:0; border-bottom-right-radius:0; }
.pwd-group button { border:none; background:#e2e8f0; padding:0 14px; cursor:pointer; border-top-right-radius:14px; border-bottom-right-radius:14px; display:flex; align-items:center; color:#475569; transition:background .25s, color .25s; }
.pwd-group button:hover { background:#cbd5e1; color:#1e293b; }
.modal-actions { margin-top:10px; display:flex; justify-content:flex-end; gap:12px; }
.error { color:#dc2626; font-size:.7rem; font-weight:600; letter-spacing:.3px; margin-top:4px; }
@keyframes fadeIn { from { opacity:0; transform:translateY(20px);} to {opacity:1; transform:translateY(0);} }
@media (max-width:640px){ .row { flex-direction:column; } .quick-nav { display:none; } .workspace { padding:0 14px; } }
</style>
