/**
 * Tests unitaires du composant BaseTextArea.vue
 * Vérifie le rendu du label, du placeholder, l'émission de l'événement,
 * et l'affichage d'un message d'erreur.
 */
import { describe, it, expect } from 'vitest'
import { mount } from '@vue/test-utils'
import BaseTextArea from '@/components/base/BaseTextArea.vue'

describe('BaseTextArea', () => {
    /** Vérifie que le label et le placeholder sont bien affichés */
    it('affiche le label et le placeholder', () => {
        const wrapper = mount(BaseTextArea, {
            props: { name: 'message', label: 'Message', placeholder: 'Tape ici...', modelValue: '' }
        })
        expect(wrapper.text()).toContain('Message')
        expect(wrapper.find('textarea').attributes('placeholder')).toBe('Tape ici...')
    })

    /** Vérifie que l’évènement update:modelValue est bien émis lors de la saisie */
    it('émet l’évènement update:modelValue lors de la saisie', async () => {
        const wrapper = mount(BaseTextArea, {
            props: { name: 'msg', modelValue: '' }
        })
        await wrapper.find('textarea').setValue('coucou')
        expect(wrapper.emitted()['update:modelValue']).toBeTruthy()
        expect(wrapper.emitted()['update:modelValue'][0]).toEqual(['coucou'])
    })

    /** Vérifie que le message d’erreur s’affiche quand la prop error est renseignée */
    it('affiche le message d’erreur', () => {
        const wrapper = mount(BaseTextArea, {
            props: { name: 'msg', modelValue: '', error: 'Erreur' }
        })
        expect(wrapper.text()).toContain('Erreur')
    })
})
