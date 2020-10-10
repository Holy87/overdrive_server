<?php


namespace application\services;


use application\models\Event;
use application\repositories\BoardRepository;
use application\repositories\ConfigurationRepository;
use application\repositories\EventsRepository;
use application\repositories\NotificationRepository;
use services\PlayerService;

class ApplicationService
{
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

    public static function clean_tables() {
        if (!NotificationRepository::delete_old_notifications()) return operation_failed(1);
        return operation_ok();
    }
}