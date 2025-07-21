describe('Envoyer un message à ami_fixe_1 en privé', () => {
    it('Se connecte, ouvre le chat privé et envoie un message, puis logout, puis login en tant que ami_fixe_1 et vérifie la réception', () => {
        // 1. Accès à la page d’accueil publique
        cy.visit('http://localhost:5173/')
        cy.contains('Roomies', { timeout: 8000 }).should('be.visible')
        cy.contains('Connexion', { timeout: 8000 }).should('be.visible')

        // 2. Clique sur le bouton Connexion
        cy.contains('Connexion').click()
        cy.url({ timeout: 5000 }).should('include', '/login')

        // 3. Saisie email/password et submit
        cy.get('input[name="email"]', { timeout: 8000 }).type('user@user.com')
        cy.get('input[name="password"]').type('password')
        cy.get('button[type="submit"]').contains('Se connecter').click()

        // 4. Arrivée sur le dashboard et présence de la sidebar
        cy.url({ timeout: 18000 }).should('include', '/dashboard')
        cy.get('[data-testid="sidebar-main"]', { timeout: 12000 }).within(() => {
            cy.get('button[title="Chat global"]').should('exist')
            cy.get('button[title="Tableau de bord"]').should('exist')
        })

        // 5. Clique dans ItemList sur ami_fixe_1 (messages privés)
        cy.contains('span', 'ami_fixe_1', { timeout: 8000 })
            .should('be.visible')
            .click()

        // 6. Vérifie la présence du pseudo dans l'entête de chat privé
        cy.contains('.font-semibold', 'ami_fixe_1', { timeout: 8000 }).should('be.visible')

        // 7. Envoie un message via la textarea du MessageInput
        const monMessage = 'Hello ami_fixe_1, test e2e !'
        cy.get('textarea[name="chatMessage"]', { timeout: 8000 }).type(monMessage)
        cy.get('button i.pi-send').parent().click()

        // 8. Vérifie que le message s’affiche dans le ChatMessage (avec ton pseudo en tant qu’expéditeur)
        cy.contains('.font-semibold', 'userClassique', { timeout: 6000 }).should('exist')
        cy.contains('.text-sm', monMessage, { timeout: 6000 }).should('exist')

        // 9. Déconnexion via le bouton dans la sidebar (avec timeout pour navigation)
        cy.get('[data-testid="sidebar-main"]').within(() => {
            cy.get('button[title="Déconnexion"]', { timeout: 8000 }).click()
        })
        // On attend d'arriver sur la page login ou accueil
        cy.url({ timeout: 10000 }).should('satisfy', url =>
            url.endsWith('/login') || url.endsWith('/')
        )

        // 10. Reconnexion en tant que ami_fixe_1
        cy.contains('Connexion', { timeout: 8000 }).click()
        cy.get('input[name="email"]', { timeout: 8000 }).type('ami1@roomies.test')
        cy.get('input[name="password"]').type('password')
        cy.get('button[type="submit"]').contains('Se connecter').click()
        cy.url({ timeout: 18000 }).should('include', '/dashboard')

        // 11. Clique sur la conversation avec userClassique
        cy.contains('span', 'userClassique', { timeout: 8000 })
            .should('be.visible')
            .click()

        // 12. Vérifie la présence du header et du message reçu (timeout pour fetch ws/messages)
        cy.contains('.font-semibold', 'userClassique', { timeout: 8000 }).should('be.visible')
        cy.contains('.text-sm', monMessage, { timeout: 8000 }).should('be.visible')
    })
})
