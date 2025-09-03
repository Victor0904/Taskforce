import { describe, it, expect, beforeEach, vi } from 'vitest'
import axios from 'axios'
import api from '../src/api.js'

// Mock axios
vi.mock('axios')

describe('API Configuration', () => {
    beforeEach(() => {
        vi.clearAllMocks()
        localStorage.clear()
    })

    describe('Configuration de base', () => {
        it('configure correctement l\'URL de base', () => {
            expect(api.defaults.baseURL).toBe('http://127.0.0.1:8000/api')
        })

        it('configure correctement les headers par défaut', () => {
            expect(api.defaults.headers['Content-Type']).toBe('application/json')
        })
    })

    describe('Intercepteur de requête', () => {
        it('ajoute le token d\'autorisation quand disponible', () => {
            const token = 'fake-jwt-token'
            localStorage.setItem('token', token)

            const config = {
                headers: {}
            }

            // Simuler l'intercepteur
            const interceptor = api.interceptors.request.handlers[0]
            const result = interceptor.fulfilled(config)

            expect(result.headers.Authorization).toBe(`Bearer ${token}`)
        })

        it('ne modifie pas la config quand pas de token', () => {
            localStorage.removeItem('token')

            const config = {
                headers: {}
            }

            // Simuler l'intercepteur
            const interceptor = api.interceptors.request.handlers[0]
            const result = interceptor.fulfilled(config)

            expect(result.headers.Authorization).toBeUndefined()
        })

        it('préserve les headers existants', () => {
            const token = 'fake-jwt-token'
            localStorage.setItem('token', token)

            const config = {
                headers: {
                    'Custom-Header': 'custom-value'
                }
            }

            // Simuler l'intercepteur
            const interceptor = api.interceptors.request.handlers[0]
            const result = interceptor.fulfilled(config)

            expect(result.headers.Authorization).toBe(`Bearer ${token}`)
            expect(result.headers['Custom-Header']).toBe('custom-value')
        })
    })

    describe('Gestion des tokens', () => {
        it('récupère le token depuis localStorage', () => {
            const token = 'test-token-123'
            localStorage.setItem('token', token)

            const config = {
                headers: {}
            }

            const interceptor = api.interceptors.request.handlers[0]
            const result = interceptor.fulfilled(config)

            expect(result.headers.Authorization).toBe(`Bearer ${token}`)
        })

        it('gère les tokens null', () => {
            localStorage.setItem('token', 'null')

            const config = {
                headers: {}
            }

            const interceptor = api.interceptors.request.handlers[0]
            const result = interceptor.fulfilled(config)

            expect(result.headers.Authorization).toBe('Bearer null')
        })

        it('gère les tokens vides', () => {
            localStorage.setItem('token', '')

            const config = {
                headers: {}
            }

            const interceptor = api.interceptors.request.handlers[0]
            const result = interceptor.fulfilled(config)

            expect(result.headers.Authorization).toBe('Bearer ')
        })
    })

    describe('Intégration avec axios', () => {
        it('utilise la même instance axios', () => {
            expect(api).toBe(axios.create())
        })

        it('préserve la fonctionnalité axios originale', () => {
            expect(typeof api.get).toBe('function')
            expect(typeof api.post).toBe('function')
            expect(typeof api.put).toBe('function')
            expect(typeof api.delete).toBe('function')
        })
    })

    describe('Scénarios d\'utilisation', () => {
        it('fonctionne avec les requêtes GET', async () => {
            const token = 'fake-token'
            localStorage.setItem('token', token)

            const mockResponse = { data: { message: 'success' } }
            axios.get.mockResolvedValue(mockResponse)

            const response = await api.get('/test')

            expect(axios.get).toHaveBeenCalledWith('/test', {
                headers: {
                    Authorization: `Bearer ${token}`
                }
            })
            expect(response).toEqual(mockResponse)
        })

        it('fonctionne avec les requêtes POST', async () => {
            const token = 'fake-token'
            localStorage.setItem('token', token)

            const mockData = { name: 'test' }
            const mockResponse = { data: { id: 1, ...mockData } }
            axios.post.mockResolvedValue(mockResponse)

            const response = await api.post('/test', mockData)

            expect(axios.post).toHaveBeenCalledWith('/test', mockData, {
                headers: {
                    Authorization: `Bearer ${token}`
                }
            })
            expect(response).toEqual(mockResponse)
        })

        it('fonctionne avec les requêtes PUT', async () => {
            const token = 'fake-token'
            localStorage.setItem('token', token)

            const mockData = { name: 'updated' }
            const mockResponse = { data: { id: 1, ...mockData } }
            axios.put.mockResolvedValue(mockResponse)

            const response = await api.put('/test/1', mockData)

            expect(axios.put).toHaveBeenCalledWith('/test/1', mockData, {
                headers: {
                    Authorization: `Bearer ${token}`
                }
            })
            expect(response).toEqual(mockResponse)
        })

        it('fonctionne avec les requêtes DELETE', async () => {
            const token = 'fake-token'
            localStorage.setItem('token', token)

            const mockResponse = { data: { message: 'deleted' } }
            axios.delete.mockResolvedValue(mockResponse)

            const response = await api.delete('/test/1')

            expect(axios.delete).toHaveBeenCalledWith('/test/1', {
                headers: {
                    Authorization: `Bearer ${token}`
                }
            })
            expect(response).toEqual(mockResponse)
        })
    })

    describe('Gestion des erreurs', () => {
        it('gère les erreurs de requête', async () => {
            const token = 'fake-token'
            localStorage.setItem('token', token)

            const error = new Error('Network Error')
            axios.get.mockRejectedValue(error)

            await expect(api.get('/test')).rejects.toThrow('Network Error')
        })

        it('gère les erreurs 401 (non autorisé)', async () => {
            const token = 'expired-token'
            localStorage.setItem('token', token)

            const error = {
                response: {
                    status: 401,
                    data: { message: 'Token expired' }
                }
            }
            axios.get.mockRejectedValue(error)

            await expect(api.get('/test')).rejects.toEqual(error)
        })

        it('gère les erreurs 403 (interdit)', async () => {
            const token = 'insufficient-token'
            localStorage.setItem('token', token)

            const error = {
                response: {
                    status: 403,
                    data: { message: 'Access denied' }
                }
            }
            axios.get.mockRejectedValue(error)

            await expect(api.get('/test')).rejects.toEqual(error)
        })
    })

    describe('Performance et optimisation', () => {
        it('ne modifie pas la config si pas de token', () => {
            localStorage.removeItem('token')

            const originalConfig = {
                headers: {
                    'Content-Type': 'application/json'
                },
                timeout: 5000
            }

            const interceptor = api.interceptors.request.handlers[0]
            const result = interceptor.fulfilled(originalConfig)

            expect(result).toEqual(originalConfig)
        })

        it('ajoute seulement le header Authorization', () => {
            const token = 'fake-token'
            localStorage.setItem('token', token)

            const config = {
                headers: {
                    'Content-Type': 'application/json'
                },
                timeout: 5000,
                data: { test: 'data' }
            }

            const interceptor = api.interceptors.request.handlers[0]
            const result = interceptor.fulfilled(config)

            expect(result.headers.Authorization).toBe(`Bearer ${token}`)
            expect(result.headers['Content-Type']).toBe('application/json')
            expect(result.timeout).toBe(5000)
            expect(result.data).toEqual({ test: 'data' })
        })
    })

    describe('Compatibilité', () => {
        it('fonctionne avec les anciens navigateurs', () => {
            // Simuler un navigateur sans localStorage
            const originalLocalStorage = window.localStorage
            delete window.localStorage

            const config = {
                headers: {}
            }

            const interceptor = api.interceptors.request.handlers[0]

            // Ne devrait pas planter
            expect(() => {
                interceptor.fulfilled(config)
            }).not.toThrow()

            // Restaurer localStorage
            window.localStorage = originalLocalStorage
        })

        it('gère les tokens malformés', () => {
            localStorage.setItem('token', 'invalid-token-format')

            const config = {
                headers: {}
            }

            const interceptor = api.interceptors.request.handlers[0]
            const result = interceptor.fulfilled(config)

            expect(result.headers.Authorization).toBe('Bearer invalid-token-format')
        })
    })
})
