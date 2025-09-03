import { describe, it, expect, beforeEach, vi } from 'vitest'
import { mount } from '@vue/test-utils'
import { createRouter, createWebHistory } from 'vue-router'
import App from '../src/App.vue'

describe('App.vue', () => {
    let router
    let wrapper

    beforeEach(() => {
        // Créer un router de test
        router = createRouter({
            history: createWebHistory(),
            routes: [
                { path: '/', name: 'Home' },
                { path: '/dashboard', name: 'Dashboard' },
                { path: '/login', name: 'Login' }
            ]
        })

        // Nettoyer les événements
        vi.clearAllMocks()
    })

    const createWrapper = (props = {}) => {
        return mount(App, {
            global: {
                plugins: [router],
                stubs: {
                    'RouterView': true,
                    'Navbar': true,
                    'PasswordChangeAlert': true
                }
            },
            props
        })
    }

    describe('Rendu initial', () => {
        it('affiche la structure principale', () => {
            wrapper = createWrapper()

            expect(wrapper.find('.main-content').exists()).toBe(true)
            expect(wrapper.findComponent({ name: 'Navbar' }).exists()).toBe(true)
            expect(wrapper.findComponent({ name: 'PasswordChangeAlert' }).exists()).toBe(true)
            expect(wrapper.findComponent({ name: 'RouterView' }).exists()).toBe(true)
        })

        it('masque le splash par défaut', () => {
            wrapper = createWrapper()

            expect(wrapper.find('.splash-overlay').exists()).toBe(false)
        })
    })

    describe('Gestion du splash', () => {
        beforeEach(() => {
            wrapper = createWrapper()
        })

        it('affiche le splash quand déclenché', async () => {
            wrapper.vm.triggerSplash()

            expect(wrapper.vm.showSplash).toBe(true)
            expect(wrapper.find('.splash-overlay').exists()).toBe(true)
        })

        it('affiche le contenu du splash', async () => {
            wrapper.vm.triggerSplash()

            const splashContent = wrapper.find('.splash-content')
            expect(splashContent.exists()).toBe(true)
            expect(splashContent.find('.splash-title').text()).toBe('Taskforce')
            expect(splashContent.find('.splash-subtitle').text()).toBe('Chargement des données...')
            expect(splashContent.find('.splash-spinner').exists()).toBe(true)
        })

        it('masque automatiquement le splash après 5 secondes', async () => {
            vi.useFakeTimers()

            wrapper.vm.triggerSplash()
            expect(wrapper.vm.showSplash).toBe(true)

            // Avancer le temps de 5 secondes
            vi.advanceTimersByTime(5000)

            expect(wrapper.vm.showSplash).toBe(false)

            vi.useRealTimers()
        })

        it('annule le timeout précédent si déclenché plusieurs fois', async () => {
            vi.useFakeTimers()

            wrapper.vm.triggerSplash()
            expect(wrapper.vm.showSplash).toBe(true)

            // Déclencher à nouveau avant la fin du premier
            vi.advanceTimersByTime(2000)
            wrapper.vm.triggerSplash()

            // Avancer le temps pour le premier timeout
            vi.advanceTimersByTime(3000)
            expect(wrapper.vm.showSplash).toBe(true) // Devrait encore être affiché

            // Avancer le temps pour le deuxième timeout
            vi.advanceTimersByTime(2000)
            expect(wrapper.vm.showSplash).toBe(false)

            vi.useRealTimers()
        })
    })

    describe('Gestion des événements', () => {
        beforeEach(() => {
            wrapper = createWrapper()
        })

        it('écoute l\'événement show-splash', () => {
            const addEventListenerSpy = vi.spyOn(window, 'addEventListener')

            // Le composant devrait écouter l'événement au montage
            expect(addEventListenerSpy).toHaveBeenCalledWith('show-splash', expect.any(Function))
        })

        it('déclenche le splash lors de l\'événement show-splash', () => {
            expect(wrapper.vm.showSplash).toBe(false)

            // Déclencher l'événement
            window.dispatchEvent(new CustomEvent('show-splash'))

            expect(wrapper.vm.showSplash).toBe(true)
        })

        it('nettoie les événements lors de la destruction', () => {
            const removeEventListenerSpy = vi.spyOn(window, 'removeEventListener')

            wrapper.unmount()

            expect(removeEventListenerSpy).toHaveBeenCalledWith('show-splash', expect.any(Function))
        })

        it('nettoie le timeout lors de la destruction', () => {
            vi.useFakeTimers()

            wrapper.vm.triggerSplash()
            expect(wrapper.vm.splashTimeoutId).toBeTruthy()

            wrapper.unmount()

            // Le timeout devrait être nettoyé
            expect(wrapper.vm.splashTimeoutId).toBeNull()

            vi.useRealTimers()
        })
    })

    describe('Styles et animations', () => {
        beforeEach(() => {
            wrapper = createWrapper()
        })

        it('applique les styles du splash', () => {
            wrapper.vm.triggerSplash()

            const splashOverlay = wrapper.find('.splash-overlay')
            expect(splashOverlay.exists()).toBe(true)

            // Vérifier que les classes CSS sont appliquées
            expect(splashOverlay.classes()).toContain('splash-overlay')
        })

        it('affiche l\'animation du spinner', () => {
            wrapper.vm.triggerSplash()

            const spinner = wrapper.find('.splash-spinner')
            expect(spinner.exists()).toBe(true)
            expect(spinner.classes()).toContain('splash-spinner')
        })

        it('applique le gradient de fond', () => {
            wrapper.vm.triggerSplash()

            const splashOverlay = wrapper.find('.splash-overlay')
            const styles = window.getComputedStyle(splashOverlay.element)

            // Le gradient devrait être appliqué via CSS
            expect(splashOverlay.element.style.background).toContain('linear-gradient')
        })
    })

    describe('Gestion des erreurs', () => {
        it('gère les erreurs lors du déclenchement du splash', () => {
            wrapper = createWrapper()

            // Simuler une erreur dans triggerSplash
            const originalTriggerSplash = wrapper.vm.triggerSplash
            wrapper.vm.triggerSplash = vi.fn().mockImplementation(() => {
                throw new Error('Splash error')
            })

            // Ne devrait pas planter l'application
            expect(() => {
                wrapper.vm.handleShowSplashEvent()
            }).not.toThrow()

            // Restaurer la méthode originale
            wrapper.vm.triggerSplash = originalTriggerSplash
        })

        it('gère les timeouts invalides', () => {
            wrapper = createWrapper()

            // Simuler un timeout invalide
            wrapper.vm.splashTimeoutId = 'invalid-timeout'

            // Ne devrait pas planter
            expect(() => {
                wrapper.vm.triggerSplash()
            }).not.toThrow()
        })
    })

    describe('Performance', () => {
        it('ne crée pas de timeout inutile', () => {
            wrapper = createWrapper()

            const setTimeoutSpy = vi.spyOn(global, 'setTimeout')

            // Ne pas déclencher le splash
            expect(setTimeoutSpy).not.toHaveBeenCalled()
        })

        it('nettoie correctement les timeouts', () => {
            vi.useFakeTimers()

            wrapper = createWrapper()

            const clearTimeoutSpy = vi.spyOn(global, 'clearTimeout')

            wrapper.vm.triggerSplash()
            wrapper.vm.triggerSplash() // Déclencher à nouveau

            // Devrait nettoyer le timeout précédent
            expect(clearTimeoutSpy).toHaveBeenCalled()

            vi.useRealTimers()
        })
    })

    describe('Accessibilité', () => {
        it('affiche le titre du splash', () => {
            wrapper = createWrapper()
            wrapper.vm.triggerSplash()

            const title = wrapper.find('.splash-title')
            expect(title.exists()).toBe(true)
            expect(title.text()).toBe('Taskforce')
        })

        it('affiche le sous-titre explicatif', () => {
            wrapper = createWrapper()
            wrapper.vm.triggerSplash()

            const subtitle = wrapper.find('.splash-subtitle')
            expect(subtitle.exists()).toBe(true)
            expect(subtitle.text()).toBe('Chargement des données...')
        })

        it('utilise des couleurs contrastées', () => {
            wrapper = createWrapper()
            wrapper.vm.triggerSplash()

            const splashOverlay = wrapper.find('.splash-overlay')
            expect(splashOverlay.element.style.color).toBe('white')
        })
    })

    describe('Responsive design', () => {
        it('affiche le splash en plein écran', () => {
            wrapper = createWrapper()
            wrapper.vm.triggerSplash()

            const splashOverlay = wrapper.find('.splash-overlay')
            const styles = window.getComputedStyle(splashOverlay.element)

            expect(styles.position).toBe('fixed')
            expect(styles.top).toBe('0px')
            expect(styles.left).toBe('0px')
            expect(styles.width).toBe('100%')
            expect(styles.height).toBe('100%')
        })

        it('centre le contenu du splash', () => {
            wrapper = createWrapper()
            wrapper.vm.triggerSplash()

            const splashOverlay = wrapper.find('.splash-overlay')
            const styles = window.getComputedStyle(splashOverlay.element)

            expect(styles.display).toBe('flex')
            expect(styles.alignItems).toBe('center')
            expect(styles.justifyContent).toBe('center')
        })
    })
})
