<?php
/** @noinspection PhpUnused */

namespace application\controllers;


use application\services\ApplicationService;
use application\services\MailService;

class Feedback_Controller
{
    public static function report_error() {
        MailService::send_error_report(base64_decode($_POST['params']));
        return ok_response();
    }

    public static function report_message() {
        return ApplicationService::report_message($_POST['game_id'], $_POST['message_id'], $_POST['report_type']);
    }
}