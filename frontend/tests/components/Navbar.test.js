import { describe, it, expect, beforeEach, vi } from 'vitest'
import { mount } from '@vue/test-utils'
import { createRouter, createWebHistory } from 'vue-router'
import Navbar from '../../src/components/Navbar.vue'

// Mock des dépendances
vi.mock('jwt-decode', () => ({
    jwtDecode: vi.fn()
}))

vi.mock('axios', () => ({
    default: {
        get: vi.fn()
    }
}))

describe('Navbar.vue', () => {
    let router
    let wrapper

    beforeEach(() => {
        // Créer un router de test
        router = createRouter({
            history: createWebHistory(),
            routes: [
                { path: '/', name: 'Home' },
                { path: '/dashboard', name: 'Dashboard' },
                { path: '/projets', name: 'Projet' },
                { path: '/admin', name: 'Admin' },
                { path: '/parametres', name: 'Parametres' },
                { path: '/login', name: 'Login' }
            ]
        })

        // Nettoyer localStorage
        localStorage.clear()
    })

    const createWrapper = (props = {}) => {
        return mount(Navbar, {
            global: {
                plugins: [router],
                stubs: {
                    'router-link': true
                }
            },
            props
        })
    }

    describe('État de connexion', () => {
        it('affiche le bouton de connexion quand l\'utilisateur n\'est pas connecté', () => {
            wrapper = createWrapper()

            expect(wrapper.find('.btn-primary').exists()).toBe(true)
            expect(wrapper.find('.btn-primary').text()).toContain('Connexion')
        })

        it('affiche la navigation quand l\'utilisateur est connecté', () => {
            localStorage.setItem('token', 'fake-token')

            wrapper = createWrapper()

            expect(wrapper.find('.navbar-nav').exists()).toBe(true)
            expect(wrapper.find('.btn-primary').exists()).toBe(false)
        })
    })

    describe('Navigation', () => {
        beforeEach(() => {
            localStorage.setItem('token', 'fake-token')
        })

        it('affiche le lien Dashboard', () => {
            wrapper = createWrapper()

            const dashboardLink = wrapper.find('[to="/dashboard"]')
            expect(dashboardLink.exists()).toBe(true)
            expect(dashboardLink.text()).toContain('Dashboard')
        })

        it('affiche le lien Projets', () => {
            wrapper = createWrapper()

            const projetsLink = wrapper.find('[to="/projets"]')
            expect(projetsLink.exists()).toBe(true)
            expect(projetsLink.text()).toContain('Projets')
        })

        it('affiche le bouton de déconnexion', () => {
            wrapper = createWrapper()

            const logoutButton = wrapper.find('.btn-secondary')
            expect(logoutButton.exists()).toBe(true)
            expect(logoutButton.text()).toContain('Déconnexion')
        })
    })

    describe('Gestion des rôles', () => {
        beforeEach(() => {
            localStorage.setItem('token', 'fake-token')
        })

        it('affiche le lien Admin pour ROLE_ADMIN', () => {
            const mockDecode = vi.fn().mockReturnValue({
                roles: ['ROLE_ADMIN'],
                username: 'admin@example.com'
            })

            vi.doMock('jwt-decode', () => ({
                jwtDecode: mockDecode
            }))

            wrapper = createWrapper()

            const adminLink = wrapper.find('[to="/admin"]')
            expect(adminLink.exists()).toBe(true)
        })

        it('affiche le lien Admin pour ROLE_MANAGER', () => {
            const mockDecode = vi.fn().mockReturnValue({
                roles: ['ROLE_MANAGER'],
                username: 'manager@example.com'
            })

            vi.doMock('jwt-decode', () => ({
                jwtDecode: mockDecode
            }))

            wrapper = createWrapper()

            const adminLink = wrapper.find('[to="/admin"]')
            expect(adminLink.exists()).toBe(true)
        })

        it('affiche le lien Admin pour ROLE_CHEF_PROJET', () => {
            const mockDecode = vi.fn().mockReturnValue({
                roles: ['ROLE_CHEF_PROJET'],
                username: 'chef@example.com'
            })

            vi.doMock('jwt-decode', () => ({
                jwtDecode: mockDecode
            }))

            wrapper = createWrapper()

            const adminLink = wrapper.find('[to="/admin"]')
            expect(adminLink.exists()).toBe(true)
        })

        it('masque le lien Admin pour ROLE_USER uniquement', () => {
            const mockDecode = vi.fn().mockReturnValue({
                roles: ['ROLE_USER'],
                username: 'user@example.com'
            })

            vi.doMock('jwt-decode', () => ({
                jwtDecode: mockDecode
            }))

            wrapper = createWrapper()

            const adminLink = wrapper.find('[to="/admin"]')
            expect(adminLink.exists()).toBe(false)
        })
    })

    describe('Affichage de l\'utilisateur', () => {
        beforeEach(() => {
            localStorage.setItem('token', 'fake-token')
        })

        it('affiche l\'email de l\'utilisateur', () => {
            const mockDecode = vi.fn().mockReturnValue({
                roles: ['ROLE_USER'],
                username: 'user@example.com'
            })

            vi.doMock('jwt-decode', () => ({
                jwtDecode: mockDecode
            }))

            wrapper = createWrapper()

            const userEmail = wrapper.find('.user-email')
            expect(userEmail.exists()).toBe(true)
        })

        it('affiche "Utilisateur inconnu" si pas d\'email', () => {
            const mockDecode = vi.fn().mockReturnValue({
                roles: ['ROLE_USER'],
                username: null
            })

            vi.doMock('jwt-decode', () => ({
                jwtDecode: mockDecode
            }))

            wrapper = createWrapper()

            const userEmail = wrapper.find('.user-email')
            expect(userEmail.text()).toBe('Utilisateur inconnu')
        })
    })

    describe('Classes CSS selon le rôle', () => {
        beforeEach(() => {
            localStorage.setItem('token', 'fake-token')
        })

        it('applique la classe admin pour ROLE_ADMIN', () => {
            const mockDecode = vi.fn().mockReturnValue({
                roles: ['ROLE_ADMIN'],
                username: 'admin@example.com'
            })

            vi.doMock('jwt-decode', () => ({
                jwtDecode: mockDecode
            }))

            wrapper = createWrapper()

            const userLink = wrapper.find('.user-email-link')
            expect(userLink.classes()).toContain('admin')
        })

        it('applique la classe manager pour ROLE_MANAGER', () => {
            const mockDecode = vi.fn().mockReturnValue({
                roles: ['ROLE_MANAGER'],
                username: 'manager@example.com'
            })

            vi.doMock('jwt-decode', () => ({
                jwtDecode: mockDecode
            }))

            wrapper = createWrapper()

            const userLink = wrapper.find('.user-email-link')
            expect(userLink.classes()).toContain('manager')
        })

        it('applique la classe chef-projet pour ROLE_CHEF_PROJET', () => {
            const mockDecode = vi.fn().mockReturnValue({
                roles: ['ROLE_CHEF_PROJET'],
                username: 'chef@example.com'
            })

            vi.doMock('jwt-decode', () => ({
                jwtDecode: mockDecode
            }))

            wrapper = createWrapper()

            const userLink = wrapper.find('.user-email-link')
            expect(userLink.classes()).toContain('chef-projet')
        })

        it('applique la classe user pour ROLE_USER', () => {
            const mockDecode = vi.fn().mockReturnValue({
                roles: ['ROLE_USER'],
                username: 'user@example.com'
            })

            vi.doMock('jwt-decode', () => ({
                jwtDecode: mockDecode
            }))

            wrapper = createWrapper()

            const userLink = wrapper.find('.user-email-link')
            expect(userLink.classes()).toContain('user')
        })
    })

    describe('Déconnexion', () => {
        beforeEach(() => {
            localStorage.setItem('token', 'fake-token')
            localStorage.setItem('mustChangePassword', 'false')
        })

        it('supprime le token du localStorage lors de la déconnexion', async () => {
            wrapper = createWrapper()

            const logoutButton = wrapper.find('.btn-secondary')
            await logoutButton.trigger('click')

            expect(localStorage.getItem('token')).toBeNull()
            expect(localStorage.getItem('mustChangePassword')).toBeNull()
        })

        it('redirige vers la page de connexion après déconnexion', async () => {
            const pushSpy = vi.spyOn(router, 'push')

            wrapper = createWrapper()

            const logoutButton = wrapper.find('.btn-secondary')
            await logoutButton.trigger('click')

            expect(pushSpy).toHaveBeenCalledWith('/login')
        })
    })

    describe('Gestion des événements', () => {
        it('écoute les changements de localStorage', () => {
            wrapper = createWrapper()

            // Simuler un changement de token
            localStorage.setItem('token', 'new-token')

            // Déclencher l'événement storage
            window.dispatchEvent(new StorageEvent('storage', {
                key: 'token',
                newValue: 'new-token'
            }))

            // Vérifier que le composant réagit
            expect(wrapper.vm.tokenRef).toBe('new-token')
        })

        it('écoute les événements token-changed', () => {
            wrapper = createWrapper()

            // Simuler un changement de token
            localStorage.setItem('token', 'updated-token')

            // Déclencher l'événement personnalisé
            window.dispatchEvent(new CustomEvent('token-changed'))

            // Vérifier que le composant réagit
            expect(wrapper.vm.tokenRef).toBe('updated-token')
        })
    })

    describe('Gestion des erreurs JWT', () => {
        beforeEach(() => {
            localStorage.setItem('token', 'invalid-token')
        })

        it('gère les tokens JWT invalides', () => {
            const mockDecode = vi.fn().mockImplementation(() => {
                throw new Error('Invalid token')
            })

            vi.doMock('jwt-decode', () => ({
                jwtDecode: mockDecode
            }))

            wrapper = createWrapper()

            // Le composant ne devrait pas planter
            expect(wrapper.exists()).toBe(true)
        })
    })
})
