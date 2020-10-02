<?php /** @noinspection PhpUnused */


namespace application\controllers;


use application\repositories\GiftCodeRepository;
use application\services\GiftCodeService;
use services\PlayerService;

class Giftcode_Controller
{

    /**
     * ottiene lo stato del codice regalo.
     * 0 - disponibile
     * 100 - non trovato
     * 102 - già usato
     * 103 - scaduto
     * @return int
     */
    public static function state() {
        $player = PlayerService::get_logged_player();
        $gift_code = GiftCodeRepository::get_code($_GET['code']);
        return GiftCodeService::get_code_state($player, $gift_code);
    }

    /**
     * interroga sulle ricompense che darà il codice regalo senza però utilizzarlo.
     * @return \application\models\GiftCode|null
     */
    public static function rewards() {
        return GiftCodeService::get_code_rewards($_GET['code']);
    }

    /**
     * Utilizza il codice regalo e restituisce le informazioni sulle ricompense.
     * @return array
     */
    public static function use_code() {
        return GiftCodeService::use_code($_POST['code']);
    }

    /**
     * Restituisce un array di tutti i codici regalo utilizzati dal giocatore.
     * Questo farà sì che il giocatore possa ottenere gli oggetti su tutti i suoi salvataggi.
     * @return array
     */
    public static function used_codes() {
        return GiftCodeService::obtained_rewards();
    }
}