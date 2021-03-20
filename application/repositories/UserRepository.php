<?php


namespace application\repositories;


use application\models\User;
use PDO;

class UserRepository extends CommonRepository
{
    public static function create(string $user_name, string $password, string $mail): int {
        $query_str = 'INSERT INTO users (user_name, mail, password) VALUES (:name, :mail, :pass)';
        $password = password_encode(self::safe_string($password));
        $user_name = self::safe_string($user_name);
        $mail = self::safe_string($mail);
        $query = self::get_connection()->prepare($query_str);
        $query->bindParam(':name', $user_name);
        $query->bindParam(':mail', $mail);
        $query->bindParam(':pass', $password);
        return $query->execute() ? self::get_connection()->lastInsertId() : 0;
    }

    public static function find_by_mail(string $mail): ?User {
        return self::find_by('mail', self::safe_string($mail));
    }

    public static function find_by_name(string $name): ?User {
        return self::find_by('user_name', self::safe_string($name));
    }

    public static function find_by_id(int $user_id): ?User {
        return self::find_by('user_id', $user_id);
    }

    public static function find_by(string $param, $value): ?User {
        $query = self::get_connection()->prepare("SELECT * FROM users where $param = :key");
        $query->bindParam(':key', $value);
        $query->execute();
        $result = $query->fetch(PDO::FETCH_ASSOC);

        if ($result && $query->rowCount() > 0) {
            return new User($result);
        } else {
            return null;
        }
    }

    public static function find_with_login(string $query_user, string $password): ?User {
        $query_str = 'select * from users where (user_name = :name or mail = :name) and password = :pass';
        $password = password_encode($password);
        $query_user = self::safe_string($query_user);
        $query = self::get_connection()->prepare($query_str);
        $query->bindParam(':name', $query_user);
        $query->bindParam(':pass', $password);
        $query->execute();
        $result = $query->fetch(PDO::FETCH_ASSOC);
        if ($result && $query->rowCount() > 0) {
            return new User($result);
        } else {
            return null;
        }
    }

    public static function delete_user(int $user_id): bool {
        $query = self::get_connection()->prepare('delete from users where user_id = :id');
        $query->bindParam(':id', $user_id);
        return $query->execute();
    }
}