/**
 * Tests unitaires pour le composant PasswordInput.vue.
 * Ces tests vérifient le rendu des critères, la gestion des erreurs,
 * l’émission des événements et la confirmation de mot de passe.
 */

import { describe, it, expect } from 'vitest'
import { mount } from '@vue/test-utils'
import PasswordInput from '@/components/form/inputs/PasswordInput.vue'
import BaseInput from '@/components/base/BaseInput.vue'

describe('PasswordInput', () => {
    /** Vérifie l’affichage des critères si showConditions est true */
    it('affiche correctement les critères si showConditions vaut true', () => {
        const wrapper = mount(PasswordInput, {
            props: {
                name: 'password',
                modelValue: '',
                showConditions: true
            },
            global: {
                components: { BaseInput }
            }
        })

        expect(wrapper.html()).toContain('Au moins 16 caractères')
        expect(wrapper.html()).toContain('Une minuscule')
        expect(wrapper.html()).toContain('Une majuscule')
        expect(wrapper.html()).toContain('Un chiffre')
        expect(wrapper.html()).toContain('Un caractère spécial')
    })

    /** Vérifie l’affichage d’une erreur si le mot de passe ne respecte pas les critères au blur */
    it('affiche une erreur si le mot de passe ne respecte pas les critères au blur', async () => {
        const wrapper = mount(PasswordInput, {
            props: {
                name: 'password',
                modelValue: 'short',
                showConditions: true
            },
            global: {
                components: { BaseInput }
            }
        })

        const input = wrapper.find('input[name="password"]')
        await input.setValue('short')
        await input.trigger('blur')

        expect(wrapper.html()).toContain('Le mot de passe ne respecte pas les critères.')
    })

    /** Vérifie que l’événement update:modelValue est bien émis lors de la saisie */
    it('émet bien l’événement update:modelValue lors de la saisie', async () => {
        const wrapper = mount(PasswordInput, {
            props: { name: 'password', modelValue: '' },
            global: { components: { BaseInput } }
        })

        const input = wrapper.find('input[name="password"]')
        await input.setValue('SuperPassword!123')
        expect(wrapper.emitted('update:modelValue')).toBeTruthy()
        expect(wrapper.emitted('update:modelValue')?.[0][0]).toBe('SuperPassword!123')
    })

    /** Vérifie que valid=true est émis si le mot de passe respecte tous les critères */
    it('émet bien valid=true si le mot de passe respecte tous les critères', async () => {
        const wrapper = mount(PasswordInput, {
            props: { name: 'password', modelValue: '', showConditions: true },
            global: { components: { BaseInput } }
        })

        const input = wrapper.find('input[name="password"]')
        const value = 'ValidPassword!1234'
        await input.setValue(value)
        await wrapper.setProps({ modelValue: value })
        await input.trigger('blur')

        expect(wrapper.emitted('valid')).toBeTruthy()
        expect(wrapper.emitted('valid')?.slice(-1)[0][0]).toBe(true)
    })

    /** Vérifie l’affichage d’une erreur si la confirmation ne correspond pas au mot de passe */
    it('affiche une erreur si la confirmation ne correspond pas', async () => {
        const wrapper = mount(PasswordInput, {
            props: {
                name: 'password',
                modelValue: 'ValidPassword!1234',
                showConfirmation: true
            },
            global: { components: { BaseInput } }
        })

        // Trouver le deuxième input (confirmation)
        const confirmationInput = wrapper.find('input[name="confirmPassword"]')
        await confirmationInput.setValue('AnotherPassword')
        await confirmationInput.trigger('blur')
        expect(wrapper.html()).toContain('Les mots de passe ne correspondent pas.')
    })
})
