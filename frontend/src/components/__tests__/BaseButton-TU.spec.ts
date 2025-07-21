/**
 * Tests unitaires du composant BaseButton.vue
 * Vérifie le rendu des slots, la gestion des variantes, l'état disabled/loading,
 * l'affichage du spinner de chargement et l'affichage des slots d'icônes.
 */
import { describe, it, expect } from 'vitest'
import { mount } from '@vue/test-utils'
import BaseButton from '@/components/base/BaseButton.vue'

describe('BaseButton', () => {

  /** Vérifie que le contenu passé dans le slot par défaut s'affiche */
  it('affiche le contenu du slot', () => {
    const wrapper = mount(BaseButton, {
      slots: { default: 'Clique-moi' }
    })
    expect(wrapper.text()).toContain('Clique-moi')
  })

  /** Vérifie que la classe CSS correspondant à la variante est appliquée */
  it('applique la bonne classe de variante', () => {
    const wrapper = mount(BaseButton, { props: { variant: 'success' } })
    const btn = wrapper.find('button')
    expect(btn.classes().join(' ')).toContain('bg-green-500')
  })

  /** Vérifie que le bouton est désactivé quand loading ou disabled est à true */
  it('est désactivé si loading ou disabled est true', async () => {
    const wrapper = mount(BaseButton, { props: { loading: true } })
    expect(wrapper.find('button').attributes('disabled')).toBeDefined()
    await wrapper.setProps({ loading: false, disabled: true })
    expect(wrapper.find('button').attributes('disabled')).toBeDefined()
  })

  /** Vérifie que le spinner de chargement s'affiche si loading est true */
  it('affiche le spinner de chargement quand loading', () => {
    const wrapper = mount(BaseButton, { props: { loading: true } })
    expect(wrapper.html()).toContain('pi-spinner')
  })

  /** Vérifie l'affichage des slots d'icône à gauche et à droite */
  it('affiche les slots icon-left et icon-right', () => {
    const wrapper = mount(BaseButton, {
      props: { iconLeft: true, iconRight: true },
      slots: {
        'icon-left': '<span>Gauche</span>',
        'icon-right': '<span>Droite</span>',
        default: 'OK'
      }
    })
    expect(wrapper.html()).toContain('Gauche')
    expect(wrapper.html()).toContain('Droite')
  })
})
