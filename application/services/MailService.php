<?php


namespace application\services;


use application\models\BoardMessage;
use application\models\Player;

class MailService
{
    public const MOTIVES = [
        0 => 'Insulti verso una persona',
        1 => 'Parole di odio o discriminazione',
        2 => 'Spoiler su parti della trama',
        3 => 'Oscenità e/o volgarità'
    ];

    /**
     * Invia una email a destinatari specifici
     * @param string $to destinatario
     * @param string $subject oggetto
     * @param string $message corpo della mail
     * @noinspection PhpUnused
     */
    public static function send_mail(string $to, string $subject, string $message) {
        $mail = MAIL_SENDER.'@'.$_SERVER['HTTP_HOST'];
        $sender = SENDER_NAME;

        $headers = "MIME-Version: 1.0" . "\r\n";
        $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
        $headers .= "From: $sender <$mail>' \r\n";

        mail($to,$subject,$message,$headers);
    }

    /**
     * Invia una email di servizio a tutti gli amministratori della piattaforma
     * @param string $subject oggetto del messaggio
     * @param string $message corpo del messaggio
     */
    public static function send_service_mail(string $subject, string $message) {
        $mail = ADMIN_SENDER.'@'.$_SERVER['HTTP_HOST'];
        $sender = SENDER_NAME;
        $headers = "MIME-Version: 1.0" . "\r\n";
        $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
        $headers .= "From: $sender <$mail>' \r\n";
        $recipients = implode(', ', ApplicationService::get_admin_mails());

        mail($recipients, $subject, $message, $headers);
    }

    public static function send_error_report($report_data) {
        $params = explode("▲", $report_data);
        $data = [
            'player_name' => $params[0],
            'player_id' => $params[1],
            'map_id' => $params[2],
            'troup_id' => $params[3],
            'party' => $params[4],
            'win_kernel' => $params[5],
            'game_version' => $params[6],
            'err_message' => $params[7],
            'err_params' => $params[8],
            'backtrace' => str_replace("¦","\r\n",$params[9]),
            'win_version' => get_windows_name($params[5])
        ];
        $message = self::load_template('error_report', $data);

        self::send_service_mail('Errore in Overdrive', $message);
    }

    public static function send_report_message(Player $reporter, BoardMessage $message, int $type): array {
        $params = [
            'author_name' => $message->get_author()->get_name(),
            'reporter_name' => $reporter->get_name(),
            'message' => base64_decode($message->get_message()),
            'reporter_id' => $reporter->get_game_token(),
            'author_id' => $reporter->get_game_token(),
            'motive' => self::MOTIVES[$type]
        ];
        $body = self::load_template('message_report', $params);
        self::send_service_mail('Segnalazione messaggio sfera dimensionale', $body);
        return operation_ok();
    }


    /**
     * carica un template HTML da inviare come messaggio.
     * I template sono presenti nella directory application/templates/mails.
     * @param string $template_name nome del file html
     * @param array $params parametri da usare come sostituzione delle key {key}
     * @return string
     */
    private static function load_template(string $template_name, array $params): string {
        $template = file_get_contents("../templates/mails/$template_name.html");
        return preg_replace_callback('/{[\s]*(\w+)[\s]*}/m', function($key) use ($params) { return isset($params[$key[1]]) ? $params[$key[1]] : $key[0]; }, $template);
    }
}