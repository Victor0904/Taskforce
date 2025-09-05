import { describe, it, expect, beforeEach, vi } from 'vitest'
import { mount } from '@vue/test-utils'
import { createRouter, createWebHistory } from 'vue-router'
import Login from '../../src/views/Login.vue'
import axios from 'axios'
import { jwtDecode } from 'jwt-decode'

// Mocks
vi.mock('axios')
vi.mock('jwt-decode')

describe('Login.vue', () => {
    let router
    let wrapper
    let store

    beforeEach(async () => {
        vi.clearAllMocks()

        // localStorage mock avec véritable stockage en mémoire
        store = {}
        Object.defineProperty(window, 'localStorage', {
            value: {
                getItem: vi.fn((k) => (k in store ? store[k] : null)),
                setItem: vi.fn((k, v) => { store[k] = String(v) }),
                removeItem: vi.fn((k) => { delete store[k] }),
                clear: vi.fn(() => { store = {} }),
            },
            writable: true,
        })
        window.localStorage.clear()

        // Router de test prêt
        router = createRouter({
            history: createWebHistory(),
            routes: [
                { path: '/login', name: 'Login' },
                { path: '/dashboard', name: 'Dashboard' },
                { path: '/admin', name: 'Admin' },
                { path: '/reset-password', name: 'ResetPassword' },
            ],
        })
        router.push('/login')
        await router.isReady()
    })

    const createWrapper = (props = {}) =>
        mount(Login, {
            global: {
                plugins: [router],
                stubs: { 'router-link': true },
            },
            props,
        })

    // ---------------- Rendu initial ----------------
    describe('Rendu initial', () => {
        it('affiche le formulaire de connexion', () => {
            wrapper = createWrapper()
            expect(wrapper.find('.login-container').exists()).toBe(true)
            expect(wrapper.find('.login-card').exists()).toBe(true)
            expect(wrapper.find('h1').text()).toBe('Connexion')
        })

        it('affiche les champs email et mot de passe', () => {
            wrapper = createWrapper()
            const emailInput = wrapper.find('input[type="email"]')
            const passwordInput = wrapper.find('input[type="password"]')
            expect(emailInput.exists()).toBe(true)
            expect(passwordInput.exists()).toBe(true)
        })

        it('affiche le bouton de connexion', () => {
            wrapper = createWrapper()
            const submitButton = wrapper.find('button[type="submit"]')
            expect(submitButton.exists()).toBe(true)
            expect(submitButton.text()).toBe('Se connecter')
        })

        it("masque le message d'erreur par défaut", () => {
            wrapper = createWrapper()
            const errorMessage = wrapper.find('.alert-error')
            expect(errorMessage.exists()).toBe(false)
        })
    })

    // ---------------- Form data ----------------
    describe('Gestion des données du formulaire', () => {
        it('lie correctement les données aux champs', async () => {
            wrapper = createWrapper()
            const emailInput = wrapper.find('input[type="email"]')
            const passwordInput = wrapper.find('input[type="password"]')
            await emailInput.setValue('test@example.com')
            await passwordInput.setValue('password123')
            expect(wrapper.vm.email).toBe('test@example.com')
            expect(wrapper.vm.password).toBe('password123')
        })

        it('valide que les champs sont requis', () => {
            wrapper = createWrapper()
            const emailInput = wrapper.find('input[type="email"]')
            const passwordInput = wrapper.find('input[type="password"]')
            expect(emailInput.attributes('required')).toBeDefined()
            expect(passwordInput.attributes('required')).toBeDefined()
        })
    })

    // ---------------- Processus de connexion ----------------
    describe('Processus de connexion', () => {
        beforeEach(() => {
            wrapper = createWrapper()
        })

        it("appelle l'API de connexion avec les bonnes données", async () => {
            const mockResponse = { data: { token: 'fake-jwt-token', mustChangePassword: false } }
            axios.post.mockResolvedValue(mockResponse)
            jwtDecode.mockReturnValue({ roles: ['ROLE_USER'], username: 'test@example.com' })

            await wrapper.find('input[type="email"]').setValue('test@example.com')
            await wrapper.find('input[type="password"]').setValue('password123')
            await wrapper.find('form').trigger('submit.prevent')

            expect(axios.post).toHaveBeenCalledWith('http://127.0.0.1:8000/api/login', {
                email: 'test@example.com',
                password: 'password123',
            })
        })

        it('stocke le token dans localStorage après connexion réussie', async () => {
            const mockResponse = { data: { token: 'fake-jwt-token', mustChangePassword: false } }
            axios.post.mockResolvedValue(mockResponse)
            jwtDecode.mockReturnValue({ roles: ['ROLE_USER'], username: 'test@example.com' })

            await wrapper.find('input[type="email"]').setValue('test@example.com')
            await wrapper.find('input[type="password"]').setValue('password123')
            await wrapper.find('form').trigger('submit.prevent')

            expect(localStorage.getItem('token')).toBe('fake-jwt-token')
            expect(localStorage.getItem('userEmail')).toBe('test@example.com')
            expect(localStorage.getItem('mustChangePassword')).toBe('false')
        })

        it("déclenche l'événement token-changed après connexion", async () => {
            const mockResponse = { data: { token: 'fake-jwt-token', mustChangePassword: false } }
            axios.post.mockResolvedValue(mockResponse)
            jwtDecode.mockReturnValue({ roles: ['ROLE_USER'], username: 'test@example.com' })
            const dispatchSpy = vi.spyOn(window, 'dispatchEvent')

            await wrapper.find('input[type="email"]').setValue('test@example.com')
            await wrapper.find('input[type="password"]').setValue('password123')
            await wrapper.find('form').trigger('submit.prevent')

            expect(dispatchSpy).toHaveBeenCalledWith(expect.objectContaining({ type: 'token-changed' }))
        })

        it("déclenche l'événement show-splash après connexion", async () => {
            const mockResponse = { data: { token: 'fake-jwt-token', mustChangePassword: false } }
            axios.post.mockResolvedValue(mockResponse)
            jwtDecode.mockReturnValue({ roles: ['ROLE_USER'], username: 'test@example.com' })
            const dispatchSpy = vi.spyOn(window, 'dispatchEvent')

            await wrapper.find('input[type="email"]').setValue('test@example.com')
            await wrapper.find('input[type="password"]').setValue('password123')
            await wrapper.find('form').trigger('submit.prevent')

            expect(dispatchSpy).toHaveBeenCalledWith(expect.objectContaining({ type: 'show-splash' }))
        })
    })

    // ---------------- Redirections ----------------
    describe('Redirection après connexion', () => {
        beforeEach(() => {
            wrapper = createWrapper()
        })

        it('redirige vers /admin pour ROLE_ADMIN', async () => {
            vi.useFakeTimers()
            const mockResponse = { data: { token: 'fake-jwt-token', mustChangePassword: false } }
            axios.post.mockResolvedValue(mockResponse)
            jwtDecode.mockReturnValue({ roles: ['ROLE_ADMIN'], username: 'admin@example.com' })
            const pushSpy = vi.spyOn(router, 'push')

            await wrapper.find('input[type="email"]').setValue('admin@example.com')
            await wrapper.find('input[type="password"]').setValue('password123')
            await wrapper.find('form').trigger('submit.prevent')

            vi.runAllTimers()
            await wrapper.vm.$nextTick()
            expect(pushSpy).toHaveBeenCalledWith('/admin')
            vi.useRealTimers()
        })

        it('redirige vers /admin pour ROLE_CHEF_PROJET', async () => {
            vi.useFakeTimers()
            const mockResponse = { data: { token: 'fake-jwt-token', mustChangePassword: false } }
            axios.post.mockResolvedValue(mockResponse)
            jwtDecode.mockReturnValue({ roles: ['ROLE_CHEF_PROJET'], username: 'chef@example.com' })
            const pushSpy = vi.spyOn(router, 'push')

            await wrapper.find('input[type="email"]').setValue('chef@example.com')
            await wrapper.find('input[type="password"]').setValue('password123')
            await wrapper.find('form').trigger('submit.prevent')

            vi.runAllTimers()
            await wrapper.vm.$nextTick()
            expect(pushSpy).toHaveBeenCalledWith('/admin')
            vi.useRealTimers()
        })

        it('redirige vers /admin pour ROLE_MANAGER', async () => {
            vi.useFakeTimers()
            const mockResponse = { data: { token: 'fake-jwt-token', mustChangePassword: false } }
            axios.post.mockResolvedValue(mockResponse)
            jwtDecode.mockReturnValue({ roles: ['ROLE_MANAGER'], username: 'manager@example.com' })
            const pushSpy = vi.spyOn(router, 'push')

            await wrapper.find('input[type="email"]').setValue('manager@example.com')
            await wrapper.find('input[type="password"]').setValue('password123')
            await wrapper.find('form').trigger('submit.prevent')

            vi.runAllTimers()
            await wrapper.vm.$nextTick()
            expect(pushSpy).toHaveBeenCalledWith('/admin')
            vi.useRealTimers()
        })

        it('redirige vers /dashboard pour ROLE_USER', async () => {
            vi.useFakeTimers()
            const mockResponse = { data: { token: 'fake-jwt-token', mustChangePassword: false } }
            axios.post.mockResolvedValue(mockResponse)
            jwtDecode.mockReturnValue({ roles: ['ROLE_USER'], username: 'user@example.com' })
            const pushSpy = vi.spyOn(router, 'push')

            await wrapper.find('input[type="email"]').setValue('user@example.com')
            await wrapper.find('input[type="password"]').setValue('password123')
            await wrapper.find('form').trigger('submit.prevent')

            vi.runAllTimers()
            await wrapper.vm.$nextTick()
            expect(pushSpy).toHaveBeenCalledWith('/dashboard')
            vi.useRealTimers()
        })
    })

    // ---------------- Gestion des erreurs ----------------
    describe('Gestion des erreurs', () => {
        beforeEach(() => {
            wrapper = createWrapper()
        })

        it("affiche un message d'erreur en cas d'échec de connexion", async () => {
            axios.post.mockRejectedValue(new Error('Identifiants invalides'))

            await wrapper.find('input[type="email"]').setValue('test@example.com')
            await wrapper.find('input[type="password"]').setValue('wrongpassword')
            await wrapper.find('form').trigger('submit.prevent')
            await wrapper.vm.$nextTick()

            const errorMessage = wrapper.find('.alert-error')
            expect(errorMessage.exists()).toBe(true)
            expect(errorMessage.text()).toBe('Identifiants invalides')
        })

        it("efface le message d'erreur lors d'une nouvelle tentative", async () => {
            axios.post.mockRejectedValueOnce(new Error('Identifiants invalides'))

            await wrapper.find('input[type="email"]').setValue('test@example.com')
            await wrapper.find('input[type="password"]').setValue('wrongpassword')
            await wrapper.find('form').trigger('submit.prevent')
            await wrapper.vm.$nextTick()
            expect(wrapper.find('.alert-error').exists()).toBe(true)

            const mockResponse = { data: { token: 'fake-jwt-token', mustChangePassword: false } }
            axios.post.mockResolvedValue(mockResponse)
            jwtDecode.mockReturnValue({ roles: ['ROLE_USER'], username: 'test@example.com' })

            await wrapper.find('form').trigger('submit.prevent')
            await wrapper.vm.$nextTick()
            expect(wrapper.find('.alert-error').exists()).toBe(false)
        })
    })

    // ---------------- Must change password ----------------
    describe('Gestion du changement de mot de passe obligatoire', () => {
        beforeEach(() => {
            wrapper = createWrapper()
        })

        it('gère mustChangePassword true', async () => {
            const mockResponse = { data: { token: 'fake-jwt-token', mustChangePassword: true } }
            axios.post.mockResolvedValue(mockResponse)
            jwtDecode.mockReturnValue({ roles: ['ROLE_USER'], username: 'test@example.com' })

            await wrapper.find('input[type="email"]').setValue('test@example.com')
            await wrapper.find('input[type="password"]').setValue('password123')
            await wrapper.find('form').trigger('submit.prevent')

            expect(localStorage.getItem('mustChangePassword')).toBe('true')
        })

        it('gère mustChangePassword false', async () => {
            const mockResponse = { data: { token: 'fake-jwt-token', mustChangePassword: false } }
            axios.post.mockResolvedValue(mockResponse)
            jwtDecode.mockReturnValue({ roles: ['ROLE_USER'], username: 'test@example.com' })

            await wrapper.find('input[type="email"]').setValue('test@example.com')
            await wrapper.find('input[type="password"]').setValue('password123')
            await wrapper.find('form').trigger('submit.prevent')

            expect(localStorage.getItem('mustChangePassword')).toBe('false')
        })
    })

    // ---------------- Validation ----------------
    describe('Validation du formulaire', () => {
        it('empêche la soumission avec des champs vides', async () => {
            wrapper = createWrapper()
            await wrapper.find('form').trigger('submit.prevent')
            expect(axios.post).not.toHaveBeenCalled()
        })

        it("affiche un message d'erreur pour des champs vides côté client", async () => {
            wrapper = createWrapper()
            await wrapper.find('input[type="email"]').setValue('test@example.com')
            await wrapper.find('input[type="password"]').setValue('')
            await wrapper.find('form').trigger('submit.prevent')

            const errorMessage = wrapper.find('.alert-error')
            expect(errorMessage.exists()).toBe(true)
            expect(errorMessage.text()).toBe('Veuillez remplir tous les champs')
            expect(axios.post).not.toHaveBeenCalled()
        })

        it("affiche un message d'erreur pour email vide côté client", async () => {
            wrapper = createWrapper()
            await wrapper.find('input[type="email"]').setValue('')
            await wrapper.find('input[type="password"]').setValue('password123')
            await wrapper.find('form').trigger('submit.prevent')

            const errorMessage = wrapper.find('.alert-error')
            expect(errorMessage.exists()).toBe(true)
            expect(errorMessage.text()).toBe('Veuillez remplir tous les champs')
            expect(axios.post).not.toHaveBeenCalled()
        })

        it('valide le format email', () => {
            wrapper = createWrapper()
            const emailInput = wrapper.find('input[type="email"]')
            expect(emailInput.attributes('type')).toBe('email')
        })
    })
})
