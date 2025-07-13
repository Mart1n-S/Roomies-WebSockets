import { defineStore } from 'pinia'
import { markRaw } from 'vue'
import PrivateMessagesPanel from '@/components/panels/PrivateMessagesPanel.vue'
// d'autres panels à venir...

export const useContextPanelStore = defineStore('contextPanel', {
    state: () => ({
        component: null as any, // Pas de composant affiché par défaut
        props: null as Record<string, any> | null
    }),

    actions: {
        /**
         * Affiche le panneau des messages privés (dashboard)
         */
        showPrivateMessagesPanel() {
            this.component = markRaw(PrivateMessagesPanel)
            this.props = null
        },

        /**
         * Affiche dynamiquement n'importe quel panneau avec des props optionnelles
         */
        showCustomPanel(component: any, props = {}) {
            this.component = markRaw(component)
            this.props = props
        },

        /**
         * Réinitialise complètement le panneau contextuel (rien affiché)
         */
        clearPanel() {
            this.component = null
            this.props = null
        }
    }
})
