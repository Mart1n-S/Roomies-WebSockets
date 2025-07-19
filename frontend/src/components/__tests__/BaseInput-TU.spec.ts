/**
 * Tests unitaires du composant BaseInput.vue
 * Vérifie le rendu du label, du placeholder, l'émission d'événements,
 * l'affichage d'une erreur, la gestion du slot right-icon et l'émission de l'event icon-click.
 */
import { describe, it, expect } from 'vitest'
import { mount } from '@vue/test-utils'
import BaseInput from '@/components/base/BaseInput.vue'

describe('BaseInput', () => {
    /** Vérifie que le label et le placeholder sont bien rendus */
    it('affiche le label et le placeholder', () => {
        const wrapper = mount(BaseInput, {
            props: { name: 'email', label: 'Email', placeholder: 'Entrer un mail', modelValue: '' }
        })
        expect(wrapper.text()).toContain('Email')
        expect(wrapper.find('input').attributes('placeholder')).toBe('Entrer un mail')
    })

    /** Vérifie que l'event update:modelValue est bien émis lors de la saisie */
    it('émet l’évènement update:modelValue lors de la saisie', async () => {
        const wrapper = mount(BaseInput, {
            props: { name: 'test', modelValue: '' }
        })
        await wrapper.find('input').setValue('hello')
        expect(wrapper.emitted()['update:modelValue']).toBeTruthy()
        expect(wrapper.emitted()['update:modelValue'][0]).toEqual(['hello'])
    })

    /** Vérifie que le message d'erreur est affiché si la prop error est renseignée */
    it('affiche le message d’erreur', () => {
        const wrapper = mount(BaseInput, {
            props: { name: 'test', modelValue: '', error: 'Champ requis' }
        })
        expect(wrapper.text()).toContain('Champ requis')
    })

    /** Vérifie que le slot right-icon s'affiche bien */
    it('affiche le slot right-icon', () => {
        const wrapper = mount(BaseInput, {
            props: { name: 'test', modelValue: '' },
            slots: { 'right-icon': '<span class="icon">X</span>' }
        })
        expect(wrapper.html()).toContain('icon')
    })

    /** Vérifie que l’évènement icon-click est émis lors d’un clic sur l’icône */
    it('émet l’évènement icon-click au clic sur l’icône', async () => {
        const wrapper = mount(BaseInput, {
            props: { name: 'test', modelValue: '' },
            slots: { 'right-icon': '<span class="icon">X</span>' }
        })
        await wrapper.find('.icon').trigger('click')
        expect(wrapper.emitted()['icon-click']).toBeTruthy()
    })
})
