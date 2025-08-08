import { createApp } from 'vue'
import App from './App.vue'
import router from './router'
import { iniciarValidacionPeriodica, iniciarValidacionVisibilidad } from './middleware/authMiddleware.js'

const app = createApp(App)
app.use(router)
app.mount('#app')

// Iniciar validación agresiva de sesión
iniciarValidacionPeriodica()
iniciarValidacionVisibilidad()