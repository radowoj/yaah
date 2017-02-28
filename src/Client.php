<?php

namespace Radowoj\Yaah;

use stdClass;
use SoapClient;
use Radowoj\Yaah\Constants\Sysvars;

class Client
{
    const WSDL_PRODUCTION = 'https://webapi.allegro.pl/service.php?wsdl';
    const WSDL_SANDBOX = 'https://webapi.allegro.pl.webapisandbox.pl/service.php?wsdl';

    protected $config = null;

    protected $soapClient = null;

    protected $allegroSessionHandle = null;

    protected $allegroUserId = null;

    protected $localVersionKey = null;

    public function __construct(Config $config)
    {
        $this->config = $config;

        $this->soapClient = new SoapClient(
            $config->getIsSandbox()
                ? self::WSDL_SANDBOX
                : self::WSDL_PRODUCTION
        );

        $this->login();
    }


    protected function getLocalVersionKey()
    {
        if (is_null($this->localVersionKey)) {
            $response = $this->soapClient->doQuerySysStatus([
                'sysvar' => Sysvars::SYSVAR_CATEGORY_TREE,
                'countryId' => $this->config->getCountryCode(),
                'webapiKey' => $this->config->getApiKey(),
            ]);

            if (!isset($response->verKey)) {
                throw new Exception("Invalid WebAPI doQuerySysStatus() response: " . print_r($response, 1));
            }

            $this->localVersionKey = $response->verKey;
        }

        return $this->localVersionKey;
    }

    protected function getWebApiRequest($data)
    {
        return array_merge($data, [
            'webapiKey'     => $this->config->getApiKey(),
            'localVersion'  => $this->getLocalVersionKey(),

            //for some methods...
            'countryId'     => $this->config->getCountryCode(),
            'sessionId'     => $this->allegroSessionHandle,

            //...for some other methods
            'countryCode'   => $this->config->getCountryCode(),
            'sessionHandle' => $this->allegroSessionHandle,
        ]);
    }

    public function login()
    {
        $data = [
            'userLogin' => $this->config->getLogin()
        ];

        if ($this->config->getPasswordHash()) {
            $data['userHashPassword'] = $this->config->getPasswordHash();
            $response = $this->soapClient->doLoginEnc($this->getWebApiRequest($data));
        } else {
            //ugly non-encrypted way, but sometimes sandbox fails to accept correct hashed login data
            $data['userPassword'] = $this->config->getPasswordPlain();
            $response = $this->soapClient->doLogin($this->getWebApiRequest($data));
        }

        //validate basic response properties
        if (!is_object($response) || !isset($response->sessionHandlePart) || !isset($response->userId)) {
            throw new Exception("Invalid WebAPI doLoginEnc() response: " . print_r($response, 1));
        }

        $this->allegroSessionHandle = $response->sessionHandlePart;
        $this->allegroUserId = $response->userId;
    }


    public function __call($name, $args)
    {
        //prefix with WebAPI "do" prefix
        $name = 'do' . ucfirst($name);

        $request = $this->getWebApiRequest(
            array_key_exists(0, $args) ? $args[0] : []
        );

        try {
            return $this->soapClient->{$name}($request);
        } catch (\Exception $e) {
            throw new Exception('WebAPI exception: ' . $e->getMessage());
        }
    }
}
