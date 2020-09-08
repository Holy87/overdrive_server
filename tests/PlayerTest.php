<?php declare (strict_types=1);
include 'autorequire.php';
use application\models\Player;
use PHPUnit\Framework\TestCase;

final class PlayerTest extends TestCase {

    /** @test */
    public function playerCanBeCreatedFromHash(): void {
        $player = new Player(['player_id'=>23, 'player_name'=>'Mario']);
        $this->assertEquals('Mario', $player->get_name());
    }
}