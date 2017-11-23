<?php namespace BW\Test;

/**
 * Please see the included LICENSE.txt with this source code. If no
 * LICENSE.txt was provided, then all rights for the source code in
 * this file are reserved by Khalifah Khalil Shabazz
 */

use Whip\Session;
use PHPUnit\Framework\TestCase;
use Whip\SessionWrapper;
use Whip\Test\Mocks\MockSession;

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
     * @covers ::getVal
     */
    public function testGetWithoutSettingWillReturnDefault()
    {
        $fixture = 'test';

        $actual = $this->sut->getVal($fixture, '1234');

        $this->assertEquals('1234', $actual);
    }

    /**
     * @covers ::setVal
     * @uses \Whip\Session::getVal
     */
    public function testSet()
    {
        $key = 'test';

        $this->sut->setVal($key, '1234');
        $actual = $this->sut->getVal($key);

        $this->assertEquals('1234', $actual);
    }

    /**
     * @covers ::setVal
     * @expectedException \Exception
     */
    public function testSetThrowsAnException()
    {
        $this->sut->setVal('test', new \stdClass());
    }

    /**
     * @covers ::setVal
     */
    public function testSuccessfullySetsValueInTheGlobalSessionArray()
    {
        $this->sut->setVal('test', '1234');

        $actual = $_SESSION['test'];

        $this->assertEquals('1234', $actual);
    }

    /**
     * @covers ::getVal
     */
    public function testSuccessfullySetsValueInTheGlobalSessionArrayAndGetIt()
    {
        $this->sut->setVal('test', '1234');

        $actual = $this->sut->getVal('test');

        $this->assertEquals('1234', $actual);
    }

    /**
     * @covers ::getArray
     * @expectedException \Whip\WhipException
     */
    public function testWillFailToGetAnUnencodedArrayFromSession()
    {
        $_SESSION['sdfsd'] = ['oe'=>''];

        $this->sut->getArray('sdfsd');
    }

    /**
     * @covers ::getArray
     */
    public function testGetArrayFromSessionWillDefaultToNullWhenNotSetInSession()
    {
        unset($_SESSION['tkjhtest']);

        $actual = $this->sut->getArray('tkjhtest');

        $this->assertNull($actual);
    }

    /**
     * @covers ::getArray
     */
    public function testCanGetArrayFromSession()
    {
        $fixture = ['er' => 123];
        $_SESSION['zxcv'] = \json_encode($fixture);

        $actual = $this->sut->getArray('zxcv');

        $this->assertSame($fixture, $actual);
    }

    /**
     * @covers ::setArray
     * @uses \Whip\SessionWrapper::getArray
     */
    public function testCanSetArrayFromSession()
    {
        $fixtureKey = '123test';
        $fixture = ['er' => 123];

        $this->sut->setArray($fixtureKey, $fixture);

        $actual = $this->sut->getArray($fixtureKey);

        $this->assertSame($fixture, $actual);
    }

    /**
     * @covers ::withSession
     */
    public function testWithSessionMethod()
    {
        $sut = $this->createMock(SessionWrapper::class);

        $sessionUser = new MockSession();

        $sessionUser->withSession($sut);

        $actual = $sessionUser->getSession();

        $this->assertSame($sut, $actual);
    }
}
