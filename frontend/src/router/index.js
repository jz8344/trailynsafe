  import { auth } from '../middleware'; 
import { createRouter, createWebHistory } from 'vue-router';
import Home from '../views/Home.vue';
import axios from 'axios';

const routes = [
  {
    path: '/',
    name: 'Home',
    component: Home
  },
  {
    path: '/about',
    name: 'sobreNosotros',
    component: () => import('../views/About.vue') 
  },
  {
    path: '/rutas',
    name: 'Rutas',
    component: () => import('../views/Rutas.vue')
  },
  {
    path: '/aliados',
    name: 'Aliados',
    component: () => import('../views/Aliados.vue')
  },
  {
    path: '/login',
    name: 'Login',
    component: () => import('../views/Login.vue')
  },
  {
    path: '/register',
    name: 'Register',
    component: () => import('../views/Register.vue')
  },
   {
     path: '/editar-perfil',
     name: 'EditarPerfil',
     meta: {
       middleware: [auth]
     },
     component: () => import('../views/EditarPerfil.vue'),
   },  
   {
     path: '/multas',
     name: 'Multas',
     meta: {
       middleware: [auth]
     },
     component: () => import('../views/Multas.vue')
   },
   {
     path: '/notificaciones',
     name: 'Notificaciones',
     meta: {
       middleware: [auth]
     },
       component: () => import('@/views/Notificaciones.vue')
   },
   {
     path: '/cambiar-email',
     name: 'ChangeEmail',
     component: () => import('../views/ChangeEmail.vue'),
     meta: { middleware: [auth] }
   },
   {
     path: '/cambiar-contrasena',
     name: 'ChangePass',
     component: () => import('../views/ChangePass.vue'),
     meta: { middleware: [auth] }
   },
   {
     path: '/cambiar-password',
     name: 'CambiarContrasena',
     component: () => import('../views/CambiarContrasena.vue'),
     meta: { middleware: [auth] }
   },
   {
     path: '/recuperar-password',
     name: 'RecuperarPassword',
     component: () => import('../components/RecuperarPassword.vue')
   }
];

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes
});

router.beforeEach(async (to, from, next) => {
  const requiresAuth = to.meta.middleware && to.meta.middleware.some(mw => mw.name === 'auth');
  if (requiresAuth) {
    const token = localStorage.getItem('token');
    if (!token) {
      next('/login');
      return;
    }
    try {
      await axios.get('http://127.0.0.1:8000/api/sesion', {
        headers: { Authorization: 'Bearer ' + token }
      });
      next();
    } catch (e) {
      localStorage.removeItem('token');
      next('/login');
    }
  } else {
    next();
  }
});

export default router;
