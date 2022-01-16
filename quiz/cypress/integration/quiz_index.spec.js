it('Test create a quiz redirect', () => {
    cy.visit('/');

    cy.get('a#new-quiz').click();
    cy.location('pathname').should('eq', '/new');
});
