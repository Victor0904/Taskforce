<?php

/**
 * This file has been auto-generated
 * by the Symfony Routing Component.
 */

return [
    false, // $matchHost
    [ // $staticRoutes
        '/affectation' => [[['_route' => 'app_affectation_index', '_controller' => 'App\\Controller\\AffectationController::index'], null, ['GET' => 0], null, true, false, null]],
        '/affectation/new' => [[['_route' => 'app_affectation_new', '_controller' => 'App\\Controller\\AffectationController::new'], null, ['GET' => 0, 'POST' => 1], null, false, false, null]],
        '/api/affectations' => [
            [['_route' => 'api_affectations_list', '_controller' => 'App\\Controller\\Api\\AffectationApiController::list'], null, ['GET' => 0], null, false, false, null],
            [['_route' => 'api_affectations_create', '_controller' => 'App\\Controller\\Api\\AffectationApiController::create'], null, ['POST' => 0], null, false, false, null],
        ],
        '/api/collaborateurs' => [
            [['_route' => 'api_collaborateurs_list', '_controller' => 'App\\Controller\\Api\\CollaborateurApiController::list'], null, ['GET' => 0], null, false, false, null],
            [['_route' => 'api_collaborateurs_create', '_controller' => 'App\\Controller\\Api\\CollaborateurApiController::create'], null, ['POST' => 0], null, false, false, null],
        ],
        '/api/missions' => [
            [['_route' => 'api_missions_list', '_controller' => 'App\\Controller\\Api\\MissionApiController::list'], null, ['GET' => 0], null, false, false, null],
            [['_route' => 'api_missions_create', '_controller' => 'App\\Controller\\Api\\MissionApiController::create'], null, ['POST' => 0], null, false, false, null],
        ],
        '/collaborateur' => [[['_route' => 'app_collaborateur_index', '_controller' => 'App\\Controller\\CollaborateurController::index'], null, ['GET' => 0], null, true, false, null]],
        '/collaborateur/new' => [[['_route' => 'app_collaborateur_new', '_controller' => 'App\\Controller\\CollaborateurController::new'], null, ['GET' => 0, 'POST' => 1], null, false, false, null]],
        '/' => [[['_route' => 'home', '_controller' => 'App\\Controller\\HomeController::index'], null, null, null, false, false, null]],
        '/mission' => [[['_route' => 'app_mission_index', '_controller' => 'App\\Controller\\MissionController::index'], null, ['GET' => 0], null, true, false, null]],
        '/mission/new' => [[['_route' => 'app_mission_new', '_controller' => 'App\\Controller\\MissionController::new'], null, ['GET' => 0, 'POST' => 1], null, false, false, null]],
    ],
    [ // $regexpList
        0 => '{^(?'
                .'|/_error/(\\d+)(?:\\.([^/]++))?(*:35)'
                .'|/a(?'
                    .'|ffectation/([^/]++)(?'
                        .'|(*:69)'
                        .'|/edit(*:81)'
                        .'|(*:88)'
                    .')'
                    .'|pi/(?'
                        .'|affectations/([^/]++)(?'
                            .'|(*:126)'
                        .')'
                        .'|collaborateurs/([^/]++)(?'
                            .'|(*:161)'
                        .')'
                    .')'
                .')'
                .'|/collaborateur/([^/]++)(?'
                    .'|(*:198)'
                    .'|/edit(*:211)'
                    .'|(*:219)'
                .')'
                .'|/mission/([^/]++)(?'
                    .'|(*:248)'
                    .'|/edit(*:261)'
                    .'|(*:269)'
                .')'
            .')/?$}sDu',
    ],
    [ // $dynamicRoutes
        35 => [[['_route' => '_preview_error', '_controller' => 'error_controller::preview', '_format' => 'html'], ['code', '_format'], null, null, false, true, null]],
        69 => [[['_route' => 'app_affectation_show', '_controller' => 'App\\Controller\\AffectationController::show'], ['id'], ['GET' => 0], null, false, true, null]],
        81 => [[['_route' => 'app_affectation_edit', '_controller' => 'App\\Controller\\AffectationController::edit'], ['id'], ['GET' => 0, 'POST' => 1], null, false, false, null]],
        88 => [[['_route' => 'app_affectation_delete', '_controller' => 'App\\Controller\\AffectationController::delete'], ['id'], ['POST' => 0], null, false, true, null]],
        126 => [
            [['_route' => 'api_affectations_show', '_controller' => 'App\\Controller\\Api\\AffectationApiController::show'], ['id'], ['GET' => 0], null, false, true, null],
            [['_route' => 'api_affectations_delete', '_controller' => 'App\\Controller\\Api\\AffectationApiController::delete'], ['id'], ['DELETE' => 0], null, false, true, null],
            [['_route' => 'api_affectations_update', '_controller' => 'App\\Controller\\Api\\AffectationApiController::update'], ['id'], ['PUT' => 0], null, false, true, null],
        ],
        161 => [
            [['_route' => 'api_collaborateurs_show', '_controller' => 'App\\Controller\\Api\\CollaborateurApiController::show'], ['id'], ['GET' => 0], null, false, true, null],
            [['_route' => 'api_collaborateurs_update', '_controller' => 'App\\Controller\\Api\\CollaborateurApiController::update'], ['id'], ['PUT' => 0], null, false, true, null],
            [['_route' => 'api_collaborateurs_delete', '_controller' => 'App\\Controller\\Api\\CollaborateurApiController::delete'], ['id'], ['DELETE' => 0], null, false, true, null],
        ],
        198 => [[['_route' => 'app_collaborateur_show', '_controller' => 'App\\Controller\\CollaborateurController::show'], ['id'], ['GET' => 0], null, false, true, null]],
        211 => [[['_route' => 'app_collaborateur_edit', '_controller' => 'App\\Controller\\CollaborateurController::edit'], ['id'], ['GET' => 0, 'POST' => 1], null, false, false, null]],
        219 => [[['_route' => 'app_collaborateur_delete', '_controller' => 'App\\Controller\\CollaborateurController::delete'], ['id'], ['POST' => 0], null, false, true, null]],
        248 => [[['_route' => 'app_mission_show', '_controller' => 'App\\Controller\\MissionController::show'], ['id'], ['GET' => 0], null, false, true, null]],
        261 => [[['_route' => 'app_mission_edit', '_controller' => 'App\\Controller\\MissionController::edit'], ['id'], ['GET' => 0, 'POST' => 1], null, false, false, null]],
        269 => [
            [['_route' => 'app_mission_delete', '_controller' => 'App\\Controller\\MissionController::delete'], ['id'], ['POST' => 0], null, false, true, null],
            [null, null, null, null, false, false, 0],
        ],
    ],
    null, // $checkCondition
];
