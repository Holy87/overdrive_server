<?php


namespace application\controllers;


use application\services\ApplicationService;

class Application_Controller
{
    public static function index() {

    }

    public static function seed_and_migrate() {

    }

    public static function game_rates() {
        return ApplicationService::calculate_bonus_rates();
    }

    // pulisce le tabelle da dati vecchi non più necessari
    public static function start_cleaning() {
        return ApplicationService::clean_tables();
    }

    public static function status() {
        return ok_response();
    }

    public static function eula() {
        return ApplicationService::load_resource('eula.txt');
    }

    public static function page_test() {
        render();
    }
}