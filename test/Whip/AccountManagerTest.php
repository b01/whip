<?php namespace Whip\Tests;

use Whip\AccountManager;
use Whip\AccountStorage;

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
        $this->accountManager = $this->createMock(AccountManager::class);
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

//        $actual = (string) \serialize($this->accountManager);

        $this->assertEquals('', $actual);
    }


     /**
      * @covers ::isValid
      * @uses \Whip\AccountManager::__construct
      * @uses \Whip\AccountManager::login
      */
     public function testWillCheckIfAnAccountIsValid()
     {
         $this->accountManager->login($this->mockAccountStorage, 'test', '1234');
         $actual = $this->accountManager->isValid();

         $this->assertTrue($actual);
     }

    /**
     * @covers ::__sleep
     * @uses \Whip\AccountManager::__construct
     * @uses \Whip\AccountManager::login
     */
    public function testWillSerialize()
    {
        $this->accountManager->login($this->mockAccountStorage, 'test', '');
        $actual = (string) \serialize($this->accountManager);

        $this->assertContains('a:1:{i:0;s:4:"test";', $actual);
    }
}
