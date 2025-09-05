import { describe, it, expect, beforeEach, vi } from 'vitest'
import { mount } from '@vue/test-utils'
import { createRouter, createWebHistory } from 'vue-router'
import Login from '../../src/views/Login.vue'
import axios from 'axios'
import { jwtDecode } from 'jwt-decode'

// Mock des dépendances
vi.mock('axios')
vi.mock('jwt-decode')

describe('Login.vue', () => {
    let router
    let wrapper

    beforeEach(() => {
        // Créer un router de test
        router = createRouter({
            history: createWebHistory(),
            routes: [
                { path: '/login', name: 'Login' },
                { path: '/dashboard', name: 'Dashboard' },
                { path: '/admin', name: 'Admin' },
                { path: '/reset-password', name: 'ResetPassword' }
            ]
        })

        // Nettoyer localStorage
        localStorage.clear()

        // Reset des mocks
        vi.clearAllMocks()
    })

    const createWrapper = (props = {}) => {
        return mount(Login, {
            global: {
                plugins: [router],
                stubs: {
                    'router-link': true
                }
            },
            props
        })
    }

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

        it('masque le message d\'erreur par défaut', () => {
            wrapper = createWrapper()

            const errorMessage = wrapper.find('.alert-error')
            expect(errorMessage.exists()).toBe(false)
        })
    })

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

    describe('Processus de connexion', () => {
        beforeEach(() => {
            wrapper = createWrapper()
        })

        it('appelle l\'API de connexion avec les bonnes données', async () => {
            const mockResponse = {
                data: {
                    token: 'fake-jwt-token',
                    mustChangePassword: false
                }
            }

            axios.post.mockResolvedValue(mockResponse)
            jwtDecode.mockReturnValue({
                roles: ['ROLE_USER'],
                username: 'test@example.com'
            })

            await wrapper.find('input[type="email"]').setValue('test@example.com')
            await wrapper.find('input[type="password"]').setValue('password123')

            await wrapper.find('form').trigger('submit.prevent')

            expect(axios.post).toHaveBeenCalledWith('http://127.0.0.1:8000/api/login', {
                email: 'test@example.com',
                password: 'password123'
            })
        })

        it('stocke le token dans localStorage après connexion réussie', async () => {
            const mockResponse = {
                data: {
                    token: 'fake-jwt-token',
                    mustChangePassword: false
                }
            }

            axios.post.mockResolvedValue(mockResponse)
            jwtDecode.mockReturnValue({
                roles: ['ROLE_USER'],
                username: 'test@example.com'
            })

            await wrapper.find('input[type="email"]').setValue('test@example.com')
            await wrapper.find('input[type="password"]').setValue('password123')

            await wrapper.find('form').trigger('submit.prevent')

            expect(localStorage.getItem('token')).toBe('fake-jwt-token')
            expect(localStorage.getItem('userEmail')).toBe('test@example.com')
            expect(localStorage.getItem('mustChangePassword')).toBe('false')
        })

        it('déclenche l\'événement token-changed après connexion', async () => {
            const mockResponse = {
                data: {
                    token: 'fake-jwt-token',
                    mustChangePassword: false
                }
            }

            axios.post.mockResolvedValue(mockResponse)
            jwtDecode.mockReturnValue({
                roles: ['ROLE_USER'],
                username: 'test@example.com'
            })

            const dispatchSpy = vi.spyOn(window, 'dispatchEvent')

            await wrapper.find('input[type="email"]').setValue('test@example.com')
            await wrapper.find('input[type="password"]').setValue('password123')

            await wrapper.find('form').trigger('submit.prevent')

            expect(dispatchSpy).toHaveBeenCalledWith(expect.objectContaining({
                type: 'token-changed'
            }))
        })

        it('déclenche l\'événement show-splash après connexion', async () => {
            const mockResponse = {
                data: {
                    token: 'fake-jwt-token',
                    mustChangePassword: false
                }
            }

            axios.post.mockResolvedValue(mockResponse)
            jwtDecode.mockReturnValue({
                roles: ['ROLE_USER'],
                username: 'test@example.com'
            })

            const dispatchSpy = vi.spyOn(window, 'dispatchEvent')

            await wrapper.find('input[type="email"]').setValue('test@example.com')
            await wrapper.find('input[type="password"]').setValue('password123')

            await wrapper.find('form').trigger('submit.prevent')

            expect(dispatchSpy).toHaveBeenCalledWith(expect.objectContaining({
                type: 'show-splash'
            }))
        })
    })

    describe('Redirection après connexion', () => {
        beforeEach(() => {
            wrapper = createWrapper()
        })

        it('redirige vers /admin pour ROLE_ADMIN', async () => {
            const mockResponse = {
                data: {
                    token: 'fake-jwt-token',
                    mustChangePassword: false
                }
            }

            axios.post.mockResolvedValue(mockResponse)
            jwtDecode.mockReturnValue({
                roles: ['ROLE_ADMIN'],
                username: 'admin@example.com'
            })

            const pushSpy = vi.spyOn(router, 'push')

            await wrapper.find('input[type="email"]').setValue('admin@example.com')
            await wrapper.find('input[type="password"]').setValue('password123')

            await wrapper.find('form').trigger('submit.prevent')

            // Attendre le setTimeout
            await new Promise(resolve => setTimeout(resolve, 150))

            expect(pushSpy).toHaveBeenCalledWith('/admin')
        })

        it('redirige vers /admin pour ROLE_CHEF_PROJET', async () => {
            const mockResponse = {
                data: {
                    token: 'fake-jwt-token',
                    mustChangePassword: false
                }
            }

            axios.post.mockResolvedValue(mockResponse)
            jwtDecode.mockReturnValue({
                roles: ['ROLE_CHEF_PROJET'],
                username: 'chef@example.com'
            })

            const pushSpy = vi.spyOn(router, 'push')

            await wrapper.find('input[type="email"]').setValue('chef@example.com')
            await wrapper.find('input[type="password"]').setValue('password123')

            await wrapper.find('form').trigger('submit.prevent')

            // Attendre le setTimeout
            await new Promise(resolve => setTimeout(resolve, 150))

            expect(pushSpy).toHaveBeenCalledWith('/admin')
        })

        it('redirige vers /admin pour ROLE_MANAGER', async () => {
            const mockResponse = {
                data: {
                    token: 'fake-jwt-token',
                    mustChangePassword: false
                }
            }

            axios.post.mockResolvedValue(mockResponse)
            jwtDecode.mockReturnValue({
                roles: ['ROLE_MANAGER'],
                username: 'manager@example.com'
            })

            const pushSpy = vi.spyOn(router, 'push')

            await wrapper.find('input[type="email"]').setValue('manager@example.com')
            await wrapper.find('input[type="password"]').setValue('password123')

            await wrapper.find('form').trigger('submit.prevent')

            // Attendre le setTimeout
            await new Promise(resolve => setTimeout(resolve, 150))

            expect(pushSpy).toHaveBeenCalledWith('/admin')
        })

        it('redirige vers /dashboard pour ROLE_USER', async () => {
            const mockResponse = {
                data: {
                    token: 'fake-jwt-token',
                    mustChangePassword: false
                }
            }

            axios.post.mockResolvedValue(mockResponse)
            jwtDecode.mockReturnValue({
                roles: ['ROLE_USER'],
                username: 'user@example.com'
            })

            const pushSpy = vi.spyOn(router, 'push')

            await wrapper.find('input[type="email"]').setValue('user@example.com')
            await wrapper.find('input[type="password"]').setValue('password123')

            await wrapper.find('form').trigger('submit.prevent')

            // Attendre le setTimeout
            await new Promise(resolve => setTimeout(resolve, 150))

            expect(pushSpy).toHaveBeenCalledWith('/dashboard')
        })
    })

    describe('Gestion des erreurs', () => {
        beforeEach(() => {
            wrapper = createWrapper()
        })

        it('affiche un message d\'erreur en cas d\'échec de connexion', async () => {
            axios.post.mockRejectedValue(new Error('Identifiants invalides'))

            await wrapper.find('input[type="email"]').setValue('test@example.com')
            await wrapper.find('input[type="password"]').setValue('wrongpassword')

            await wrapper.find('form').trigger('submit.prevent')

            await wrapper.vm.$nextTick()

            const errorMessage = wrapper.find('.alert-error')
            expect(errorMessage.exists()).toBe(true)
            expect(errorMessage.text()).toBe('Identifiants invalides')
        })

        it('efface le message d\'erreur lors d\'une nouvelle tentative', async () => {
            // Premier échec
            axios.post.mockRejectedValueOnce(new Error('Identifiants invalides'))

            await wrapper.find('input[type="email"]').setValue('test@example.com')
            await wrapper.find('input[type="password"]').setValue('wrongpassword')

            await wrapper.find('form').trigger('submit.prevent')
            await wrapper.vm.$nextTick()

            expect(wrapper.find('.alert-error').exists()).toBe(true)

            // Deuxième tentative réussie
            const mockResponse = {
                data: {
                    token: 'fake-jwt-token',
                    mustChangePassword: false
                }
            }

            axios.post.mockResolvedValue(mockResponse)
            jwtDecode.mockReturnValue({
                roles: ['ROLE_USER'],
                username: 'test@example.com'
            })

            await wrapper.find('form').trigger('submit.prevent')
            await wrapper.vm.$nextTick()

            expect(wrapper.find('.alert-error').exists()).toBe(false)
        })
    })

    describe('Gestion du changement de mot de passe obligatoire', () => {
        beforeEach(() => {
            wrapper = createWrapper()
        })

        it('gère mustChangePassword true', async () => {
            const mockResponse = {
                data: {
                    token: 'fake-jwt-token',
                    mustChangePassword: true
                }
            }

            axios.post.mockResolvedValue(mockResponse)
            jwtDecode.mockReturnValue({
                roles: ['ROLE_USER'],
                username: 'test@example.com'
            })

            await wrapper.find('input[type="email"]').setValue('test@example.com')
            await wrapper.find('input[type="password"]').setValue('password123')

            await wrapper.find('form').trigger('submit.prevent')

            expect(localStorage.getItem('mustChangePassword')).toBe('true')
        })

        it('gère mustChangePassword false', async () => {
            const mockResponse = {
                data: {
                    token: 'fake-jwt-token',
                    mustChangePassword: false
                }
            }

            axios.post.mockResolvedValue(mockResponse)
            jwtDecode.mockReturnValue({
                roles: ['ROLE_USER'],
                username: 'test@example.com'
            })

            await wrapper.find('input[type="email"]').setValue('test@example.com')
            await wrapper.find('input[type="password"]').setValue('password123')

            await wrapper.find('form').trigger('submit.prevent')

            expect(localStorage.getItem('mustChangePassword')).toBe('false')
        })
    })

    describe('Validation du formulaire', () => {
        it('empêche la soumission avec des champs vides', async () => {
            wrapper = createWrapper()

            const form = wrapper.find('form')
            await form.trigger('submit.prevent')

            // Le formulaire HTML devrait empêcher la soumission
            expect(axios.post).not.toHaveBeenCalled()
        })

        it('affiche un message d\'erreur pour des champs vides côté client', async () => {
            wrapper = createWrapper()

            // Remplir seulement l'email
            await wrapper.find('input[type="email"]').setValue('test@example.com')
            await wrapper.find('input[type="password"]').setValue('')

            await wrapper.find('form').trigger('submit.prevent')

            const errorMessage = wrapper.find('.alert-error')
            expect(errorMessage.exists()).toBe(true)
            expect(errorMessage.text()).toBe('Veuillez remplir tous les champs')
            expect(axios.post).not.toHaveBeenCalled()
        })

        it('affiche un message d\'erreur pour email vide côté client', async () => {
            wrapper = createWrapper()

            // Remplir seulement le mot de passe
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
