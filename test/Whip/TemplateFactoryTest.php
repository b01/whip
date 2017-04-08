<?php namespace Whip\Tests;

use Whip\TemplateFactory;
use PHPUnit\Framework\TestCase;

/**
 * Class TemplateFactoryTest
 *
 * @package Whip\Tests
 * @coversDefaultClass \Whip\TemplateFactory
 */
class TemplateFactoryTest extends TestCase
{
    /** @var \Whip\TemplateFactory */
    private $templateFactory;

    public function setUp()
    {
        $this->templateFactory = $this->getMockForAbstractClass(TemplateFactory::class);
    }

    /**
     * @covers ::setTemplateDir
     * @covers ::getTemplateDir
     */
    public function testWillNotSetTemplateDirectory()
    {
        $fixture = 'bad-diras*!@$';

        $actual = $this->templateFactory::setTemplateDir($fixture);

        $this->assertNotEquals($fixture, $this->templateFactory::getTemplateDir());
        $this->assertFalse($actual);
    }

    /**
     * @covers ::setTemplateDir
     * @covers ::getTemplateDir
     */
    public function testWillSetTemplateDirectory()
    {
        $fixture = __DIR__;

        $this->templateFactory::setTemplateDir($fixture);

        $this->assertEquals($fixture, $this->templateFactory::getTemplateDir());
    }

    /**
     * @covers ::create
     */
    public function xtestWillCallGetRendererMethod()
    {
        $fixture = 'test';

        $this->templateFactory->expects($this->once())
            ->method('getRenderer')
            ->willReturn($fixture);

        $actual = $this->templateFactory::create();

        $this->assertEquals($fixture, $actual);
    }
}
