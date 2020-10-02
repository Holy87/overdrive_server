<?php
/** @noinspection PhpUnused */

namespace application\controllers;


use application\services\ChestService;

class Chest_Controller
{

    /**
     * Restituisce lo stato di uno scrigno dimensionale.
     * full - lo scrigno contiene qualcosa (quindi è chiuso)
     * emptu - lo scrigno è vuoto (quindi aperto)
     * @return string
     */
    public static function check_chest_state(): string
    {
        return ChestService::check_chest($_GET['chest']);
    }

    /**
     * Questa procedura non solo apre lo scrigno per restituire il contenuto sottoforma di item (id oggetto e tipo)
     * ma cancella anche l'oggetto dallo scrigno poiché si suppone che il giocatore l'abbia già preso.
     * Nei dati di gioco restituisce anche owner_id e owner_name, che identificano rispettivamente ID e nome del
     * giocatore che ha lasciato l'oggetto nello scrigno.
     * Viene anche restituito il token del per certificare il bonus di fama e infamia.
     * @return false|int|string
     */
    public static function open() {
        return ChestService::loot_chest($_POST['chest']);
    }

    /**
     * Inserisce nello scrigno un oggetto. Necessita dei parametri di autenticazione,
     * tipo oggetto (0: item, 1: weapon, 2: armor) ed ID oggetto del database di gioco.
     * @return int|string
     */
    public static function fill() {
        return ChestService::fill_chest($_POST['chest'], intval($_POST['item_type']), $_POST['item_id']);
    }

    /**
     * quando il giocatore che ha preso l'oggetto ringrazia oppure viene sconfitto dal mostro nascosto, manda un feedback
     * per aumentare la fama o l'infamia. Necessita dei dati di autenticazione, il "token" che ha preso dallo scrigno che
     * serve a validare la ricompensa ed il tipo (0 se fama, 1 se infamia)
     * @return int|string
     */
    public static function feedback() {
        return ChestService::check_feedback($_POST['token'], intval($_POST['type']));
    }
}