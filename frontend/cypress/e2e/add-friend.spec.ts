describe('Ajout d\'un ami via le code ami', () => {
    it('Connecte un user, accède à la modal d\'ajout et envoie la demande', () => {
        // 1. Accès à la page d’accueil publique (timeout car assets chargent)
        cy.visit('https://localhost:5173/')
        cy.contains('Roomies', { timeout: 8000 }).should('be.visible')
        cy.contains('Connexion', { timeout: 8000 }).should('be.visible')

        // 2. Clique sur le bouton Connexion
        cy.contains('Connexion').click()
        cy.url({ timeout: 5000 }).should('include', '/login')

        // 3. Saisie email/password et submit
        cy.get('input[name="email"]', { timeout: 8000 }).type('user@user.com')
        cy.get('input[name="password"]').type('password')
        cy.get('button[type="submit"]').contains('Se connecter').click()

        // 4. Arrivée sur le dashboard et présence de la sidebar (timeout car l’init utilisateur/ws peut être long)
        cy.url({ timeout: 16000 }).should('include', '/dashboard')
        cy.get('[data-testid="sidebar-main"]', { timeout: 12000 }).within(() => {
            cy.get('button[title="Chat global"]').should('exist')
            cy.get('button[title="Tableau de bord"]').should('exist')
        })


        // 5. Clique sur le bouton "Amis" dans l’UI principale
        cy.contains('Amis', { timeout: 8000 }).should('be.visible').click()

        // 6. Clique sur le bouton "Ajouter" dans la vue amis
        cy.contains('Ajouter', { timeout: 8000 }).should('be.visible').click()

        // 7. La modal d’ajout d’ami doit apparaître
        cy.contains('Ajouter des amis', { timeout: 8000 }).should('be.visible')
        cy.get('input[name="friendCode"]').should('be.visible')

        // 8. Remplir le code ami (20 caractères)
        const friendCode = '03BDD87491F927C8E43C' // ⚠️ à adapter selon users de test
        cy.get('input[name="friendCode"]').type(friendCode)

        // 9. Submit le formulaire
        cy.get('button[type="submit"]').contains('Envoyer').click()

        // 10. Vérifie que le toast de succès apparaît (timeout allongé)
        cy.contains('Demande d’ami envoyée avec succès !', { timeout: 8000 }).should('be.visible')

        // 11. La modal se ferme
        cy.contains('Ajouter des amis').should('not.exist')
    })
})
