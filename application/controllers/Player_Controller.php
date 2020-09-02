<?php /** @noinspection PhpUnused */

namespace application\controllers;

use application\repositories\AchievementRepository;
use \application\repositories\PlayerRepository;
use services\PlayerService;

class Player_Controller {

    /**
     * Restituisce le informazioni pubbliche del giocatore passandogli uno dei due parametri, player_id o name come query.
     * @return \application\models\Player|null
     */
    public static function index() {
        if (isset($_GET['player_id'])) {
            return PlayerRepository::get_player_from_id(intval($_GET["player_id"]));
        } elseif (isset($_GET['name'])) {
            return PlayerRepository::get_player_from_name($_GET['name']);
        } else {
            return null;
        }
    }

    /**
     * Aggiorna i progressi pubblici del giocatore (storia, missioni, livello ecc...)
     * @return int|string
     */
    public static function update() {
        $player = PlayerService::authenticate_player($_POST['player_id'], $_POST['game_token']);
        if ($player) {
            $player->merge($_POST);
            PlayerRepository::save_player($player);
            return ok_response();
        } else {
            return player_unregistered();
        }
    }

    /**
     * Funzione di creazione del giocatore. Necessita di un game_token creato dal client di gioco e
     * nome e ID volto scelti dal giocatore al momento della registrazione.
     * @return array
     */
    public static function create() {
        return PlayerService::create_player($_POST['game_token'], $_POST['name'], $_POST['face_id']);
    }

    /**
     * Aggiorna il face del giocatore. Necessita dei soliti parametri di autenticazione più l'attributo
     * face_id che identifica il codice del nuovo face.
     * @return int
     */
    public static function update_face() {
        $player = PlayerService::authenticate_player($_POST['player_id'], $_POST['game_token']);
        if ($player) {
            $player->set_face(intval($_POST['face_id']));
            PlayerRepository::save_player($player);
            return http_response_code(200);
        } else {
            return player_unregistered();
        }
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
        $player = PlayerService::authenticate_player($_POST['player_id'], $_POST['game_token']);
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
}