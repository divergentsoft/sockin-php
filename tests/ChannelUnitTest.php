<?php


class ChannelUnitTest extends PHPUnit_Framework_TestCase
{

    public function setUp()
    {

        $this->channel = new Sockin\Channel();

    }

    /**
     * @expectedException Sockin\SockinException
     */
    public function testInvalidChannel()
    {
        $this->channel->verify('channelbad&', 'appid');
    }

    public function testBuildChannel()
    {

        $result = $this->channel->verify('test-channel','1234');

        $this->assertEquals('channel:1234:test-channel',$result);
    }


}