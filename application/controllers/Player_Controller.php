<?php /** @noinspection PhpUnused */

namespace application\controllers;

use application\repositories\AchievementRepository;
use \application\repositories\PlayerRepository;
use services\PlayerService;

class Player_Controller {

    public static function login() {
        $player = PlayerService::authenticate_player(intval($_POST['player_id']), $_POST['game_token']);
        if ($player) {
            if ($player->is_banned()) return operation_failed(banned());
            $_SESSION['player_id'] = $player->get_id();
            return operation_ok();
        } else {
            return operation_failed(player_unregistered());
        }
    }

    public static function logout() {
        session_destroy();
        return operation_ok();
    }

    /**
     * Restituisce le informazioni pubbliche del giocatore passandogli uno dei due parametri, player_id o name come query.
     * Se non viene passato alcun parametro, viene dato il giocatore loggato.
     * @return \application\models\Player|null
     */
    public static function index() {
        if (isset($_GET['player_id'])) {
            return PlayerRepository::get_player_from_id(intval($_GET["player_id"]));
        } elseif (isset($_GET['name'])) {
            return PlayerRepository::get_player_from_name($_GET['name']);
        } else {
            return PlayerService::get_logged_player();
        }
    }

    /**
     * Aggiorna i progressi pubblici del giocatore (storia, missioni, livello ecc...)
     */
    public static function update() {
        return PlayerService::update_player($_POST);
    }

    /**
     * Funzione di creazione del giocatore. Necessita di un game_token creato dal client di gioco e
     * nome e ID volto scelti dal giocatore al momento della registrazione.
     * @return array
     */
    public static function create() {
        $old_token = isset($_POST['old_token']) ? $_POST['old_token'] : null;
        $title_id = isset($_POST['title_id']) ? $_POST['title_id'] : null;
        return PlayerService::create_player($_POST['game_token'], $_POST['name'], $_POST['face_id'], $title_id, $old_token);
    }

    /**
     * Aggiorna il face del giocatore. Necessita dei soliti parametri di autenticazione più l'attributo
     * face_id che identifica il codice del nuovo face.
     */
    public static function update_face() {
        return PlayerService::update_player_face($_POST['face_id']);
    }

    /**
     * Aggiorna il titolo del giocatore. Necessita dei soliti parametri di autenticazione più l'attributo
     * title_id che identifica il codice del nuovo face.
     * @return array
     */
    public static function update_title() {
        return PlayerService::update_player_title($_POST['title_id']);
    }

    /**
     * Controlla se un nome è valido per essere registrato.
     * Ottiene un numero come risposta.
     * 0 - valido
     * 1 - nome già usato
     * 2 - include parole non permesse (parolacce o offensive)
     * 3 - include caratteri speciali non permessi
     * @return int
     */
    public static function check_name_valid() {
        return PlayerService::name_is_valid($_GET['name']);
    }

    /**
     * Sblocca un obiettivo al giocatore. Necessita dei parametri di autenticazione e dell'ID obiettivo.
     * @return int|string
     */
    public static function unlock_achievement() {
        $player = PlayerService::get_logged_player();
        if ($player == null) return player_unregistered();
        if ($player->is_banned()) return banned();
        $result = AchievementRepository::unlock_achievement($player->get_id(), intval($_GET['achievement_id']));
        return $result ? ok_response() : unprocessed();
    }

    /**
     * Ottiene gli obiettivi sbloccati dal giocatore. L'informazione è pubblica, perciò richiede
     * solo il player_id o il nome del giocatore.
     * Restituisce un array di ID obiettivi.
     * @return array|int
     */
    public static function get_achievements() {
        if (isset($_GET['player_id']))
            $player = PlayerRepository::get_player_from_id($_GET['player_id']);
        else
            $player = PlayerRepository::get_player_from_name($_GET['name']);
        if ($player == null) return player_unregistered();
        return AchievementRepository::get_player_achievements($player->get_id());
    }

    public static function get_unlocked_titles() {
        return PlayerService::get_titles($_GET['player_id']);
    }

    public static function unlock_titles() {
        $titles_array = explode(',', $_POST['title_ids']);
        return PlayerService::unlock_titles($titles_array);
    }
}