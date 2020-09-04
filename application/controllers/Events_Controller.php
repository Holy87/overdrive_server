<?php /** @noinspection PhpUnused */


namespace application\controllers;


use application\repositories\EventsRepository;

class Events_Controller
{
    public static function list() {
        return EventsRepository::get_active_events();
    }
}