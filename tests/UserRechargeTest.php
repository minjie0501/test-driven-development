<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use App\Entity\User;

//class has to end with Test
class UserRechargeTest extends TestCase
{
    function dataProviderForUserRecharge(): array
    {
        return [
            [12, true],
            [-23, false],
            [0, false],
            [123, true]
        ];
    }

    /**
     * function has to start with Test
     * @dataProvider dataProviderForUserRecharge
     */
    public function testUserRecharge(int $amount, bool $expectedOutput): void
    {
        $user = new User(false);

        $this->assertEquals($expectedOutput, $user->canRecharge($amount));
    }
}
