<?php namespace Whip\Tests;

use Whip\AccountManager;
use Whip\AccountStorage;
use Whip\Tests\Mocks\MockAccountManager;

/**
 * Class AccountManagerTest
 *
 * @package \Whip\Tests
 * @coversDefaultClass \Whip\AccountManager
 */
class AccountManagerTest extends \PHPUnit\Framework\TestCase
{
    /** @var \Whip\AccountManager|\PHPUnit_Framework_MockObject_MockObject */
    private $accountManager;

    /** @var \Whip\AccountStorage|\PHPUnit_Framework_MockObject_MockObject */
    private $mockAccountStorage;

    public function setUp()
    {
        $this->mockAccountStorage = $this->createMock(AccountStorage::class);
        $this->accountManager = $this->getMockForAbstractClass(AccountManager::class);
    }

    /**
     * @covers ::__construct
     */
    public function testWillInitializeAAccountManagerObject()
    {
        $this->assertInstanceOf(AccountManager::class, $this->accountManager);
    }


    /**
     * @covers ::login
     * @uses \Whip\AccountManager::__construct
     */
    public function testWillSuccessfullyLogin()
    {
        $this->mockAccountStorage->expects($this->once())
            ->method('lookup')
            ->with('test', '1234')
            ->willReturn(true);

        $actual = $this->accountManager->login($this->mockAccountStorage, 'test', '1234');

        $this->assertEquals(101, strlen($actual));
    }


     /**
      * @covers ::isLoggedIn
      * @uses \Whip\AccountManager::__construct
      * @uses \Whip\AccountManager::login
      */
     public function testWillCheckIfAnAccountIsValid()
     {
         $this->mockAccountStorage->expects($this->once())
             ->method('lookup')
             ->with('test', '1234')
             ->willReturn(true);

         $this->accountManager->login($this->mockAccountStorage, 'test', '1234');

         $actual = $this->accountManager->isLoggedIn();

         $this->assertTrue($actual);
     }

    /**
     * @covers ::__sleep
     * @uses \Whip\AccountManager::__construct
     * @uses \Whip\AccountManager::login
     */
    public function testWillSerialize()
    {
        $this->mockAccountStorage->expects($this->once())
            ->method('lookup')
            ->with('test', '1234')
            ->willReturn(true);

        $this->accountManager->login($this->mockAccountStorage, 'test', '1234');

        $actual = (string) \serialize($this->accountManager);

        $this->assertEquals(212, strlen($actual));
    }

    /**
     * @covers ::__wakeup
     * @uses \Whip\AccountManager::__construct
     * @uses \Whip\AccountManager::login
     */
    public function testWillUnserialize()
    {
        $this->mockAccountStorage->expects($this->once())
            ->method('lookup')
            ->with('test', '1234')
            ->willReturn(true);

        $this->accountManager->login($this->mockAccountStorage, 'test', '1234');

        $serialized = \serialize($this->accountManager);
        $actual = \unserialize($serialized);

        $this->assertTrue($actual->isLoggedIn());
    }
}
