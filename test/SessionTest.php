<?php namespace BW\Test;

/**
 * Please see the included LICENSE.txt with this source code. If no
 * LICENSE.txt was provided, then all rights for the source code in
 * this file are reserved by Khalifah Khalil Shabazz
 */

use Whip\Session;
use PHPUnit\Framework\TestCase;

/**
 * Class SessionTest
 *
 * @package \Whip\Test
 * @coversDefaultClass \Whip\Session
 */
class SessionTest extends TestCase
{
    /** @var \Whip\Session */
    private $sut;

    public function setUp()
    {
        $this->sut = $this->getMockForTrait(Session::class);
    }

    /**
     * @covers ::getSessionVal
     */
    public function testGetWithoutSettingWillReturnDefault()
    {
        $fixture = 'test';

        $actual = $this->sut->getSessionVal($fixture, '1234');

        $this->assertEquals('1234', $actual);
    }

    /**
     * @covers ::setSessionVal
     * @uses \Whip\Session::getSessionVal
     */
    public function testSet()
    {
        $key = 'test';

        $this->sut->setSessionVal($key, '1234');
        $actual = $this->sut->getSessionVal($key);

        $this->assertEquals('1234', $actual);
    }

    /**
     * @covers ::setSessionVal
     * @expectedException \Exception
     */
    public function testSetThrowsAnException()
    {
        $this->sut->setSessionVal('test', new \stdClass());
    }

    /**
     * @covers ::setSessionVal
     */
    public function testSuccessfullySetsValueInTheGlobalSessionArray()
    {
        $this->sut->setSessionVal('test', '1234');

        $actual = $_SESSION['test'];

        $this->assertEquals('1234', $actual);
    }

    /**
     * @covers ::getSessionVal
     */
    public function testSuccessfullySetsValueInTheGlobalSessionArrayAndGetIt()
    {
        $this->sut->setSessionVal('test', '1234');

        $actual = $this->sut->getSessionVal('test');

        $this->assertEquals('1234', $actual);
    }

    /**
     * @covers ::getArrayFromSession
     * @expectedException \Whip\WhipException
     */
    public function testWillFailToGetAnUnencodedArrayFromSession()
    {
        $_SESSION['sdfsd'] = ['oe'=>''];

        $this->sut->getArrayFromSession('sdfsd');
    }

    /**
     * @covers ::getArrayFromSession
     */
    public function testGetArrayFromSessionWillDefaultToNullWhenNotSetInSession()
    {
        unset($_SESSION['tkjhtest']);

        $actual = $this->sut->getArrayFromSession('tkjhtest');

        $this->assertNull($actual);
    }

    /**
     * @covers ::getArrayFromSession
     */
    public function testCanGetArrayFromSession()
    {
        $fixture = ['er' => 123];
        $_SESSION['zxcv'] = \json_encode($fixture);

        $actual = $this->sut->getArrayFromSession('zxcv');

        $this->assertSame($fixture, $actual);
    }

    /**
     * @covers ::setArrayInSession
     * @uses \Whip\SessionWrapper::getArrayFromSession
     */
    public function testCanSGetArrayFromSession()
    {
        $fixtureKey = '123test';
        $fixture = ['er' => 123];

        $this->sut->setArrayInSession($fixtureKey, $fixture);

        $actual = $this->sut->getArrayFromSession($fixtureKey);

        $this->assertSame($fixture, $actual);
    }
}
