<?php
/** @noinspection PhpUnused */

namespace application\controllers;


use application\services\ApplicationService;
use application\services\MailService;

class Feedback_Controller
{
    /**
     * segnala un errore non previsto dal gioco. Il report invierà una email agli amministratori con tutti idettagli.
     * @return array
     */
    public static function report_error(): array
    {
        MailService::send_error_report(base64_decode($_POST['params']));
        return operation_ok();
    }

    /**
     * segnala un messaggio dalla sfera dimensionale. Necessita dei dati di autenticazione e del messaggio incriminato,
     * nonché del tipo di report. Verrà inviata una mail agli amministratori con tutti i dettagli.
     * @return array
     */
    public static function report_message() {
        return ApplicationService::report_message($_POST['player_id'], $_POST['game_token'], $_POST['message_id'], $_POST['report_type']);
    }
}