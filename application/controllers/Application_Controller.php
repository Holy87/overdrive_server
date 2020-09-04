<?php


namespace application\controllers;


use application\services\ApplicationService;

class Application_Controller
{
    public static function seed_and_migrate() {

    }

    public static function game_rates() {
        return ApplicationService::calculate_bonus_rates();
    }
}