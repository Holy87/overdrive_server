<?php


namespace application\services;


use application\models\Event;
use application\repositories\BoardRepository;
use application\repositories\ConfigurationRepository;
use application\repositories\EventsRepository;
use application\repositories\NotificationRepository;
use application\repositories\TokenRepository;
use PHPUnit\TextUI\Configuration\Configuration;
use services\PlayerService;

class ApplicationService
{
    const REPORT_MESSAGE_ACTION = 'REPORT';
    const CHANGE_PASSWORD_ACTION = 'CHANGE_PWD';
    const CONFIRM_EMAIL_ACTION = 'CONFIRM_MAIL';


    public static function report_message(int $message_id, int $report_type): array {
        $reporter = PlayerService::get_logged_player();
        $message = BoardRepository::get_message($message_id);
        return MailService::send_report_message($reporter, $message, $report_type);
    }

    public static function get_admin_mails(): array {
        return ConfigurationRepository::admin_mails();
    }

    public static function calculate_bonus_rates(): array {
        $rates = ConfigurationRepository::get_game_rates();
        $events = EventsRepository::get_active_events();
        /** @var Event $event */
        foreach ($events as $event) {
            $rates['exp_rate'] += $event->getExpRate() - 100;
            $rates['drop_rate'] += $event->getDropRate() - 100;
            $rates['gold_rate'] += $event->getGoldRate() - 100;
            $rates['jp_rate'] += $event->getJpRate() - 100;
        }
        return $rates;
    }

    public static function load_resource(string $resource_name): string {
        $path = './resources/'.$resource_name;
        $file = fopen($path, "r") or die("Non riesco ad aprire il file");
        $content = fread($file,filesize($path));
        fclose($file);
        return $content;
    }

    public static function create_confirmation_mail_action(int $player_id): string {
        return self::create_action(self::CONFIRM_EMAIL_ACTION, [$player_id]);
    }

    public static function create_password_change_action(int $player_id) {
        return self::create_action(self::CHANGE_PASSWORD_ACTION, [$player_id]);
    }

    public static function create_message_report_action(int $message_id): string {
        return self::create_action(self::REPORT_MESSAGE_ACTION, [$message_id]);
    }

    public static function create_action(string $action, array $params): ?string {
        $params_comma = implode(",", $params);
        return ConfigurationRepository::create_action_url($action,$params_comma);
    }

    /**
     * Questo metodo elimina i record vecchi non più utili in modo da tenere il database
     * snello e leggero.
     * @return array
     */
    public static function clean_tables() {
        if (!NotificationRepository::delete_old_notifications()) return operation_failed(1);
        if (!TokenRepository::delete_unlinked_tokens()) return operation_failed(2);
        return operation_ok();
    }
}