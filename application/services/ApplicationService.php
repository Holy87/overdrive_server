<?php


namespace application\services;


use application\models\Event;
use application\repositories\BoardRepository;
use application\repositories\ConfigurationRepository;
use application\repositories\EventsRepository;
use application\repositories\PlayerRepository;
use services\PlayerService;

class ApplicationService
{
    public static function report_message(int $player_id, string $game_token, int $message_id, int $report_type): int {
        $reporter = PlayerService::authenticate_player($player_id, $game_token);
        if ($reporter == null) return player_unregistered();
        if ($reporter->is_banned()) return banned();
        $message = BoardRepository::get_message($message_id);
        $author = PlayerRepository::get_player_from_id($message->get_author_id());
        return MailService::send_report_message($reporter, $message, $author, $report_type);
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
}