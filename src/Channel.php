<?php

namespace Sockin;


/**
 * Class Channel
 * @package Sockin
 */
class Channel
{

    /**
     * Verify the channel name
     *
     * @param $channel
     * @param $appId
     * @throws SockinException
     * @return string
     */
    public function verify($channel, $appId)
    {
        if (!preg_match('/^[a-zA-Z0-9_-]+$/', $channel)) {

            throw new SockinException('Invalid channel name: ' . $channel);

        }

        return $this->buildChannel($channel,$appId);


    }

    /**
     * Build the channel string
     *
     * @param $appId
     * @return string
     */
    protected function buildChannel($channel,$appId)
    {
        return "channel:" . $appId . ":" . $channel;
    }



} 