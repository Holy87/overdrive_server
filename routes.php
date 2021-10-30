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

function routes(): array
{
    return [
        'player' => [
            'get' => [
                'get_player: index',
                'check_name_valid',
                'achievements: get_achievements',
                'titles: get_unlocked_titles',
                'party_info',
                'search_name',
                'following', // giocatori che segui
                'followers', // giocatori che ti seguono
            ],
            'post' => [
                '*update',
                '*update_face',
                '*update_title',
                'create',
                '*unlock_achievement',
                '*titles: unlock_titles',
                '*follow',
                '*unfollow',
                'login',
                'logout'
            ]
        ],
        'chest' => [
            'get' => ['check: check_chest_state'],
            'post' => ['*open', '*fill', '*feedback']
        ],
        'board' => [
            'get' => ['messages: get_messages'],
            'post' => ['*message: post_message']
        ],
        'feedback' => [
            'post' => ['report_error', '*report_message']
        ],
        'notifications' => [
            'get' => ['*read', '*unread','*count: unread_count'],
            'post' => ['*read_all: set_all_read']
        ],
        'giftcode' => [
            'get' => ['*state', '*rewards'],
            'post' => ['*apply: use_code']
        ],
        'auction' => [
            'get' => ['list', '*listed', '*sold'],
            'post' => ['*buy']
        ],
        'events' => [
            'get' => ['list']
        ],
        'application' => [
            'get' => ['game_rates', 'status', 'eula'],
            'post' => ['clean: start_cleaning']
        ]
    ];
}
