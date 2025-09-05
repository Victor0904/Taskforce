import { vi } from 'vitest'

// ========================================
// 1. CORRECTION : Timers sans récursion infinie
// ========================================

// Sauvegarder les vrais timers AVANT toute manipulation
const realSetTimeout = global.setTimeout.bind(global)
const realClearTimeout = global.clearTimeout.bind(global)

// Option A : espionner seulement (pas de redéfinition récursive)
vi.spyOn(global, 'setTimeout')
vi.spyOn(global, 'clearTimeout')

// Si on veut une implémentation custom, utiliser les "réels"
Object.defineProperty(global, 'setTimeout', {
    value: vi.fn((cb, delay) => realSetTimeout(cb, delay)),
    writable: true,
})

Object.defineProperty(global, 'clearTimeout', {
    value: vi.fn((id) => realClearTimeout(id)),
    writable: true,
})

// ========================================
// 2. CORRECTION : Mock axios complet avec create + interceptors
// ========================================

vi.mock('axios', () => {
    const mock = {
        get: vi.fn(),
        post: vi.fn(),
        put: vi.fn(),
        delete: vi.fn(),
        patch: vi.fn(),
        // Interceptors attendus par src/api.js
        interceptors: {
            request: {
                use: vi.fn(),
                eject: vi.fn(),
                handlers: []
            },
            response: {
                use: vi.fn(),
                eject: vi.fn(),
                handlers: []
            },
        },
        // axios.create doit retourner "un client" qui a les mêmes props
        create: vi.fn(function () {
            return {
                ...mock,
                // Chaque instance a ses propres interceptors
                interceptors: {
                    request: {
                        use: vi.fn(),
                        eject: vi.fn(),
                        handlers: []
                    },
                    response: {
                        use: vi.fn(),
                        eject: vi.fn(),
                        handlers: []
                    },
                }
            }
        }),
        // Méthodes statiques
        defaults: {
            headers: {
                common: {},
                get: {},
                post: {},
                put: {},
                delete: {},
                patch: {}
            }
        }
    }
    return { default: mock }
})

// ========================================
// 3. CORRECTION : Mock localStorage réaliste avec événements
// ========================================

class LocalStorageMock {
    constructor() {
        this.store = {}
    }

    getItem(key) {
        return this.store[key] ?? null
    }

    setItem(key, value) {
        this.store[key] = String(value)
        // Simuler l'événement "storage"
        const event = new Event('storage')
        event.key = key
        event.newValue = this.store[key]
        event.oldValue = null
        event.storageArea = this
        window.dispatchEvent(event)

        // Simuler l'événement custom "token-changed" si c'est le token
        if (key === 'token') {
            const customEvent = new CustomEvent('token-changed', {
                detail: { token: value }
            })
            window.dispatchEvent(customEvent)
        }
    }

    removeItem(key) {
        const oldValue = this.store[key]
        delete this.store[key]

        const event = new Event('storage')
        event.key = key
        event.newValue = null
        event.oldValue = oldValue
        event.storageArea = this
        window.dispatchEvent(event)

        // Simuler l'événement custom "token-changed" si c'est le token
        if (key === 'token') {
            const customEvent = new CustomEvent('token-changed', {
                detail: { token: null }
            })
            window.dispatchEvent(customEvent)
        }
    }

    clear() {
        Object.keys(this.store).forEach(k => this.removeItem(k))
    }

    get length() {
        return Object.keys(this.store).length
    }

    key(index) {
        const keys = Object.keys(this.store)
        return keys[index] || null
    }
}

// Mock localStorage avec implémentation réaliste
Object.defineProperty(window, 'localStorage', {
    value: new LocalStorageMock(),
    writable: false,
})

// Mock sessionStorage similaire
class SessionStorageMock {
    constructor() {
        this.store = {}
    }

    getItem(key) {
        return this.store[key] ?? null
    }

    setItem(key, value) {
        this.store[key] = String(value)
        const event = new Event('storage')
        event.key = key
        event.newValue = this.store[key]
        event.oldValue = null
        event.storageArea = this
        window.dispatchEvent(event)
    }

    removeItem(key) {
        const oldValue = this.store[key]
        delete this.store[key]

        const event = new Event('storage')
        event.key = key
        event.newValue = null
        event.oldValue = oldValue
        event.storageArea = this
        window.dispatchEvent(event)
    }

    clear() {
        Object.keys(this.store).forEach(k => this.removeItem(k))
    }

    get length() {
        return Object.keys(this.store).length
    }

    key(index) {
        const keys = Object.keys(this.store)
        return keys[index] || null
    }
}

Object.defineProperty(window, 'sessionStorage', {
    value: new SessionStorageMock(),
    writable: false,
})

// ========================================
// 4. CORRECTION : Événements réalistes
// ========================================

// Event helpers
if (typeof window.CustomEvent !== 'function') {
    global.CustomEvent = class CustomEvent extends Event {
        constructor(name, params = {}) {
            super(name, params)
            this.detail = params.detail
        }
    }
}

// Mock des événements avec implémentation réaliste
const eventListeners = new Map()

Object.defineProperty(window, 'addEventListener', {
    value: vi.fn((event, handler, options) => {
        if (!eventListeners.has(event)) {
            eventListeners.set(event, new Set())
        }
        eventListeners.get(event).add(handler)
    }),
    writable: true,
})

Object.defineProperty(window, 'removeEventListener', {
    value: vi.fn((event, handler) => {
        if (eventListeners.has(event)) {
            eventListeners.get(event).delete(handler)
        }
    }),
    writable: true,
})

Object.defineProperty(window, 'dispatchEvent', {
    value: vi.fn((event) => {
        if (eventListeners.has(event.type)) {
            eventListeners.get(event.type).forEach(handler => {
                try {
                    handler(event)
                } catch (error) {
                    console.error('Error in event handler:', error)
                }
            })
        }
        return true
    }),
    writable: true,
})

// ========================================
// 5. Mock de getComputedStyle
// ========================================

Object.defineProperty(window, 'getComputedStyle', {
    value: vi.fn(() => ({
        position: 'fixed',
        top: '0px',
        left: '0px',
        width: '100%',
        height: '100%',
        display: 'flex',
        alignItems: 'center',
        justifyContent: 'center',
        color: 'white',
        background: 'linear-gradient(135deg, #667eea 0%, #764ba2 100%)',
        fontSize: '16px',
        fontFamily: 'system-ui, sans-serif',
        lineHeight: '1.5',
        margin: '0',
        padding: '0',
        border: 'none',
        outline: 'none',
        boxSizing: 'border-box',
        zIndex: '1000',
        opacity: '1',
        visibility: 'visible',
        transform: 'none',
        transition: 'none',
    })),
    writable: true,
})

// ========================================
// 6. Mock de console pour éviter les logs dans les tests
// ========================================

global.console = {
    ...console,
    log: vi.fn(),
    debug: vi.fn(),
    info: vi.fn(),
    warn: vi.fn(),
    error: vi.fn(),
}

// ========================================
// 7. Mock de fetch si nécessaire
// ========================================

global.fetch = vi.fn()

// ========================================
// 8. Mock de URL et URLSearchParams
// ========================================

if (typeof global.URL === 'undefined') {
    global.URL = class URL {
        constructor(url, base) {
            this.href = url
            this.origin = 'http://localhost'
            this.protocol = 'http:'
            this.host = 'localhost'
            this.hostname = 'localhost'
            this.port = ''
            this.pathname = '/'
            this.search = ''
            this.hash = ''
        }
    }
}

if (typeof global.URLSearchParams === 'undefined') {
    global.URLSearchParams = class URLSearchParams {
        constructor(init) {
            this.params = new Map()
            if (init) {
                if (typeof init === 'string') {
                    init.split('&').forEach(pair => {
                        const [key, value] = pair.split('=')
                        if (key) this.params.set(key, value || '')
                    })
                }
            }
        }

        get(name) { return this.params.get(name) }
        set(name, value) { this.params.set(name, value) }
        has(name) { return this.params.has(name) }
        delete(name) { this.params.delete(name) }
        toString() {
            return Array.from(this.params.entries())
                .map(([key, value]) => `${key}=${value}`)
                .join('&')
        }
    }
}

// ========================================
// 9. Mock de ResizeObserver
// ========================================

global.ResizeObserver = vi.fn().mockImplementation(() => ({
    observe: vi.fn(),
    unobserve: vi.fn(),
    disconnect: vi.fn(),
}))

// ========================================
// 10. Mock de IntersectionObserver
// ========================================

global.IntersectionObserver = vi.fn().mockImplementation(() => ({
    observe: vi.fn(),
    unobserve: vi.fn(),
    disconnect: vi.fn(),
}))

// ========================================
// 11. Mock de matchMedia
// ========================================

Object.defineProperty(window, 'matchMedia', {
    writable: true,
    value: vi.fn().mockImplementation(query => ({
        matches: false,
        media: query,
        onchange: null,
        addListener: vi.fn(),
        removeListener: vi.fn(),
        addEventListener: vi.fn(),
        removeEventListener: vi.fn(),
        dispatchEvent: vi.fn(),
    })),
})

// ========================================
// 12. Configuration des fausses horloges (optionnel)
// ========================================

// Pour utiliser les fausses horloges dans certains tests :
// vi.useFakeTimers()
// vi.advanceTimersByTime(5000)
// vi.useRealTimers()

console.log('✅ Tests setup completed - All mocks configured correctly')