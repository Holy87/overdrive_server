<?php


namespace application\repositories;


use application\models\Event;
use PDO;

class EventsRepository extends CommonRepository
{
    public static function get_active_events(): array {
        $query = 'select * from events where :today between start_date and end_date';
        $stmt = self::get_connection()->prepare($query);
        $current_date = date('Y-m-d');
        $stmt->bindParam(':today', $current_date, PDO::PARAM_STR);
        $stmt->execute();
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return array_map(function($data) { return new Event($data); }, $results);
    }
}