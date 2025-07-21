describe('Création d’une partie de jeu en ligne', () => {
    it('userClassique crée une partie, vérifie la présence dans le lobby', () => {
        // 1. Accès à la page d’accueil publique
        cy.visit('http://localhost:5173/')
        cy.contains('Roomies', { timeout: 12000 }).should('be.visible')
        cy.contains('Connexion', { timeout: 12000 }).should('be.visible')

        // 2. Connexion
        cy.contains('Connexion').click()
        cy.url({ timeout: 12000 }).should('include', '/login')
        cy.get('input[name="email"]', { timeout: 12000 }).type('user@user.com')
        cy.get('input[name="password"]').type('password')
        cy.get('button[type="submit"]').contains('Se connecter').click()
        cy.url({ timeout: 22000 }).should('include', '/dashboard')

        // 3. Sidebar : clique sur Jeux en ligne (Games)
        cy.get('[data-testid="sidebar-main"]', { timeout: 12000 }).within(() => {
            cy.get('button[title="Jeux en ligne"]', { timeout: 12000 }).click()
        })

        // 4. Vérifie le titre "Jeux en ligne"
        cy.contains('h1', 'Jeux en ligne', { timeout: 12000 }).should('be.visible')

        // 5. Clique sur "Créer une partie"
        cy.contains('button', 'Créer une partie', { timeout: 12000 }).click()

        // 6. La modal doit s’ouvrir (titre)
        cy.contains('h2', 'Créer une partie', { timeout: 6000 }).should('be.visible')

        // 7. Remplit le nom et submit
        const roomName = 'RoomiesE2E'
        cy.get('input[name="roomName"]').type(roomName)
        cy.get('select[name="selectGame"]').select('morpion')
        cy.get('button[type="submit"]').contains('Créer la partie').click()

        // 8. Le lobby revient, la nouvelle card doit exister (nom + pseudo owner)
        cy.contains('h1', 'Jeux en ligne', { timeout: 12000 }).should('be.visible')
        cy.contains('.text-lg.font-bold', roomName, { timeout: 12000 }).should('exist')
        cy.contains('.text-sm.text-gray-300', 'userClassique', { timeout: 12000 }).should('exist')
    })
})
