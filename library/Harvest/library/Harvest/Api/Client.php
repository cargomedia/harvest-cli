<?php

class Harvest_Api_Client extends CM_Class_Abstract {

    /** @var string */
    private $_account;

    /** @var string */
    private $_email;

    /** @var string */
    private $_password;

    /**
     * @param string|null $account
     * @param string|null $email
     * @param string|null $password
     */
    public function __construct($account = null, $email = null, $password = null) {
        $config = self::_getConfig();
        if (null === $account) {
            $account = $config->account;
        }
        if (null === $email) {
            $email = $config->email;
        }
        if (null === $password) {
            $password = $config->password;
        }

        $this->_account = (string) $account;
        $this->_email = (string) $email;
        $this->_password = (string) $password;
    }

    /**
     * @param string      $path
     * @param array|null  $query
     * @param string|null $postData
     * @throws CM_Exception
     * @return array
     */
    public function sendRequest($path, array $query = null, $postData = null) {
        $credentials = $this->_email . ':' . $this->_password;
        $url = CM_Util::link('https://' . $this->_account . '.harvestapp.com' . $path, $query);

        $headers = array(
            'Content-type: application/xml',
            'Accept: application/json',
            'Authorization: Basic ' . base64_encode($credentials)
        );

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_TIMEOUT, 60);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_USERAGENT, 'njam/harvest-cli');
        if (null !== $postData) {
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
        }

        $postData = curl_exec($ch);
        if (false === $postData) {
            throw new CM_Exception('Request `' . $query . '` failed: ' . curl_error($ch));
        }
        curl_close($ch);

        return CM_Params::decode($postData, true);
    }
}
