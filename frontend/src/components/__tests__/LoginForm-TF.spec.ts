/**
 * Test fonctionnel LoginForm
 * - Teste le submit, loading, gestion erreurs, redirection.
 */
import { describe, it, expect, vi, beforeEach } from 'vitest'
import { mount } from '@vue/test-utils'
import LoginForm from '@/components/form/LoginForm.vue'

// Mocks globaux
const mockLogin = vi.fn()
const mockResetError = vi.fn()
const mockRouter = { push: vi.fn() }
const mockToast = { error: vi.fn(), success: vi.fn() }

// Variables pour simuler l'état du store entre chaque test
let mockUser: any = null
let mockError = ''

// IMPORTANT: Mocker AVANT l'import du composant !
vi.mock('@/stores/authStore', () => ({
    useAuthStore: () => ({
        login: mockLogin,
        resetError: mockResetError,
        get user() { return mockUser },
        get error() { return mockError },
    }),
}))
vi.mock('vue-router', () => ({
    useRouter: () => mockRouter
}))
vi.mock('vue-toastification', () => ({
    useToast: () => mockToast
}))

describe('LoginForm', () => {
    beforeEach(() => {
        // Reset tous les mocks et états à chaque test
        vi.clearAllMocks()
        mockLogin.mockReset()
        mockRouter.push.mockReset()
        mockToast.error.mockReset()
        mockToast.success.mockReset()
        mockResetError.mockReset()
        mockUser = null
        mockError = ''
    })

    it('affiche le loader pendant la soumission', async () => {
        mockLogin.mockImplementationOnce(() => new Promise(resolve => setTimeout(resolve, 100)))
        const wrapper = mount(LoginForm, {
            global: { stubs: { RouterLink: { template: '<a />' } } }
        })
        const vm: any = wrapper.vm
        vm.email = 'user@user.com'
        vm.password = 'password'
        await wrapper.vm.$nextTick()

        // Avant submit, bouton actif
        expect(wrapper.find('button[type="submit"]').attributes('disabled')).toBeUndefined()

        // Submit
        wrapper.find('form').trigger('submit.prevent')
        await wrapper.vm.$nextTick()
        // Pendant loading, bouton désactivé
        expect(wrapper.find('button[type="submit"]').attributes('disabled')).toBeDefined()
    })

    it('redirige vers /dashboard si connexion OK', async () => {
        // Prépare la promesse qui simule login
        mockLogin.mockImplementationOnce(async () => {
            mockUser = { id: 1, email: 'user@user.com' } // simule connexion réussie
            mockError = ''
        })

        const wrapper = mount(LoginForm, {
            global: { stubs: { RouterLink: { template: '<a />' } } }
        })
        const vm: any = wrapper.vm
        vm.email = 'user@user.com'
        vm.password = 'password'
        await wrapper.vm.$nextTick()

        await wrapper.find('form').trigger('submit.prevent')
        await wrapper.vm.$nextTick()

        // La redirection est appelée
        expect(mockRouter.push).toHaveBeenCalledWith('/dashboard')
        // Pas de toast erreur
        expect(mockToast.error).not.toHaveBeenCalled()
    })

    it('affiche un toast en cas d’erreur', async () => {
        // Simule une erreur
        mockLogin.mockImplementationOnce(async () => {
            mockUser = null
            mockError = 'Identifiants invalides.'
        })

        const wrapper = mount(LoginForm, {
            global: { stubs: { RouterLink: { template: '<a />' } } }
        })
        const vm: any = wrapper.vm
        vm.email = 'user@user.com'
        vm.password = 'password'
        await wrapper.vm.$nextTick()

        await wrapper.find('form').trigger('submit.prevent')
        await wrapper.vm.$nextTick()

        expect(mockToast.error).toHaveBeenCalledWith('Identifiants invalides.')
        expect(mockRouter.push).not.toHaveBeenCalled()
    })
})
