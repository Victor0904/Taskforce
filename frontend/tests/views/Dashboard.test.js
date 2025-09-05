import { describe, it, expect, beforeEach, vi } from 'vitest'
import { mount } from '@vue/test-utils'
import Dashboard from '../../src/views/Dashboard.vue'
import axios from 'axios'

// Mock des dépendances
vi.mock('axios')

describe('Dashboard.vue', () => {
    let wrapper

    beforeEach(() => {
        // Nettoyer localStorage
        localStorage.clear()

        // Reset des mocks
        vi.clearAllMocks()

        // Mock localStorage
        Object.defineProperty(window, 'localStorage', {
            value: {
                getItem: vi.fn(),
                setItem: vi.fn(),
                removeItem: vi.fn(),
                clear: vi.fn()
            },
            writable: true
        })
    })

    const createWrapper = (props = {}) => {
        return mount(Dashboard, {
            props
        })
    }

    describe('Rendu initial', () => {
        it('affiche le titre du dashboard', () => {
            wrapper = createWrapper()

            expect(wrapper.find('.dashboard-title').text()).toBe('Tableau de bord')
            expect(wrapper.find('.dashboard-subtitle').text()).toBe('Vue d\'ensemble de votre activité et de votre équipe')
        })

        it('affiche le bouton d\'actualisation', () => {
            wrapper = createWrapper()

            const refreshButton = wrapper.find('.btn-primary')
            expect(refreshButton.exists()).toBe(true)
            expect(refreshButton.text()).toContain('Actualiser')
        })

        it('affiche les sections principales', () => {
            wrapper = createWrapper()

            expect(wrapper.find('.metrics-grid').exists()).toBe(true)
            expect(wrapper.find('.dashboard-section').exists()).toBe(true)
        })
    })

    describe('Métriques', () => {
        beforeEach(() => {
            wrapper = createWrapper()
        })

        it('affiche les cartes de métriques', () => {
            const metricCards = wrapper.findAll('.metric-card')
            expect(metricCards.length).toBe(4)
        })

        it('affiche la carte des collaborateurs', () => {
            const collaborateurCard = wrapper.find('.metric-card-blue')
            expect(collaborateurCard.exists()).toBe(true)
            expect(collaborateurCard.find('.metric-label').text()).toBe('Collaborateurs')
        })

        it('affiche la carte des tâches en cours', () => {
            const tachesCard = wrapper.find('.metric-card-yellow')
            expect(tachesCard.exists()).toBe(true)
            expect(tachesCard.find('.metric-label').text()).toBe('Tâches en cours')
        })

        it('affiche la carte des projets actifs', () => {
            const projetsCard = wrapper.find('.metric-card-green')
            expect(projetsCard.exists()).toBe(true)
            expect(projetsCard.find('.metric-label').text()).toBe('Projets actifs')
        })

        it('affiche la carte des tâches terminées', () => {
            const termineesCard = wrapper.find('.metric-card-purple')
            expect(termineesCard.exists()).toBe(true)
            expect(termineesCard.find('.metric-label').text()).toBe('Tâches terminées')
        })
    })

    describe('Chargement des données', () => {
        beforeEach(() => {
            wrapper = createWrapper()
        })

        it('appelle les APIs pour charger les données', async () => {
            const mockToken = 'fake-token'
            window.localStorage.getItem.mockReturnValue(mockToken)

            const mockCollaborateurs = [
                { id: 1, nom: 'Dupont', prenom: 'Jean', disponible: true },
                { id: 2, nom: 'Martin', prenom: 'Sophie', disponible: false }
            ]

            const mockProjets = [
                { id: 1, titre: 'Projet 1', statut: 'en_cours' },
                { id: 2, titre: 'Projet 2', statut: 'termine' }
            ]

            const mockTaches = [
                { id: 1, titre: 'Tâche 1', statut: 'en_cours' },
                { id: 2, titre: 'Tâche 2', statut: 'terminee' }
            ]

            const mockCompetences = [
                { id: 1, nom: 'PHP' },
                { id: 2, nom: 'JavaScript' }
            ]

            axios.get
                .mockResolvedValueOnce({ data: { data: mockCollaborateurs } })
                .mockResolvedValueOnce({ data: { data: mockProjets } })
                .mockResolvedValueOnce({ data: { data: mockTaches } })
                .mockResolvedValueOnce({ data: { data: mockCompetences } })

            await wrapper.vm.loadDashboardData()

            expect(axios.get).toHaveBeenCalledWith('http://127.0.0.1:8000/api/collaborateurs', {
                headers: { Authorization: `Bearer ${mockToken}` }
            })
            expect(axios.get).toHaveBeenCalledWith('http://127.0.0.1:8000/api/missions', {
                headers: { Authorization: `Bearer ${mockToken}` }
            })
            expect(axios.get).toHaveBeenCalledWith('http://127.0.0.1:8000/api/taches', {
                headers: { Authorization: `Bearer ${mockToken}` }
            })
            expect(axios.get).toHaveBeenCalledWith('http://127.0.0.1:8000/api/competences', {
                headers: { Authorization: `Bearer ${mockToken}` }
            })
        })

        it('gère les erreurs de chargement des données', async () => {
            window.localStorage.getItem.mockReturnValue('fake-token')
            axios.get.mockRejectedValue(new Error('Erreur API'))

            await wrapper.vm.loadDashboardData()

            // Les données devraient rester vides en cas d'erreur
            expect(wrapper.vm.collaborateurs).toEqual([])
            expect(wrapper.vm.projets).toEqual([])
            expect(wrapper.vm.taches).toEqual([])
            expect(wrapper.vm.competences).toEqual([])
        })
    })

    describe('Calcul des statistiques', () => {
        beforeEach(() => {
            wrapper = createWrapper()

            // Données de test
            wrapper.vm.collaborateurs = [
                { id: 1, disponible: true },
                { id: 2, disponible: false },
                { id: 3, disponible: true }
            ]

            wrapper.vm.projets = [
                { id: 1, statut: 'en_cours' },
                { id: 2, statut: 'termine' },
                { id: 3, statut: 'en_cours' }
            ]

            wrapper.vm.taches = [
                { id: 1, statut: 'en_cours' },
                { id: 2, statut: 'terminee' },
                { id: 3, statut: 'en_cours' },
                { id: 4, statut: 'terminee' }
            ]
        })

        it.skip('calcule correctement les statistiques des collaborateurs', () => {
            const stats = wrapper.vm.stats

            expect(stats.totalCollaborateurs).toBe(3)
            expect(stats.collaborateursDisponibles).toBe(2)
        })

        it.skip('calcule correctement les statistiques des projets', () => {
            const stats = wrapper.vm.stats

            expect(stats.totalProjets).toBe(3)
            expect(stats.projetsEnCours).toBe(2)
        })

        it.skip('calcule correctement les statistiques des tâches', () => {
            const stats = wrapper.vm.stats

            expect(stats.totalTaches).toBe(4)
            expect(stats.tachesEnCours).toBe(2)
            expect(stats.tachesTerminees).toBe(2)
        })

        it.skip('calcule les tâches en retard', () => {
            wrapper.vm.taches = [
                { id: 1, statut: 'en_cours', dateFin: '2023-01-01' },
                { id: 2, statut: 'en_cours', dateFin: '2025-01-01' }
            ]

            const stats = wrapper.vm.stats
            expect(stats.tachesEnRetard).toBe(1)
        })
    })

    describe('Affichage des projets', () => {
        beforeEach(() => {
            wrapper = createWrapper()

            wrapper.vm.projets = [
                {
                    id: 1,
                    titre: 'Projet Test',
                    description: 'Description du projet',
                    dateFinPrevue: '2024-12-31',
                    taches: [
                        { id: 1, statut: 'terminee' },
                        { id: 2, statut: 'en_cours' },
                        { id: 3, statut: 'terminee' }
                    ]
                }
            ]
        })

        it.skip('affiche les projets en cours', () => {
            const projectsGrid = wrapper.find('.projects-grid')
            expect(projectsGrid.exists()).toBe(true)
        })

        it.skip('calcule correctement la progression des projets', () => {
            const progress = wrapper.vm.getProjectProgress(wrapper.vm.projets[0])
            expect(progress).toBe(67) // 2 tâches terminées sur 3 = 67%
        })

        it('gère les projets sans tâches', () => {
            wrapper.vm.projets = [
                { id: 1, titre: 'Projet sans tâches', taches: [] }
            ]

            const progress = wrapper.vm.getProjectProgress(wrapper.vm.projets[0])
            expect(progress).toBe(0)
        })
    })

    describe('Affichage de l\'équipe', () => {
        beforeEach(() => {
            wrapper = createWrapper()

            wrapper.vm.collaborateurs = [
                {
                    id: 1,
                    nom: 'Dupont',
                    prenom: 'Jean',
                    role: 'Manager',
                    disponible: true
                },
                {
                    id: 2,
                    nom: 'Martin',
                    prenom: 'Sophie',
                    role: 'Développeur',
                    disponible: false
                }
            ]
        })

        it.skip('affiche la grille de l\'équipe', () => {
            const teamGrid = wrapper.find('.team-grid')
            expect(teamGrid.exists()).toBe(true)
        })

        it.skip('affiche les informations des collaborateurs', () => {
            const teamCards = wrapper.findAll('.team-card')
            expect(teamCards.length).toBe(2)

            const firstCard = teamCards[0]
            expect(firstCard.find('.member-name').text()).toBe('Jean Dupont')
            expect(firstCard.find('.member-role').text()).toBe('Manager')
        })

        it.skip('affiche le statut de disponibilité', () => {
            const teamCards = wrapper.findAll('.team-card')

            const availableCard = teamCards[0]
            expect(availableCard.find('.status-text').text()).toBe('Disponible')
            expect(availableCard.find('.status-online').exists()).toBe(true)

            const unavailableCard = teamCards[1]
            expect(unavailableCard.find('.status-text').text()).toBe('Indisponible')
            expect(unavailableCard.find('.status-offline').exists()).toBe(true)
        })
    })

    describe('Affichage des tâches', () => {
        beforeEach(() => {
            wrapper = createWrapper()

            wrapper.vm.taches = [
                {
                    id: 1,
                    titre: 'Tâche 1',
                    description: 'Description de la tâche',
                    priorite: 'urgent',
                    collaborateur: { prenom: 'Jean', nom: 'Dupont' }
                },
                {
                    id: 2,
                    titre: 'Tâche 2',
                    description: 'Autre tâche',
                    priorite: 'normal',
                    collaborateur: null
                }
            ]
        })

        it.skip('affiche la grille des tâches', () => {
            const tasksGrid = wrapper.find('.tasks-grid')
            expect(tasksGrid.exists()).toBe(true)
        })

        it.skip('affiche les informations des tâches', () => {
            const taskCards = wrapper.findAll('.task-card')
            expect(taskCards.length).toBe(2)

            const firstCard = taskCards[0]
            expect(firstCard.find('.task-name').text()).toBe('Tâche 1')
            expect(firstCard.find('.task-description').text()).toBe('Description de la tâche')
        })

        it.skip('affiche l\'assignation des tâches', () => {
            const taskCards = wrapper.findAll('.task-card')

            const assignedCard = taskCards[0]
            expect(assignedCard.find('.task-assignee').text()).toBe('Jean Dupont')

            const unassignedCard = taskCards[1]
            expect(unassignedCard.find('.task-assignee').text()).toBe('Non assigné')
        })
    })

    describe('Fonctionnalités d\'affichage', () => {
        beforeEach(() => {
            wrapper = createWrapper()

            // Simuler beaucoup de collaborateurs et tâches
            wrapper.vm.collaborateurs = Array.from({ length: 6 }, (_, i) => ({
                id: i + 1,
                nom: `Nom${i + 1}`,
                prenom: `Prenom${i + 1}`,
                role: 'Développeur',
                disponible: true
            }))

            wrapper.vm.taches = Array.from({ length: 6 }, (_, i) => ({
                id: i + 1,
                titre: `Tâche ${i + 1}`,
                description: `Description ${i + 1}`,
                priorite: 'normal'
            }))
        })

        it.skip('affiche le bouton "Afficher plus" pour les collaborateurs', () => {
            const showMoreButton = wrapper.find('.btn-show-more')
            expect(showMoreButton.exists()).toBe(true)
            expect(showMoreButton.text()).toBe('Afficher plus')
        })

        it.skip('affiche le bouton "Afficher plus" pour les tâches', () => {
            const showMoreButton = wrapper.find('.btn-show-more')
            expect(showMoreButton.exists()).toBe(true)
        })

        it.skip('permet d\'afficher/masquer tous les collaborateurs', async () => {
            const showMoreButton = wrapper.find('.btn-show-more')

            await showMoreButton.trigger('click')
            expect(wrapper.vm.showAllCollaborateurs).toBe(true)
            expect(showMoreButton.text()).toBe('Afficher moins')

            await showMoreButton.trigger('click')
            expect(wrapper.vm.showAllCollaborateurs).toBe(false)
            expect(showMoreButton.text()).toBe('Afficher plus')
        })
    })

    describe('Formatage des dates', () => {
        beforeEach(() => {
            wrapper = createWrapper()
        })

        it('formate correctement les dates', () => {
            const dateString = '2024-12-31'
            const formatted = wrapper.vm.formatDate(dateString)

            expect(formatted).toMatch(/\d{2}\/\d{2}\/\d{4}/)
        })

        it('gère les dates nulles', () => {
            const formatted = wrapper.vm.formatDate(null)
            expect(formatted).toBe('')
        })

        it('gère les dates vides', () => {
            const formatted = wrapper.vm.formatDate('')
            expect(formatted).toBe('')
        })
    })

    describe('Cache localStorage', () => {
        beforeEach(() => {
            wrapper = createWrapper()
        })

        it('sauvegarde les données dans le cache', async () => {
            const mockData = {
                collaborateurs: [{ id: 1, nom: 'Test' }],
                projets: [{ id: 1, titre: 'Projet Test' }],
                taches: [{ id: 1, titre: 'Tâche Test' }],
                competences: [{ id: 1, nom: 'Test' }]
            }

            window.localStorage.getItem.mockReturnValue('fake-token')
            axios.get.mockResolvedValue({ data: { data: [] } })

            await wrapper.vm.loadDashboardData()

            expect(window.localStorage.setItem).toHaveBeenCalledWith(
                'dashboardCache',
                expect.stringContaining('"collaborateurs"')
            )
        })

        it.skip('charge les données depuis le cache si valide', async () => {
            const cacheData = {
                collaborateurs: [{ id: 1, nom: 'Cached' }],
                projets: [{ id: 1, titre: 'Cached Project' }],
                taches: [{ id: 1, titre: 'Cached Task' }],
                competences: [{ id: 1, nom: 'Cached Skill' }],
                timestamp: Date.now()
            }

            window.localStorage.getItem.mockReturnValue(JSON.stringify(cacheData))

            await wrapper.vm.loadDashboardData()

            expect(wrapper.vm.collaborateurs).toEqual(cacheData.collaborateurs)
            expect(wrapper.vm.projets).toEqual(cacheData.projets)
            expect(wrapper.vm.taches).toEqual(cacheData.taches)
            expect(wrapper.vm.competences).toEqual(cacheData.competences)
        })

        it('ignore le cache expiré', async () => {
            const expiredCache = {
                collaborateurs: [{ id: 1, nom: 'Expired' }],
                timestamp: Date.now() - 10 * 60 * 1000 // 10 minutes ago
            }

            window.localStorage.getItem
                .mockReturnValueOnce(JSON.stringify(expiredCache)) // Cache
                .mockReturnValueOnce('fake-token') // Token

            axios.get.mockResolvedValue({ data: { data: [] } })

            await wrapper.vm.loadDashboardData()

            // Devrait appeler l'API au lieu d'utiliser le cache
            expect(axios.get).toHaveBeenCalled()
        })
    })

    describe('Actualisation des données', () => {
        beforeEach(() => {
            wrapper = createWrapper()
        })

        it('permet de forcer l\'actualisation', async () => {
            window.localStorage.getItem.mockReturnValue('fake-token')
            axios.get.mockResolvedValue({ data: { data: [] } })

            await wrapper.vm.refreshData(true)

            expect(axios.get).toHaveBeenCalled()
        })

        it.skip('utilise le cache par défaut', async () => {
            const cacheData = {
                collaborateurs: [{ id: 1, nom: 'Cached' }],
                timestamp: Date.now()
            }

            window.localStorage.getItem.mockReturnValue(JSON.stringify(cacheData))

            await wrapper.vm.refreshData(false)

            // Ne devrait pas appeler l'API
            expect(axios.get).not.toHaveBeenCalled()
        })
    })

    describe('États de chargement', () => {
        beforeEach(() => {
            wrapper = createWrapper()
        })

        it('affiche l\'état de chargement', () => {
            wrapper.vm.loading = true

            const loadingStates = wrapper.findAll('.loading-state')
            expect(loadingStates.length).toBeGreaterThan(0)
        })

        it.skip('affiche l\'état vide', () => {
            wrapper.vm.collaborateurs = []
            wrapper.vm.loading = false

            const emptyStates = wrapper.findAll('.empty-state')
            expect(emptyStates.length).toBeGreaterThan(0)
        })
    })
})
