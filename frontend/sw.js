// Service worker Roomies : installation immédiate + gestion notifications push

self.addEventListener('install', () => self.skipWaiting())
self.addEventListener('activate', () => self.clients.claim())

// Notifications push (robuste à tout type de payload)
self.addEventListener('push', (event) => {
  let data = {}
  try {
    // Essaye de parser en JSON
    data = event.data?.json() || {}
  } catch (e) {
    // Si c'est juste une string (par exemple "Test push message from DevTools.")
    data = {
      title: 'Roomies',
      body: event.data?.text() || '',
    }
  }
  const title = data.title || 'Roomies'
  const options = {
    body: data.body || '',
    icon: data.icon || '/roomies-icon-192.png',
    badge: data.badge || '/roomies-icon-192.png',
    data: data.url || '/',
  }
  event.waitUntil(self.registration.showNotification(title, options))
})

self.addEventListener('notificationclick', (event) => {
  event.notification.close()
  event.waitUntil(
    clients.matchAll({ type: 'window', includeUncontrolled: true }).then((clientList) => {
      for (const client of clientList) {
        if (client.url === event.notification.data && 'focus' in client) {
          return client.focus()
        }
      }
      if (clients.openWindow) {
        return clients.openWindow(event.notification.data)
      }
    }),
  )
})
