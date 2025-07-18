import axios from '@/modules/axios'

let socket: WebSocket | null = null
let pingInterval: ReturnType<typeof setInterval> | null = null
const listeners: ((data: any) => void)[] = []
let shouldReconnect = true

/**
 * Initialise la connexion WebSocket en récupérant d'abord un token temporaire
 */
export async function connectWebSocket(): Promise<WebSocket | null> {
    if (socket && socket.readyState !== WebSocket.CLOSED) {
        console.warn('🟡 WebSocket déjà connecté')
        return socket
    }

    try {
        const res = await axios.get('/ws/token', {
            withCredentials: true
        })

        const token = res.data.token
        console.log('🎫 Token WebSocket récupéré :', token)

        socket = new WebSocket('wss://localhost/ws/')

        socket.onopen = () => {
            console.log('✅ WebSocket ouvert')
            socket?.send(JSON.stringify({
                type: 'authenticate',
                token
            }))

            // PING toutes les 30 sec
            pingInterval = setInterval(() => {
                if (socket?.readyState === WebSocket.OPEN) {
                    console.log('📡 Ping envoyé au serveur')
                    socket.send(JSON.stringify({ type: 'ping' }))
                }
            }, 30_000)
        }

        socket.onmessage = (event) => {
            try {
                const data = JSON.parse(event.data)
                console.log('📩 Message WebSocket :', data)
                listeners.forEach((cb) => cb(data))
            } catch (e) {
                console.error('❌ Erreur de parsing du message WebSocket :', e)
            }
        }

        socket.onerror = (err) => {
            console.error('❌ WebSocket erreur', err)
        }

        socket.onclose = () => {
            console.log('🔌 WebSocket fermé')
            socket = null

            // Stop le ping
            if (pingInterval) {
                clearInterval(pingInterval)
                pingInterval = null
            }

            // Ne reconnecte que si voulu
            if (shouldReconnect) {
                setTimeout(() => {
                    console.log('🔁 Tentative de reconnexion WebSocket...')
                    connectWebSocket()
                }, 1000)
            } else {
                console.log('🚫 Pas de reconnexion WebSocket (logout volontaire)')
            }
        }

        return socket
    } catch (err) {
        console.error('❌ Échec de récupération du token ou de la connexion WebSocket', err)
        return null
    }
}

/**
 * Envoie un message via le WebSocket s’il est ouvert
 */
export function sendWebSocketMessage(message: any) {
    if (socket?.readyState === WebSocket.OPEN) {
        socket.send(JSON.stringify(message))
    } else {
        console.warn('⚠️ WebSocket non connecté, tentative de reconnexion...')
        connectWebSocket().then(() => {
            if (socket?.readyState === WebSocket.OPEN) {
                socket.send(JSON.stringify(message))
            }
        })
    }
}

/**
 * Abonne un listener pour recevoir les messages WebSocket
 */
export function addWebSocketListener(callback: (data: any) => void) {
    listeners.push(callback)
}

/**
 * Supprime un listener précédemment ajouté
 */
export function removeWebSocketListener(callback: (data: any) => void) {
    const index = listeners.indexOf(callback)
    if (index !== -1) {
        listeners.splice(index, 1)
    }
}

export function disconnectWebSocket() {
    shouldReconnect = false
    if (socket) {
        socket.close()
    }
}
