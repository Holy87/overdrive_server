<?php


namespace application\services;


use application\repositories\BoardRepository;
use application\repositories\ConfigurationRepository;
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
}