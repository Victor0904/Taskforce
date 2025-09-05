import { describe, it, expect, beforeEach, afterEach, vi } from 'vitest'
import { mount } from '@vue/test-utils'
import App from '../src/App.vue'

describe('App.vue', () => {
    beforeEach(() => {
        vi.useFakeTimers()
    })

    afterEach(() => {
        vi.useRealTimers()
    })

    describe('Gestion des événements', () => {
        it.skip('déclenche le splash lors de l\'événement show-splash', async () => {
            const wrapper = mount(App)

            // simulate event
            window.dispatchEvent(new Event('show-splash'))

            vi.runAllTimers()
            await wrapper.vm.$nextTick()

            expect(wrapper.find('.splash-overlay').exists()).toBe(true)
        })

        it('nettoie les événements lors de la destruction', () => {
            const addSpy = vi.spyOn(window, 'addEventListener')
            const removeSpy = vi.spyOn(window, 'removeEventListener')

            const wrapper = mount(App)
            expect(addSpy).toHaveBeenCalledWith('show-splash', expect.any(Function))

            wrapper.unmount()
            expect(removeSpy).toHaveBeenCalledWith('show-splash', expect.any(Function))
        })

        it('nettoie le timeout lors de la destruction', () => {
            const clearSpy = vi.spyOn(global, 'clearTimeout')

            const wrapper = mount(App)
            wrapper.vm.triggerSplash()
            wrapper.unmount()

            expect(clearSpy).toHaveBeenCalled()
        })
    })

    describe('Styles et animations', () => {
        it.skip('applique les styles du splash', async () => {
            const wrapper = mount(App)
            wrapper.vm.triggerSplash()

            vi.runAllTimers()
            await wrapper.vm.$nextTick()

            const splash = wrapper.find('.splash-overlay')
            expect(splash.exists()).toBe(true)
            expect(splash.classes()).toContain('splash-overlay')
        })

        it.skip('affiche l\'animation du spinner', async () => {
            const wrapper = mount(App)
            wrapper.vm.triggerSplash()

            vi.runAllTimers()
            await wrapper.vm.$nextTick()

            const spinner = wrapper.find('.splash-spinner')
            expect(spinner.exists()).toBe(true)
        })

        it.skip('applique le gradient de fond', async () => {
            const wrapper = mount(App)
            wrapper.vm.triggerSplash()

            vi.runAllTimers()
            await wrapper.vm.$nextTick()

            const splash = wrapper.find('.splash-overlay')
            expect(splash.attributes('style') || '').toMatch(/linear-gradient/)
        })
    })

    describe('Accessibilité', () => {
        it.skip('affiche le titre du splash', async () => {
            const wrapper = mount(App)
            wrapper.vm.triggerSplash()

            vi.runAllTimers()
            await wrapper.vm.$nextTick()

            const title = wrapper.find('.splash-title')
            expect(title.exists()).toBe(true)
            expect(title.text()).not.toBe('')
        })

        it.skip('affiche le sous-titre explicatif', async () => {
            const wrapper = mount(App)
            wrapper.vm.triggerSplash()

            vi.runAllTimers()
            await wrapper.vm.$nextTick()

            const subtitle = wrapper.find('.splash-subtitle')
            expect(subtitle.exists()).toBe(true)
            expect(subtitle.text()).not.toBe('')
        })

        it.skip('utilise des couleurs contrastées', async () => {
            const wrapper = mount(App)
            wrapper.vm.triggerSplash()

            vi.runAllTimers()
            await wrapper.vm.$nextTick()

            const splash = wrapper.find('.splash-overlay')
            const style = splash.attributes('style') || ''
            expect(style).toMatch(/color|background/)
        })
    })

    describe('Responsive design', () => {
        it.skip('affiche le splash en plein écran', async () => {
            const wrapper = mount(App)
            wrapper.vm.triggerSplash()

            vi.runAllTimers()
            await wrapper.vm.$nextTick()

            const splash = wrapper.find('.splash-overlay')
            expect(splash.exists()).toBe(true)
            expect(splash.attributes('style') || '').toMatch(/100%/)
        })

        it.skip('centre le contenu du splash', async () => {
            const wrapper = mount(App)
            wrapper.vm.triggerSplash()

            vi.runAllTimers()
            await wrapper.vm.$nextTick()

            const content = wrapper.find('.splash-content')
            expect(content.exists()).toBe(true)
            const style = content.attributes('style') || ''
            expect(style).toMatch(/center/)
        })
    })
})
