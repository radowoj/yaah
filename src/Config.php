<?php namespace Radowoj\Yaah;

class Config
{
    /**
      * @var string Allegro WebAPI key
      */
    protected $apiKey = null;

    /**
      * @var string Allegro login
      */
    protected $login = null;

    /**
      * @var string Allegro password hash (base64 of sha-256 of plaintext password)
      */
    protected $passwordHash = null;

    /**
     * @var boolean Use sandbox or production
     */
    protected $isSandbox = null;

    /**
     * @var string country code required by WebAPI for certain operations
     */
    protected $countryCode = null;

    /**
     * @var string plain text password
     * Useful for those moments when sandbox refuses to work with hashed password
     * ("wrong username or password" exception despite using correct credentials).
     * It is not recommended to use it otherwise, always keep hashed, not plaintext
     * password in your config for safety reasons.
     */
    protected $passwordPlain = null;


    public function __construct(array $params)
    {
        $requiredParams = ['apiKey', 'login', 'passwordHash', 'isSandbox', 'countryCode'];

        foreach($requiredParams as $property) {
            if (!array_key_exists($property, $params)) {
                throw new Exception("{$property} is required in params array");
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
