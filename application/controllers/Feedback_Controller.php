<?php
/** @noinspection PhpUnused */

namespace application\controllers;


use application\services\ApplicationService;
use application\services\MailService;

class Feedback_Controller
{
    /**
     * segnala un errore non previsto dal gioco. Il report invierà una email agli amministratori con tutti idettagli.
     * @return string
     */
    public static function report_error(): string
    {
        MailService::send_error_report(base64_decode($_POST['params']));
        return ok_response();
    }

    /**
     * segnala un messaggio dalla sfera dimensionale. Necessita dei dati di autenticazione e del messaggio incriminato,
     * nonché del tipo di report. Verrà inviata una mail agli amministratori con tutti i dettagli.
     * @return int
     */
    public static function report_message() {
        return ApplicationService::report_message($_POST['player_id'], $_POST['game_token'], $_POST['message_id'], $_POST['report_type']);
    }
}