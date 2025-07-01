// src/modules/axios.ts
import axios from 'axios'

const axiosInstance = axios.create({
    baseURL: import.meta.env.VITE_API_URL,
    withCredentials: true, // IMPORTANT pour envoyer les cookies JWT
    headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json'
    }
})

export default axiosInstance
