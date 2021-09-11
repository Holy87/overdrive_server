<?php


namespace application\controllers;


use application\repositories\BoardRepository;
use application\repositories\ConfigurationRepository;
use application\repositories\PlayerRepository;
use application\services\ApplicationService;
use services\PlayerService;

class Page_Controller
{
    public static function index() {
        $page = $_GET["page"];
        render('index', $_GET, $page);
    }

    public static function action() {
        $page = 'user_alert';
        $_SESSION['message'] = BoardRepository::get_message(23);
        $token = ConfigurationRepository::get_action_url($_GET['token']);
        if ($token && $token['action'] == ApplicationService::REPORT_MESSAGE_ACTION) {
            $_SESSION['action'] = $token;
            render($page, $_GET, 'action');
        } else {
            //TODO: Inserire redirect su 404
        }
    }
}