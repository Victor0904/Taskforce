import { describe, it, expect, beforeEach, beforeAll, afterAll, vi } from 'vitest'
import axios from 'axios'
import api from '../src/api.js'

// 🟢 Mock d'axios avec une instance factice
const mockAxiosInstance = {
    defaults: { baseURL: '', headers: {} },
    interceptors: { request: { use: vi.fn(), handlers: [] } },
    get: vi.fn(),
    post: vi.fn(),
    put: vi.fn(),
    delete: vi.fn(),
}

vi.mock('axios', () => {
    return {
        default: {
            create: vi.fn(() => mockAxiosInstance),
        },
    }
})

// 🟢 Polyfill de localStorage pour Node.js
beforeAll(() => {
    const store = {}
    global.localStorage = {
        getItem: (key) => (key in store ? store[key] : null),
        setItem: (key, val) => (store[key] = val.toString()),
        removeItem: (key) => delete store[key],
        clear: () => Object.keys(store).forEach((k) => delete store[k]),
    }
})

afterAll(() => {
    delete global.localStorage
})

describe('API Configuration', () => {
    beforeEach(() => {
        vi.clearAllMocks()
        localStorage.clear()
        mockAxiosInstance.get.mockReset()
        mockAxiosInstance.post.mockReset()
        mockAxiosInstance.put.mockReset()
        mockAxiosInstance.delete.mockReset()
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

            const config = { headers: {} }
            const interceptor = api.interceptors.request.handlers[0]
            const result = interceptor.fulfilled(config)

            expect(result.headers.Authorization).toBe(`Bearer ${token}`)
        })

        it('ne modifie pas la config quand pas de token', () => {
            localStorage.removeItem('token')

            const config = { headers: {} }
            const interceptor = api.interceptors.request.handlers[0]
            const result = interceptor.fulfilled(config)

            expect(result.headers.Authorization).toBeUndefined()
        })

        it('préserve les headers existants', () => {
            const token = 'fake-jwt-token'
            localStorage.setItem('token', token)

            const config = { headers: { 'Custom-Header': 'custom-value' } }
            const interceptor = api.interceptors.request.handlers[0]
            const result = interceptor.fulfilled(config)

            expect(result.headers.Authorization).toBe(`Bearer ${token}`)
            expect(result.headers['Custom-Header']).toBe('custom-value')
        })
    })

    describe('Intégration avec axios', () => {
        it('crée bien une instance axios avec create', () => {
            expect(axios.create).toHaveBeenCalledWith(
                expect.objectContaining({
                    baseURL: 'http://127.0.0.1:8000/api',
                    headers: expect.any(Object),
                })
            )
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
            mockAxiosInstance.get.mockResolvedValue(mockResponse)

            const response = await api.get('/test')

            expect(mockAxiosInstance.get).toHaveBeenCalledWith('/test')
            expect(response).toEqual(mockResponse)
        })

        it('fonctionne avec les requêtes POST', async () => {
            const token = 'fake-token'
            localStorage.setItem('token', token)

            const mockData = { name: 'test' }
            const mockResponse = { data: { id: 1, ...mockData } }
            mockAxiosInstance.post.mockResolvedValue(mockResponse)

            const response = await api.post('/test', mockData)

            expect(mockAxiosInstance.post).toHaveBeenCalledWith('/test', mockData)
            expect(response).toEqual(mockResponse)
        })

        it('fonctionne avec les requêtes PUT', async () => {
            const token = 'fake-token'
            localStorage.setItem('token', token)

            const mockData = { name: 'updated' }
            const mockResponse = { data: { id: 1, ...mockData } }
            mockAxiosInstance.put.mockResolvedValue(mockResponse)

            const response = await api.put('/test/1', mockData)

            expect(mockAxiosInstance.put).toHaveBeenCalledWith('/test/1', mockData)
            expect(response).toEqual(mockResponse)
        })

        it('fonctionne avec les requêtes DELETE', async () => {
            const token = 'fake-token'
            localStorage.setItem('token', token)

            const mockResponse = { data: { message: 'deleted' } }
            mockAxiosInstance.delete.mockResolvedValue(mockResponse)

            const response = await api.delete('/test/1')

            expect(mockAxiosInstance.delete).toHaveBeenCalledWith('/test/1')
            expect(response).toEqual(mockResponse)
        })
    })

    describe('Gestion des erreurs', () => {
        it('gère les erreurs de requête', async () => {
            const error = new Error('Network Error')
            mockAxiosInstance.get.mockRejectedValue(error)

            await expect(api.get('/test')).rejects.toThrow('Network Error')
        })

        it('gère les erreurs 401 (non autorisé)', async () => {
            const error = { response: { status: 401, data: { message: 'Token expired' } } }
            mockAxiosInstance.get.mockRejectedValue(error)

            await expect(api.get('/test')).rejects.toEqual(error)
        })

        it('gère les erreurs 403 (interdit)', async () => {
            const error = { response: { status: 403, data: { message: 'Access denied' } } }
            mockAxiosInstance.get.mockRejectedValue(error)

            await expect(api.get('/test')).rejects.toEqual(error)
        })
    })

    describe('Compatibilité', () => {
        it('gère les tokens malformés', () => {
            localStorage.setItem('token', 'invalid-token-format')

            const config = { headers: {} }
            const interceptor = api.interceptors.request.handlers[0]
            const result = interceptor.fulfilled(config)

            expect(result.headers.Authorization).toBe('Bearer invalid-token-format')
        })
    })
})
