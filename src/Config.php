<?php namespace Radowoj\Yaah;

class Config
{
    protected $apiKey = null;

    protected $login = null;

    protected $passwordHash = null;

    protected $isSandbox = null;

    protected $countryCode = null;

    protected $passwordPlain = null;

    public function __construct(array $params)
    {
        if (!array_key_exists('apiKey', $params)) {
            throw new \Exception("apiKey is required in params array");
        }

        $this->apiKey = $params['apiKey'];

        if (!array_key_exists('login', $params)) {
            throw new \Exception("login is required in params array");
        }

        $this->login = $params['login'];

        if (!array_key_exists('passwordHash', $params)) {
            throw new \Exception("passwordHash is required in params array");
        }

        $this->passwordHash = $params['passwordHash'];

        if (!array_key_exists('isSandbox', $params)) {
            throw new \Exception("isSandbox is required in params array");
        }

        $this->isSandbox = $params['isSandbox'];

        if (!array_key_exists('countryCode', $params)) {
            throw new \Exception("countryCode is required in params array");
        }

        $this->countryCode = $params['countryCode'];
    }

    public function getApiKey()
    {
        return $this->apiKey;
    }

    public function getLogin()
    {
        return $this->login;
    }

    public function getPasswordHash()
    {
        return $this->passwordHash;
    }

    public function getIsSandbox()
    {
        return $this->isSandbox;
    }

    public function getCountryCode()
    {
        return $this->countryCode;
    }

    public function getPasswordPlain()
    {
        return $this->passwordPlain;
    }

    public function setPasswordPlain($passwordPlain)
    {
        $this->passwordPlain = $passwordPlain;
    }

}
