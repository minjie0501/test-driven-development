<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use App\Entity\Room;
use App\Entity\User;


class CheckRoomAvailabilityTest extends TestCase
{
    function dataProviderForPremiumRoom(): array
    {
        return [
            [true, true, true],
            [false, false, true],
            [false, true, true],
            [true, false, false]
        ];
    }

    /**
     * function has to start with Test
     * @dataProvider dataProviderForPremiumRoom
     */
    public function testPremiumRoom(bool $roomVar, bool $userVar, bool $expectedOutput): void
    {

        $room = new Room($roomVar);
        $user = new User($userVar);

        $this->assertEquals($expectedOutput, $room->canBook($user));
    }


    function dataProviderForBookedTime(): array
    {
        return [
            [new DateTime("2018-01-10 02:00:45"), new DateTime("2018-01-10 04:00:45"), true],
            [new DateTime("2018-01-10 12:00:00"), new DateTime("2018-01-10 16:00:00"), true],
            [new DateTime("2018-02-10 02:00:45"), new DateTime("2018-01-10 02:00:45"), false],
            [new DateTime("2018-01-10 02:00:45"), new DateTime("2018-01-10 10:00:45"), false]
        ];
    }

    /**
     * function has to start with Test
     * @dataProvider dataProviderForBookedTime
     */
    public function testBookedTime(DateTime $start, DateTime $end, bool $expectedOutput): void
    {

        $room = new Room(false);
        $d1 = $start;
        $d2 = $end;

        $this->assertEquals($expectedOutput, $room->canBookTimeFrame($d1, $d2));
    }

    function dataProviderForCanAfford(): array
    {
        return [
            [new User(false), 2, 2, false],
            [new User(true), 100, 4, true],
            [new User(false), 12, 3, true],
            [new User(true), 7, 4, false]
        ];
    }

    /**
     * function has to start with Test
     * @dataProvider dataProviderForCanAfford
     */
    public function testCanAfford(User $user, int $credit, int $hoursBooked, bool $expectedOutput): void
    {
        $room = new Room(false);
        $testUser = $user;
        $testUser->setCredit($credit);

        $this->assertEquals($expectedOutput, $room->canAfford($testUser, $hoursBooked));
    }

    // NOTE: php ./vendor/bin/phpunit



    function dataProviderForIsAvailable(): array
    {
        return [
            [new DateTime("2018-01-10 12:00:45"), new DateTime("2018-01-10 14:00:45"), [
                ['start' => new DateTime("2018-01-10 02:00:45"), 'end' => new DateTime("2018-01-10 06:00:45")],
                ['start' => new DateTime("2018-01-10 12:00:45"), 'end' => new DateTime("2018-01-10 13:00:45")],
                ['start' => new DateTime("2018-01-10 14:00:45"), 'end' => new DateTime("2018-01-10 16:00:45")],
            ], false],
            [new DateTime("2018-01-10 12:00:45"), new DateTime("2018-01-10 14:00:45"), [
                ['start' => new DateTime("2018-01-10 08:00:45"), 'end' => new DateTime("2018-01-10 12:00:45")],
                ['start' => new DateTime("2018-01-10 08:00:45"), 'end' => new DateTime("2018-01-10 10:00:45")],
                ['start' => new DateTime("2018-01-10 14:00:45"), 'end' => new DateTime("2018-01-10 19:00:45")],
            ], true],
            [new DateTime("2018-01-10 15:00:45"), new DateTime("2018-01-10 19:00:45"), [
                ['start' => new DateTime("2018-01-10 12:00:45"), 'end' => new DateTime("2018-01-10 15:00:45")],
                ['start' => new DateTime("2018-01-10 11:00:45"), 'end' => new DateTime("2018-01-10 12:00:45")],
                ['start' => new DateTime("2018-01-10 11:00:45"), 'end' => new DateTime("2018-01-10 14:00:45")],
            ], true],
            [new DateTime("2018-01-10 10:00:45"), new DateTime("2018-01-10 14:00:45"), [
                ['start' => new DateTime("2018-01-10 02:00:45"), 'end' => new DateTime("2018-01-10 02:00:45")],
                ['start' => new DateTime("2018-01-10 02:00:45"), 'end' => new DateTime("2018-01-10 02:00:45")],
                ['start' => new DateTime("2018-01-10 12:00:45"), 'end' => new DateTime("2018-01-10 13:00:45")],
            ], false],
        ];
    }

    /**
     * function has to start with Test
     * @dataProvider dataProviderForIsAvailable
     */
    public function testIsAvailable(DateTime $start, DateTime $end, array $reservedDates, bool $expectedOutput): void
    {
        $room = new Room(false);
        $this->assertEquals($expectedOutput, $room->isAvailable($start, $end, $reservedDates));
    }
}
