import { createRouter, createWebHistory } from 'vue-router'
import { useAuthStore } from '@/stores/authStore'
// Déclaration des routes
const routes = [
  {
    path: '/register',
    name: 'register',
    component: () => import('@/views/RegisterView.vue'),
    meta: {
      requiresGuest: true,
      title: 'Inscription - Roomies'
    }
  },
  {
    path: '/verified-email',
    name: 'verified-email',
    component: () => import('@/views/VerifiedEmailView.vue'),
    meta: {
      requiresGuest: true,
      title: 'Vérification email - Roomies'
    }
  },
  {
    path: '/resend-confirmation-email',
    name: 'resend-confirmation-email',
    component: () => import('@/views/ResendConfirmationEmailView.vue'),
    meta: {
      requiresGuest: true,
      title: 'Renvoyer l’email de confirmation - Roomies'
    }
  },
  {
    path: '/login',
    name: 'login',
    component: () => import('@/views/LoginView.vue'),
    meta: {
      requiresGuest: true,
      title: 'Connexion - Roomies'
    }
  },
  {
    path: '/forgot-password',
    name: 'forgot-password',
    component: () => import('@/views/ForgotPasswordView.vue'),
    meta: {
      requiresGuest: true,
      title: 'Mot de passe oublié - Roomies'
    }
  },
  {
    path: '/reset-password',
    name: 'reset-password',
    component: () => import('@/views/ResetPasswordView.vue'),
    meta: {
      requiresGuest: true,
      title: 'Réinitialiser le mot de passe - Roomies'
    }
  },
  {
    path: '/',
    name: 'home.public',
    component: () => import('@/views/HomePublicView.vue'),
    meta: {
      requiresGuest: true,
      title: 'Accueil - Roomies'

    }
  },
  {
    path: '/dashboard',
    component: () => import('@/layouts/AuthenticatedLayout.vue'),
    meta: { requiresAuth: true },
    children: [
      {
        path: '',
        name: 'friends.list',
        component: () => import('@/views/FriendsView.vue'),
        meta: {
          title: 'Mes amis - Roomies'
        }
      },
      {
        path: 'chat/:roomId',
        name: 'private.chat',
        component: () => import('@/views/PrivateChatView.vue'),
        meta: {
          title: 'Conversation - Roomies'
        }
      }
    ]
  },
  {
    path: '/global/chat',
    component: () => import('@/layouts/AuthenticatedLayout.vue'),
    meta: { requiresAuth: true },
    children: [
      {
        path: '',
        name: 'global.chat',
        component: () => import('@/views/GlobalChatView.vue'),
        meta: {
          title: 'Chat global - Roomies'
        }
      }
    ]
  },
  {
    path: '/games',
    component: () => import('@/layouts/AuthenticatedLayout.vue'),
    meta: { requiresAuth: true },
    children: [
      {
        path: '',
        name: 'games.list',
        component: () => import('@/views/LobbyView.vue'),
        meta: { title: 'Jeux en ligne - Roomies' }
      },
      {
        path: '/room/:roomId',
        name: 'games.room',
        component: () => import('@/views/GamesView.vue'),
        meta: { title: 'Jeux en ligne - Roomies' }
      },
    ]
  },
  {
    path: '/serveur',
    component: () => import('@/layouts/AuthenticatedLayout.vue'),
    meta: { requiresAuth: true },
    children: [
      {
        path: 'chat/:roomId',
        name: 'serveurChat.private',
        component: () => import('@/views/PrivateServeurChatView.vue'),
        meta: {
          title: 'Conversation de groupe - Roomies'
        }
      }
    ]
  },
  {
    path: '/settings',
    component: () => import('@/layouts/AuthenticatedLayout.vue'),
    meta: { requiresAuth: true },
    children: [
      {
        path: '',
        name: 'settings',
        component: () => import('@/views/ProfileSettingsView.vue'),
        meta: {
          title: 'Paramètres du profil - Roomies'
        }
      }
    ]
  },
  {
    path: '/:pathMatch(.*)*',
    redirect: '/'
  }

]

// Création du router
export const router = createRouter({
  history: createWebHistory(),
  routes
})

router.afterEach((to) => {
  const defaultTitle = 'Roomies'
  const title = to.meta.title as string
  document.title = title || defaultTitle
})

// Protection des routes
router.beforeEach(async (to, from, next) => {
  const auth = useAuthStore()

  // Si l'utilisateur n'est pas encore récupéré, on le fait
  if ((to.meta.requiresAuth || to.meta.requiresGuest) && !auth.user && !auth.userFetched) {
    const refreshable = !!to.meta.requiresAuth
    await auth.fetchUser(refreshable)
  }


  // Route réservée aux invités
  if (to.meta.requiresGuest && auth.user) {
    return next('/dashboard')
  }

  // Route protégée
  if (to.meta.requiresAuth && !auth.user) {
    return next('/login')
  }

  return next()
})

