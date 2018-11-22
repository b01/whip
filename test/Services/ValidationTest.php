<?php namespace Whip\Test\Services;

use PHPUnit\Framework\TestCase;
use Whip\Services\Validation;

/**
 * Class ValidationTest
 *
 * @package \Whip\Test\Services
 * @coversDefaultClass \Whip\Services\Validation
 */
class ValidationTest extends TestCase
{
    /** @var array Fixed rules to test. */
    private $fixtureRules;

    public function setUp()
    {
        $this->fixtureRules = [
            'numericField' => ['type' => 'range', 'constraint' => [1, 100]],
            'stringField' => ['type' => 'string', 'constraint' => 5],
            'regexField' => ['type' => 'regex', 'constraint' => '[a-z]{1,3}'],
            'setField' => ['type' => 'set', 'constraint' => ['item1', 'item2', 'item3']],
        ];
    }

    /**
     * @covers ::__construct
     */
    public function testCanInitialize()
    {
        $sut = new Validation();

        $this->assertInstanceOf(Validation::class, $sut);
    }

    /**
     * @covers ::validate
     * @uses \Whip\Services\Validation::__construct
     */
    public function testCanValidateNumericValueIsWithinInRange()
    {
        $sut = new Validation();

        $sut->validate(['numericField' => 21]);
    }

    /**
     * @covers ::validate
     * @uses \Whip\Services\Validation::__construct
     */
    public function testCanInvalidateNumericValueIsOutOfRange()
    {
        $sut = new Validation();

        $sut->validate(['numericField' => -1]);
    }

    /**
     * @covers ::validate
     * @uses \Whip\Services\Validation::__construct
     */
    public function testCanValidateString()
    {
        $sut = new Validation();

        $sut->validate(['stringField' => 'test']);
    }

    /**
     * @covers ::validate
     * @uses \Whip\Services\Validation::__construct
     */
    public function testCanInvalidateString()
    {
        $sut = new Validation();

        $sut->validate(['stringField' => 'test']);
    }

    /**
     * @covers ::validate
     * @uses \Whip\Services\Validation::__construct
     */
    public function testCanValidateStringAsPartOfASet()
    {
        $sut = new Validation();

        $sut->validate(['setField' => 'item1']);
    }

    /**
     * @covers ::validate
     * @uses \Whip\Services\Validation::__construct
     */
    public function testCanInvalidateAStringIsNotInASet()
    {
        $sut = new Validation();

        $sut->validate(['setField' => 'item1']);
    }

    /**
     * @covers ::validate
     * @uses \Whip\Services\Validation::__construct
     */
    public function testCanValidateAValuePassesARegularExpression()
    {
        $sut = new Validation();

        $sut->validate(['regexField' => 'item1']);
    }

    /**
     * @covers ::validate
     * @uses \Whip\Services\Validation::__construct
     */
    public function testCanInvalidateAValueFailsARegularExpression()
    {
        $sut = new Validation();

        $sut->validate(['setField' => 'item1']);
    }

    /**
     * @covers ::addRules
     * @uses \Whip\Services\Validation::__construct
     */
    public function testCanAddMultipleRules()
    {
        $sut = new Validation();

        $actual = $sut->addRules($this->fixtureRules);

        $this->assertCount($actual);
    }

    /**
     * @covers ::addRule
     * @uses \Whip\Services\Validation::__construct
     */
    public function testCanAddASingleRule()
    {
        $sut = new Validation();

        $actual = $sut->addRule('test', 'range', [1,5]);
        $actual2 = $sut->validate(['test' => 2]);

        $this->assertTrue($actual);
        $this->assertTrue($actual2);
    }

    /**
     * @covers ::addCustomType
     * @uses \Whip\Services\Validation::__construct
     */
    public function testCanAddACustomRule()
    {
        $sut = new Validation();

        $actual = $sut->addCustomType(
            'customType',
            function ($value) {
            }
        );

        $actual2 = $sut->addRule(
            'customField',
            'customType',
            function ($value) {
                $value = (int) $value;
                return $value !== 0 && $value % 2 === 0;
            }
        );

        $actual3 = $sut->validate(['test' => 2]);

        $this->assertTrue($actual);
        $this->assertTrue($actual2);
        $this->assertTrue($actual3);
    }
}
