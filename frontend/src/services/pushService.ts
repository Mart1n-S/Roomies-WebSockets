import axios from '@/modules/axios'
import type { User } from '@/models/User'
// Récupère la variable d’environnement VITE_VAPID_PUBLIC_KEY
const VAPID_PUBLIC_KEY = import.meta.env.VITE_VAPID_PUBLIC_KEY;


/**
 * Demande à l'utilisateur la permission d'envoyer des notifications push.
 */
export async function askPermission(): Promise<NotificationPermission> {
    return await Notification.requestPermission();
}

/**
 * Convertit une chaîne VAPID base64 en Uint8Array.
 */
function urlBase64ToUint8Array(base64String: string): Uint8Array {
    const padding = '='.repeat((4 - base64String.length % 4));
    const base64 = (base64String + padding).replace(/-/g, '+').replace(/_/g, '/');
    const rawData = window.atob(base64)
    const outputArray = new Uint8Array(rawData.length)
    for (let i = 0; i < rawData.length; ++i) {
        outputArray[i] = rawData.charCodeAt(i)
    }
    return outputArray
}

/**
 * Abonne l'utilisateur aux notifications push via le Service Worker.
 */
export async function subscribeUserToPush(): Promise<PushSubscription> {
    const registration = await navigator.serviceWorker.ready
    const subscribeOptions = {
        userVisibleOnly: true,
        applicationServerKey: urlBase64ToUint8Array(VAPID_PUBLIC_KEY)
    }
    return await registration.pushManager.subscribe(subscribeOptions)
}

/**
 * Envoie l'abonnement push au backend sous la forme attendue par l'API.
 */
export async function sendSubscriptionToBackEnd(
    sub: PushSubscription | null,
    enabled: boolean
): Promise<User> {
    let payload: any = { pushNotificationsEnabled: enabled }
    if (sub) {
        const { endpoint, keys } = sub.toJSON()
        if (!keys?.p256dh || !keys?.auth) {
            throw new Error('La clé de souscription push est incomplète ou absente.')
        }
        payload = {
            ...payload,
            endpoint,
            p256dh: keys.p256dh,
            auth: keys.auth
        }
    }
    const response = await axios.post('/subscribe', payload)
    return response.data as User
}



