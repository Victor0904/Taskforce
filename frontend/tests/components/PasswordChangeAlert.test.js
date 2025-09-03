import { describe, it, expect, beforeEach, vi } from 'vitest'
import { mount } from '@vue/test-utils'
import { createRouter, createWebHistory } from 'vue-router'
import PasswordChangeAlert from '../../src/components/PasswordChangeAlert.vue'

describe('PasswordChangeAlert.vue', () => {
    let router
    let wrapper

    beforeEach(() => {
        // Créer un router de test
        router = createRouter({
            history: createWebHistory(),
            routes: [
                { path: '/reset-password', name: 'ResetPassword' }
            ]
        })

        // Nettoyer localStorage
        localStorage.clear()
    })

    const createWrapper = (props = {}) => {
        return mount(PasswordChangeAlert, {
            global: {
                plugins: [router],
                stubs: {
                    'router-link': true
                }
            },
            props
        })
    }

    describe('Affichage conditionnel', () => {
        it('masque l\'alerte par défaut', () => {
            wrapper = createWrapper()

            expect(wrapper.find('.password-change-alert').exists()).toBe(false)
        })

        it('affiche l\'alerte quand mustChangePassword est true et token existe', () => {
            localStorage.setItem('token', 'fake-token')
            localStorage.setItem('mustChangePassword', 'true')

            wrapper = createWrapper()

            expect(wrapper.find('.password-change-alert').exists()).toBe(true)
        })

        it('masque l\'alerte quand mustChangePassword est false', () => {
            localStorage.setItem('token', 'fake-token')
            localStorage.setItem('mustChangePassword', 'false')

            wrapper = createWrapper()

            expect(wrapper.find('.password-change-alert').exists()).toBe(false)
        })

        it('masque l\'alerte quand pas de token', () => {
            localStorage.setItem('mustChangePassword', 'true')

            wrapper = createWrapper()

            expect(wrapper.find('.password-change-alert').exists()).toBe(false)
        })
    })

    describe('Contenu de l\'alerte', () => {
        beforeEach(() => {
            localStorage.setItem('token', 'fake-token')
            localStorage.setItem('mustChangePassword', 'true')
        })

        it('affiche le titre de l\'alerte', () => {
            wrapper = createWrapper()

            const alertText = wrapper.find('.alert-text strong')
            expect(alertText.text()).toBe('Changement de mot de passe requis')
        })

        it('affiche le message explicatif', () => {
            wrapper = createWrapper()

            const alertText = wrapper.find('.alert-text p')
            expect(alertText.text()).toContain('Votre compte utilise encore le mot de passe par défaut "admin"')
        })

        it('affiche le bouton de changement de mot de passe', () => {
            wrapper = createWrapper()

            const button = wrapper.find('.btn-primary')
            expect(button.exists()).toBe(true)
            expect(button.text()).toBe('Changer le mot de passe')
        })

        it('affiche l\'icône de l\'alerte', () => {
            wrapper = createWrapper()

            const icon = wrapper.find('.alert-icon')
            expect(icon.exists()).toBe(true)
            expect(icon.text()).toBe('🔒')
        })
    })

    describe('Navigation vers reset-password', () => {
        beforeEach(() => {
            localStorage.setItem('token', 'fake-token')
            localStorage.setItem('mustChangePassword', 'true')
        })

        it('redirige vers /reset-password quand on clique sur le bouton', async () => {
            const pushSpy = vi.spyOn(router, 'push')

            wrapper = createWrapper()

            const button = wrapper.find('.btn-primary')
            await button.trigger('click')

            expect(pushSpy).toHaveBeenCalledWith('/reset-password')
        })

        it('redirige vers /reset-password quand on clique sur le lien', async () => {
            const pushSpy = vi.spyOn(router, 'push')

            wrapper = createWrapper()

            const link = wrapper.find('.alert-link')
            await link.trigger('click')

            expect(pushSpy).toHaveBeenCalledWith('/reset-password')
        })
    })

    describe('Réactivité aux changements', () => {
        it('réagit aux changements de token dans localStorage', async () => {
            wrapper = createWrapper()

            // Initialement masqué
            expect(wrapper.find('.password-change-alert').exists()).toBe(false)

            // Ajouter le token et mustChangePassword
            localStorage.setItem('token', 'new-token')
            localStorage.setItem('mustChangePassword', 'true')

            // Déclencher l'événement storage
            window.dispatchEvent(new StorageEvent('storage', {
                key: 'token',
                newValue: 'new-token'
            }))

            await wrapper.vm.$nextTick()

            // L'alerte devrait maintenant être visible
            expect(wrapper.find('.password-change-alert').exists()).toBe(true)
        })

        it('réagit aux changements de mustChangePassword', async () => {
            localStorage.setItem('token', 'fake-token')
            localStorage.setItem('mustChangePassword', 'true')

            wrapper = createWrapper()

            // Initialement visible
            expect(wrapper.find('.password-change-alert').exists()).toBe(true)

            // Changer mustChangePassword à false
            localStorage.setItem('mustChangePassword', 'false')

            // Déclencher l'événement storage
            window.dispatchEvent(new StorageEvent('storage', {
                key: 'mustChangePassword',
                newValue: 'false'
            }))

            await wrapper.vm.$nextTick()

            // L'alerte devrait maintenant être masquée
            expect(wrapper.find('.password-change-alert').exists()).toBe(false)
        })

        it('réagit aux événements token-changed', async () => {
            wrapper = createWrapper()

            // Initialement masqué
            expect(wrapper.find('.password-change-alert').exists()).toBe(false)

            // Ajouter les données nécessaires
            localStorage.setItem('token', 'new-token')
            localStorage.setItem('mustChangePassword', 'true')

            // Déclencher l'événement personnalisé
            window.dispatchEvent(new CustomEvent('token-changed'))

            await wrapper.vm.$nextTick()

            // L'alerte devrait maintenant être visible
            expect(wrapper.find('.password-change-alert').exists()).toBe(true)
        })
    })

    describe('Gestion des états', () => {
        it('gère correctement l\'état showAlert', () => {
            wrapper = createWrapper()

            // État initial
            expect(wrapper.vm.showAlert).toBe(false)

            // Simuler les conditions d'affichage
            localStorage.setItem('token', 'fake-token')
            localStorage.setItem('mustChangePassword', 'true')

            wrapper.vm.checkPasswordStatus()

            expect(wrapper.vm.showAlert).toBe(true)
        })

        it('met à jour showAlert quand les conditions changent', () => {
            localStorage.setItem('token', 'fake-token')
            localStorage.setItem('mustChangePassword', 'true')

            wrapper = createWrapper()

            expect(wrapper.vm.showAlert).toBe(true)

            // Changer les conditions
            localStorage.removeItem('token')
            wrapper.vm.checkPasswordStatus()

            expect(wrapper.vm.showAlert).toBe(false)
        })
    })

    describe('Méthodes utilitaires', () => {
        it('checkPasswordStatus fonctionne correctement', () => {
            wrapper = createWrapper()

            // Test avec token et mustChangePassword
            localStorage.setItem('token', 'fake-token')
            localStorage.setItem('mustChangePassword', 'true')

            wrapper.vm.checkPasswordStatus()
            expect(wrapper.vm.showAlert).toBe(true)

            // Test sans token
            localStorage.removeItem('token')
            wrapper.vm.checkPasswordStatus()
            expect(wrapper.vm.showAlert).toBe(false)

            // Test avec token mais sans mustChangePassword
            localStorage.setItem('token', 'fake-token')
            localStorage.setItem('mustChangePassword', 'false')

            wrapper.vm.checkPasswordStatus()
            expect(wrapper.vm.showAlert).toBe(false)
        })

        it('goToResetPassword fonctionne correctement', async () => {
            const pushSpy = vi.spyOn(router, 'push')

            wrapper = createWrapper()

            wrapper.vm.goToResetPassword()

            expect(pushSpy).toHaveBeenCalledWith('/reset-password')
        })
    })

    describe('Nettoyage des événements', () => {
        it('nettoie les événements lors de la destruction du composant', () => {
            const removeEventListenerSpy = vi.spyOn(window, 'removeEventListener')

            wrapper = createWrapper()
            wrapper.unmount()

            expect(removeEventListenerSpy).toHaveBeenCalledWith('storage', expect.any(Function))
            expect(removeEventListenerSpy).toHaveBeenCalledWith('token-changed', expect.any(Function))
        })
    })
})
