<?php

namespace Sockin;


/**
 * Class Sockin
 * @package Sockin
 */
class Sockin
{

    /**
     * @var The application ID, obtained from the app dashboard
     */
    protected $appId;

    /**
     * @var The application public key, from the app dashboard
     */
    protected $appKey;

    /**
     * @var The application private key, from the app dashboard, keep this safe
     */
    protected $appSecret;

    /**
     * @var \GuzzleHttp\Client
     */
    protected $guzzleClient;

    /**
     * @var Channel
     */
    protected $channel;

    /**
     * @var The current path appended to the base URI
     */
    protected $route;


    /**
     * Create the Sockin object
     *
     * Sockin constructor.
     * @param $appId
     * @param $appKey
     * @param $appSecret
     */
    public function __construct($appId, $appKey, $appSecret)
    {
        $this->check_compatibility();

        $this->appId = $appId;

        $this->appKey = $appKey;

        $this->appSecret = $appSecret;

        $this->guzzleClient = new \GuzzleHttp\Client();

        $this->channel = new Channel();
    }

    /**
     * Send an message on a channel for an event
     *
     * @param $channel
     * @param $event
     * @param $data
     * @throws SockinException
     * @return string
     */
    public function send($channel, $event, $data)
    {
        $this->route = "send";

        $codedChannel = $this->channel->verify($channel, $this->appId);

        $jsonData = $this->getJson($data);

        $hashedData = $this->hashData($jsonData, $codedChannel, $event);

        return $this->dispatchMessage($jsonData, $codedChannel, $event, $hashedData);

    }

    /**
     * Force the data to JSON
     *
     * @param $data
     * @return string
     */
    protected function getJson($data)
    {
        if ($this->isJson($data)) {

            return $data;
        }

        return $this->toJson($data);

    }

    /**
     * Hash some data with the private key
     *
     * @param $data
     * @param $codedChannel
     * @param $event
     * @return string
     */
    protected function hashData($data, $codedChannel, $event)
    {
        return hash_hmac("sha256", $data . $codedChannel . $event, $this->appSecret);
    }


    /**
     * Send the message to the server
     *
     * @param $jsonData
     * @param $channel
     * @param $event
     * @param $hashedData
     * @throws SockinException
     * @return string
     */
    protected function dispatchMessage($jsonData, $channel, $event, $hashedData)
    {
        $url = Settings::BASE_URL . $this->route;

        $res = $this->guzzleClient->request('POST', $url, ['form_params' => [

            'app_id' => $this->appId,
            'app_key' => $this->appKey,
            'hash' => $hashedData,
            'channel' => $channel,
            'event' => $event,
            'data' => $jsonData

        ]]);

        if ($res->getStatusCode() != 200) {

            throw new SockinException("Error sending message");
        }

        return $res->getBody();


    }


    /**
     * Encode the data as JSON
     *
     * @param $data
     * @return string
     * @throws SockinException
     */
    protected function toJson($data)
    {
        $json = json_encode($data);

        if (json_last_error() !== JSON_ERROR_NONE) {

            throw new SockinException('Could not convert data to JSON');
        }

        return $json;
    }

    /**
     * Check whether our data is already in JSON format
     *
     * @param $data
     * @return bool
     */
    protected function isJson($data)
    {
        if (is_array($data)) {

            return false;
        }

        if (is_string($data) && json_decode($data) && json_last_error() == JSON_ERROR_NONE) {

            return true;
        }

        return false;
    }

    /**
     * Make sure we are setup to run properly
     *
     * @throws SockinException
     */
    protected function check_compatibility()
    {
        if (!in_array('sha256', hash_algos())) {

            throw new SockinException('SHA256 support required to ensure data security, please update your PHP installation to include it.');
        }
    }

    /**
     * The lib version
     *
     * @return string
     */
    public function getVersion()
    {
        return Settings::VERSION;
    }


}
