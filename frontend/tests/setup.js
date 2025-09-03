import { vi } from 'vitest'

// Mock des APIs du navigateur
Object.defineProperty(window, 'localStorage', {
    value: {
        getItem: vi.fn(),
        setItem: vi.fn(),
        removeItem: vi.fn(),
        clear: vi.fn(),
    },
    writable: true,
})

Object.defineProperty(window, 'sessionStorage', {
    value: {
        getItem: vi.fn(),
        setItem: vi.fn(),
        removeItem: vi.fn(),
        clear: vi.fn(),
    },
    writable: true,
})

// Mock des événements
Object.defineProperty(window, 'dispatchEvent', {
    value: vi.fn(),
    writable: true,
})

Object.defineProperty(window, 'addEventListener', {
    value: vi.fn(),
    writable: true,
})

Object.defineProperty(window, 'removeEventListener', {
    value: vi.fn(),
    writable: true,
})

// Mock de getComputedStyle
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
    })),
    writable: true,
})

// Mock de setTimeout et clearTimeout
Object.defineProperty(global, 'setTimeout', {
    value: vi.fn((callback, delay) => {
        return setTimeout(callback, delay)
    }),
    writable: true,
})

Object.defineProperty(global, 'clearTimeout', {
    value: vi.fn((id) => {
        return clearTimeout(id)
    }),
    writable: true,
})

// Mock de console pour éviter les logs dans les tests
global.console = {
    ...console,
    log: vi.fn(),
    debug: vi.fn(),
    info: vi.fn(),
    warn: vi.fn(),
    error: vi.fn(),
}
