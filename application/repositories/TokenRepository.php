<?php


namespace application\repositories;


use application\models\FeedbackToken;
use PDO;

class TokenRepository extends CommonRepository
{
    public static function find_token(string $code): ?FeedbackToken {
        $code = self::safe_string($code);
        $query = self::get_connection()->prepare('select * from feedback_tokens where token = :code');
        $query->bindParam(':code', $code);
        $query->execute();
        return  ($query->rowCount() > 0) ? new FeedbackToken($query->fetch(PDO::FETCH_ASSOC)) : null;
    }

    public static function create_token(int $player_id, int $type): ?FeedbackToken {
        $player_id = intval($player_id);
        $type = intval($type);
        $query = self::get_connection()->prepare('insert into feedback_tokens (token, player_id, type) VALUES (:token, :id, :type)');
        $code = generateRandomString(20);
        $query->bindParam(':token', $code);
        $query->bindParam(':id', $player_id);
        $query->bindParam(':type', $type);
        if ($query->execute()) {
            return new FeedbackToken(['token' => $code, 'type' => $type, 'player_id' => $player_id]);
        } else {
            return null;
        }
    }

    public static function delete_token(int $token_id): bool {
        $token_id = intval($token_id);
        $query = self::get_connection()->prepare('delete from feedback_tokens where token_id = :id');
        $query->bindParam(':id', $token_id);
        return $query->execute();
    }

    public static function delete_unlinked_tokens(): bool {
        $query = "delete from feedback_tokens where token not in (select token from chests)";
        return self::get_connection()->prepare($query)->execute();
    }
}