<?php

namespace Radowoj\Yaah;

use PHPUnit\Framework\TestCase;
use Radowoj\Yaah\Config;

class ConfigTest extends TestCase
{
    protected $defaultParams = [
        'apiKey' => 'some api key',
        'login' => 'someLogin',
        'passwordHash' => 'passwordHash',
        'isSandbox' => true,
        'countryCode' => 1,
    ];


    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage apiKey is required in params array
     */
    public function testApiKeyRequired()
    {
        $params = $this->defaultParams;
        unset($params['apiKey']);
        new Config($params);
    }


    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage login is required in params array
     */
    public function testLoginRequired()
    {
        $params = $this->defaultParams;
        unset($params['login']);
        new Config($params);
    }

    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage passwordHash is required in params array
     */
    public function testPasswordHashRequired()
    {
        $params = $this->defaultParams;
        unset($params['passwordHash']);
        new Config($params);
    }


    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage isSandbox is required in params array
     */
    public function testIsSandboxRequired()
    {
        $params = $this->defaultParams;
        unset($params['isSandbox']);
        new Config($params);
    }


    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage countryCode is required in params array
     */
    public function testCountryCodeRequired()
    {
        $params = $this->defaultParams;
        unset($params['countryCode']);
        new Config($params);
    }


    public function testGetters()
    {
        $params = $this->defaultParams;
        $config = new Config($params);
        $this->assertSame($params['apiKey'], $config->getApiKey());
        $this->assertSame($params['login'], $config->getLogin());
        $this->assertSame($params['passwordHash'], $config->getPasswordHash());
        $this->assertSame($params['countryCode'], $config->getCountryCode());
        $this->assertSame($params['isSandbox'], $config->getIsSandbox());
    }


    public function testPlaintextPassword()
    {
        $params = $this->defaultParams;
        $config = new Config($params);
        $config->setPasswordPlain('somePlaintextPassword');
        $this->assertSame('somePlaintextPassword', $config->getPasswordPlain());
    }

}
