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
        '/api/collaborateurs' => [[['_route' => 'api_collaborateurs', '_controller' => 'App\\Controller\\Api\\CollaborateurApiController::index'], null, ['GET' => 0], null, false, false, null]],
        '/collaborateur' => [[['_route' => 'app_collaborateur_index', '_controller' => 'App\\Controller\\CollaborateurController::index'], null, ['GET' => 0], null, true, false, null]],
        '/collaborateur/new' => [[['_route' => 'app_collaborateur_new', '_controller' => 'App\\Controller\\CollaborateurController::new'], null, ['GET' => 0, 'POST' => 1], null, false, false, null]],
        '/' => [[['_route' => 'home', '_controller' => 'App\\Controller\\HomeController::index'], null, null, null, false, false, null]],
        '/mission' => [[['_route' => 'app_mission_index', '_controller' => 'App\\Controller\\MissionController::index'], null, ['GET' => 0], null, true, false, null]],
        '/mission/new' => [[['_route' => 'app_mission_new', '_controller' => 'App\\Controller\\MissionController::new'], null, ['GET' => 0, 'POST' => 1], null, false, false, null]],
    ],
    [ // $regexpList
        0 => '{^(?'
                .'|/_error/(\\d+)(?:\\.([^/]++))?(*:35)'
                .'|/affectation/([^/]++)(?'
                    .'|(*:66)'
                    .'|/edit(*:78)'
                    .'|(*:85)'
                .')'
                .'|/collaborateur/([^/]++)(?'
                    .'|(*:119)'
                    .'|/edit(*:132)'
                    .'|(*:140)'
                .')'
                .'|/mission/([^/]++)(?'
                    .'|(*:169)'
                    .'|/edit(*:182)'
                    .'|(*:190)'
                .')'
            .')/?$}sDu',
    ],
    [ // $dynamicRoutes
        35 => [[['_route' => '_preview_error', '_controller' => 'error_controller::preview', '_format' => 'html'], ['code', '_format'], null, null, false, true, null]],
        66 => [[['_route' => 'app_affectation_show', '_controller' => 'App\\Controller\\AffectationController::show'], ['id'], ['GET' => 0], null, false, true, null]],
        78 => [[['_route' => 'app_affectation_edit', '_controller' => 'App\\Controller\\AffectationController::edit'], ['id'], ['GET' => 0, 'POST' => 1], null, false, false, null]],
        85 => [[['_route' => 'app_affectation_delete', '_controller' => 'App\\Controller\\AffectationController::delete'], ['id'], ['POST' => 0], null, false, true, null]],
        119 => [[['_route' => 'app_collaborateur_show', '_controller' => 'App\\Controller\\CollaborateurController::show'], ['id'], ['GET' => 0], null, false, true, null]],
        132 => [[['_route' => 'app_collaborateur_edit', '_controller' => 'App\\Controller\\CollaborateurController::edit'], ['id'], ['GET' => 0, 'POST' => 1], null, false, false, null]],
        140 => [[['_route' => 'app_collaborateur_delete', '_controller' => 'App\\Controller\\CollaborateurController::delete'], ['id'], ['POST' => 0], null, false, true, null]],
        169 => [[['_route' => 'app_mission_show', '_controller' => 'App\\Controller\\MissionController::show'], ['id'], ['GET' => 0], null, false, true, null]],
        182 => [[['_route' => 'app_mission_edit', '_controller' => 'App\\Controller\\MissionController::edit'], ['id'], ['GET' => 0, 'POST' => 1], null, false, false, null]],
        190 => [
            [['_route' => 'app_mission_delete', '_controller' => 'App\\Controller\\MissionController::delete'], ['id'], ['POST' => 0], null, false, true, null],
            [null, null, null, null, false, false, 0],
        ],
    ],
    null, // $checkCondition
];
