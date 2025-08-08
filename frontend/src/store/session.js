import { ref } from 'vue';

export const usuario = ref(
  localStorage.getItem('usuario')
    ? JSON.parse(localStorage.getItem('usuario'))
    : null
);

export function loginUsuario(user) {
  usuario.value = user;
  localStorage.setItem('usuario', JSON.stringify(user));
}

export function logoutUsuario() {
  usuario.value = null;
  localStorage.removeItem('usuario');
}