<?php

namespace Radowoj\Yaah;

use InvalidArgumentException;

class Config
{
    /**
     * Allegro WebAPI key
     * @var string
     */
    protected $apiKey = null;

    /**
     * Allegro login
     * @var string
     */
    protected $login = null;

    /**
     * Allegro password hash (base64 of sha-256 of plaintext password)
     * @var string
     */
    protected $passwordHash = null;

    /**
     * Whether to use sandbox
     * @var boolean
     */
    protected $isSandbox = null;

    /**
     * @var string country code required by WebAPI for certain operations
     */
    /**
     * [$countryCode description]
     * @var [type]
     */
    protected $countryCode = null;

    /**
     * Plain text password
     * Useful for those moments when sandbox refuses to work with hashed password
     * ("wrong username or password" exception despite using correct credentials).
     * It is not recommended to use it otherwise, always keep hashed, not plaintext
     * password in your config for safety reasons.
     * @var string
     */
    protected $passwordPlain = null;


    public function __construct(array $params)
    {
        $requiredParams = ['apiKey', 'login', 'passwordHash', 'isSandbox', 'countryCode'];

        foreach ($requiredParams as $property) {
            if (!array_key_exists($property, $params)) {
                throw new InvalidArgumentException("{$property} is required in params array");
            }

            $this->{$property} = $params[$property];
        }
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
