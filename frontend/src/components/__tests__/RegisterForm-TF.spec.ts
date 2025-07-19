/**
 * Test d'intégration du RegisterForm
 * - Vérifie la validation, l'affichage d'erreur, l'appel au store, et la navigation si succès.
 */
import { describe, it, expect, vi, beforeEach } from 'vitest'
import { mount } from '@vue/test-utils'
import RegisterForm from '@/components/form/RegisterForm.vue'

// --- Mocks globaux ---
// Mock de la méthode registerUser du store, de la fonction push du router, et du toastification.
const mockRegister = vi.fn()
const mockRouter = { push: vi.fn() }
const mockToast = { success: vi.fn(), error: vi.fn() }

// Les mocks DOIVENT être déclarés AVANT l'import des composants qui les utilisent
vi.mock('@/stores/authStore', () => ({
    useAuthStore: () => ({
        registerUser: mockRegister,
        error: '',
        loading: false,
        resetError: vi.fn()
    })
}))
// AvatarInput est mocké pour éviter les problèmes d'upload dans un test
vi.mock('@/components/form/inputs/AvatarInput.vue', () => ({
    default: {
        name: 'AvatarInput',
        template: '<input data-testid="avatar-input" @input="$emit(\'update:file\', $event.target.files[0])" />'
    }
}))
// Mock du router
vi.mock('vue-router', () => ({
    useRouter: () => mockRouter
}))
// Mock du système de toast
vi.mock('vue-toastification', () => ({
    useToast: () => mockToast
}))

describe('RegisterForm', () => {
    // Avant chaque test, reset tous les mocks pour garantir l'isolation des tests
    beforeEach(() => {
        vi.clearAllMocks()
        mockRegister.mockReset()
        mockRouter.push.mockReset()
        mockToast.success.mockReset()
        mockToast.error.mockReset()
    })

    it('affiche une erreur si le formulaire est incomplet', async () => {
        // On monte le composant avec un stub pour RouterLink (sinon warning)
        const wrapper = mount(RegisterForm, {
            global: {
                stubs: {
                    RouterLink: { template: '<a />' }
                }
            }
        })
        // On soumet le formulaire vide
        await wrapper.find('form').trigger('submit.prevent')
        // On vérifie que le toast d'erreur a bien été appelé avec le bon message
        expect(mockToast.error).toHaveBeenCalledWith('Merci de remplir tous les champs correctement.')
    })

    it('appelle registerUser et redirige si succès', async () => {
        // On simule un succès du registerUser
        mockRegister.mockResolvedValueOnce({})
        const wrapper = mount(RegisterForm, {
            global: {
                stubs: {
                    RouterLink: { template: '<a />' }
                }
            }
        })

        // On patch les champs du formulaire côté instance
        // (c'est plus rapide/fiable pour un test fonctionnel, ici on court-circuite les validations enfant)
        const vm: any = wrapper.vm
        vm.email = 'john@doe.com'
        vm.pseudo = 'Johnny'
        vm.password = 'PasswordValid123!'
        vm.isEmailValid = true
        vm.isPseudoValid = true
        vm.isAvatarValid = true
        vm.isPasswordValid = true
        await wrapper.vm.$nextTick()

        // Soumission du formulaire
        await wrapper.find('form').trigger('submit.prevent')

        // Vérifie que registerUser a bien été appelé (donc le store reçoit la requête)
        expect(mockRegister).toHaveBeenCalled()
        // Vérifie que la redirection (push) a bien été effectuée vers /login
        expect(mockRouter.push).toHaveBeenCalledWith('/login')
    })

    it('affiche les messages d’erreur retournés par le backend', async () => {
        // On simule un rejet (erreur backend avec violations sur les champs)
        mockRegister.mockRejectedValueOnce({
            response: {
                data: {
                    violations: [
                        { propertyPath: 'email', message: 'Email déjà utilisé.' },
                        { propertyPath: 'pseudo', message: 'Pseudo trop court.' }
                    ]
                }
            }
        })

        const wrapper = mount(RegisterForm, {
            global: {
                stubs: {
                    RouterLink: { template: '<a />' }
                }
            }
        })

        // On patch les champs côté parent comme si tout était valide
        const vm: any = wrapper.vm
        vm.email = 'john@doe.com'
        vm.pseudo = 'Johnny'
        vm.password = 'PasswordValid123!'
        vm.isEmailValid = true
        vm.isPseudoValid = true
        vm.isAvatarValid = true
        vm.isPasswordValid = true
        await wrapper.vm.$nextTick()

        // On soumet le formulaire (ça va déclencher le rejet et afficher les erreurs)
        await wrapper.find('form').trigger('submit.prevent')
        await wrapper.vm.$nextTick() // attend l'update DOM après catch

        // On vérifie que les messages d'erreur sont bien visibles dans le HTML
        expect(wrapper.html()).toContain('Email déjà utilisé.')
        expect(wrapper.html()).toContain('Pseudo trop court.')
    })
})

