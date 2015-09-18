<?php

use Sockin\Sockin;

class SockinUnitTest extends PHPUnit_Framework_TestCase
{

    protected function setUp()
    {
        $this->sockin = new Sockin('appid', 'somekey', 'somesecret');

    }


    /**
     * @expectedException Sockin\SockinException
     */
    public function testInvalidChannel()
    {

        $this->sockin->send("badchannel^", 'event', 'data');

    }

    public function testForbiddenAccess()
    {

        $res = json_decode($this->sockin->send('asfd', 'event', 'data'), true);

        $this->assertArrayHasKey("403", $res);

    }

    public function testSuccessMessageString()
    {

        $sockin = new Sockin('12301', 'Khl6yOgoyW9iL2nQID430Nz5v7ngP5OKdh4lbob0', 'SEnLKA5J2lSEQZQOqtEeppmc4K8h3oiJFvtjmAN1');

        $res = json_decode($sockin->send('test-channel', 'test-event', 'test string message'), true);

        $this->assertArrayHasKey("200", $res);
    }

    public function testSuccessMessageArray()
    {

        $sockin = new Sockin('12301', 'Khl6yOgoyW9iL2nQID430Nz5v7ngP5OKdh4lbob0', 'SEnLKA5J2lSEQZQOqtEeppmc4K8h3oiJFvtjmAN1');

        $res = json_decode($sockin->send('test-channel', 'test-event', ['key' => 'value']), true);

        $this->assertArrayHasKey("200", $res);
    }

    public function testSuccessMessageJSON(){

        $sockin = new Sockin('12301','Khl6yOgoyW9iL2nQID430Nz5v7ngP5OKdh4lbob0','SEnLKA5J2lSEQZQOqtEeppmc4K8h3oiJFvtjmAN1');

        $res = json_decode($sockin->send('test-channel','test-event','{"key":"value"}'),true);

        $this->assertArrayHasKey("200",$res);
    }

    public function testSuccessMissingParam(){

        $sockin = new Sockin('12301','Khl6yOgoyW9iL2nQID430Nz5v7ngP5OKdh4lbob0','SEnLKA5J2lSEQZQOqtEeppmc4K8h3oiJFvtjmAN1');

        $res = json_decode($sockin->send('test-channel','','test string message'),true);

        $this->assertArrayHasKey("400",$res);
    }

    public function testVersion()
    {
        $this->assertEquals('0.1', $this->sockin->getVersion());

    }

}