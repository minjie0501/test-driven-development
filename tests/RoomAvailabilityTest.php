<?php 
declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use App\Entity\Room;
use App\Entity\User;


class CheckRoomAvailabilityTest extends TestCase
{
    /**
     * function has to start with Test
     */
    public function testPremiumRoom(): void{
        $room = new Room(false);
        $user = new User(false);

        $this->assertTrue($room->canBook($user));
    }

}