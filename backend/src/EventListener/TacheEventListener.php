<?php

namespace App\EventListener;

use App\Entity\Tache;
use App\Service\TaskAssignmentService;
use Doctrine\ORM\Event\PostPersistEventArgs;
use Doctrine\ORM\Event\PostUpdateEventArgs;
use Doctrine\ORM\Event\PostRemoveEventArgs;
use Doctrine\ORM\Event\PreRemoveEventArgs;

/**
 * Event Listener pour la mise à jour automatique de disponibilité
 * Se déclenche automatiquement lors de tous les événements de tâches
 */
class TacheEventListener
{
    public function __construct(
        private TaskAssignmentService $taskAssignmentService
    ) {}

    /**
     * Après la création d'une tâche
     */
    public function postPersist(PostPersistEventArgs $args): void
    {
        $entity = $args->getObject();
        
        if (!$entity instanceof Tache) {
            return;
        }

        // La mise à jour de disponibilité est déjà gérée dans assignTaskAutomatically
        // Pas besoin de la déclencher ici
    }

    /**
     * Après la mise à jour d'une tâche
     */
    public function postUpdate(PostUpdateEventArgs $args): void
    {
        $entity = $args->getObject();
        
        if (!$entity instanceof Tache) {
            return;
        }

        // Mettre à jour automatiquement la disponibilité du collaborateur assigné
        if ($entity->getCollaborateur()) {
            $this->taskAssignmentService->updateCollaborateurAvailability($entity->getCollaborateur());
        }
    }

    /**
     * Après la suppression d'une tâche
     * Note: La mise à jour de disponibilité est gérée directement dans le TacheController
     */
    public function postRemove(PostRemoveEventArgs $args): void
    {
        // Cette méthode est laissée vide car la mise à jour est gérée dans le controller
        // pour éviter les problèmes de cycle de vie des entités
    }
}
