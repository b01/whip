<?php namespace Whip\Test;

/**
 * Please see the included LICENSE.txt with this source code. If no
 * LICENSE.txt was provided, then all rights for the source code in
 * this file are reserved by Khalifah Khalil Shabazz
 */

use Whip\FormFactory;
use PHPUnit\Framework\TestCase;
use Whip\Lash\Validator;
use Whip\Test\Mocks\MockHtmlForm;

/**
 * Class FormFactoryTest
 *
 * @package \Whip\Test
 * @coversDefaultClass \Whip\FormFactory
 */
class FormFactoryTest extends TestCase
{
    /**
     * @covers ::__construct
     */
    public function testCanInitialize()
    {
        $sut = new FormFactory();

        $this->assertInstanceOf(FormFactory::class, $sut);
    }

    /**
     * @covers ::set
     * @covers ::get
     * @use \Whip\FormFactory::__construct
     */
    public function testCanSetAForm()
    {
        $sut = new FormFactory();
        $mockValidator = $this->createMock(Validator::class);
        $fixtureForm = new MockHtmlForm($mockValidator);

        $sut->set(MockHtmlForm::class, function () use($fixtureForm) {
            return $fixtureForm;
        });

        $actual = $sut->get(MockHtmlForm::getId());

        $this->assertEquals($fixtureForm, $actual);
    }
}
