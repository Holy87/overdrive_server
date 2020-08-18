<?php


namespace application\services;


use application\repositories\BoardRepository;
use application\repositories\ConfigurationRepository;
use application\repositories\PlayerRepository;

class ApplicationService
{
    public static function report_message(string $game_id, int $message_id, int $report_type): int {
        $reporter = PlayerRepository::get_player_from_game($game_id);
        if ($reporter == null) return player_unregistered();
        if ($reporter->is_banned()) return banned();
        $message = BoardRepository::get_message($message_id);
        $author = PlayerRepository::get_player_from_game($message->get_author_id());
        return MailService::send_report_message($reporter, $message, $author, $report_type);
    }

    public static function get_admin_mails(): array {
        return ConfigurationRepository::admin_mails();
    }
}