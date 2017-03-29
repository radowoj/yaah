<?php

namespace Radowoj\Yaah\HelperFactory;

use PHPUnit\Framework\TestCase;
use Radowoj\Yaah\Auction;

class FactoryTest extends TestCase
{

    public function providerFactoryClass()
    {
        return [
            ['Factory'],
            ['DebugFactory'],
        ];
    }

    /**
     * @dataProvider providerFactoryClass
     */
    public function testFactoryReturnsHelper($factoryClass)
    {
        $class = "Radowoj\\Yaah\\HelperFactory\\{$factoryClass}";

        $factory = new $class();

        $helper = $factory->create([
            'apiKey' => 'some-api-key',
            'login' => 'some-login',
            'passwordHash' => 'some-password-hash',
            'isSandbox' => true,
            'countryCode' => 1,
        ]);

        $this->assertInstanceOf('Radowoj\Yaah\Helper', $helper);
    }
}
