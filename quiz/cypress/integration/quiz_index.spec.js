/// <reference types="cypress" />

describe('quiz-app', () => {
    it('Create a new quiz redirects to /new route', () => {
        cy.visit('/');

        cy.get('a#new-quiz').click();
        cy.location('pathname').should('eq', '/new');
    });
});

