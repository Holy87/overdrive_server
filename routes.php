<?php

/*
 * DEFINIZIONE DELLE ROUTES
 * Definire il controller, quindi l'array dei metodi (GET, POST, PUT, DELETE...)
 * e quindi l'azione da intraprendere.
 * Come azione va scritto il metodo statico del controller, oppure la conversione
 * da url a metodo. Esempio di url:
 *
 * api/player/create
 * l'aplicazione cercherà Player_Controller, metodo create.
 * Se il nome del metodo è diverso da quello che deve apparire nell'url,
 * si deve definire come nome_url: nome_metodo. Esempio:
 * Voglio chiamare Chest_Controller al suo metodo open_chest quando viene fatta una POST:
 * 'chest' => [
 *   'post' => [
 *     'open: open_chest'
 *   ]
 * ]
 * la route associerà direttamente chest a Chest_Controller.
 */

function routes(): array {
    return [
        'player' => [
            'get' => [
                'get_player: index',
                'check_name_valid',
                'achievements: get_achievements'
            ],
            'post' => [
                'update',
                'update_face',
                'create',
                'unlock_achievement'
            ]
        ],
        'chest' => [
            'get' => ['check: check_chest_state'],
            'post' => ['open','fill','feedback']
        ],
        'board' => [
            'get' => ['messages: get_messages'],
            'post' => ['message: post_message']
        ],
        'feedback' => [
            'post' => ['report_error', 'report_message']
        ],
        'notifications' => [
            'get' => ['read']
        ]
    ];
}