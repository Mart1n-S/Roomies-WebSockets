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
    name: 'home.private',
    component: () => import('@/views/HomePrivateView.vue'),
    meta: {
      requiresAuth: true,
      title: 'Tableau de bord - Roomies'
    }
  },

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

  // On ne fetch que si :
  //  - la route a besoin de connaître l'utilisateur
  //  - ET qu'on n'a pas déjà tenté de le récupérer
  //  - ET qu'on ne l’a pas déjà dans l’état
  if ((to.meta.requiresAuth || to.meta.requiresGuest) && !auth.user && !auth.userFetched) {
    await auth.fetchUser()
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

