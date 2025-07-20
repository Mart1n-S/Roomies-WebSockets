import 'primeicons/primeicons.css'
import Toast from 'vue-toastification'
import 'vue-toastification/dist/index.css'
import './assets/main.css'


import { createApp } from 'vue'
import { createPinia } from 'pinia'

import App from './App.vue'
import { router } from './router'

const app = createApp(App)
app.use(Toast, {
    timeout: 5000,
    position: 'top-right',
    closeOnClick: true,
    pauseOnHover: true,
    draggable: true,
    hideProgressBar: false
})
app.use(createPinia())
app.use(router)

app.mount('#app')

if ('serviceWorker' in navigator) {
    window.addEventListener('load', () => {
        navigator.serviceWorker.register('/sw.js')
    })
}