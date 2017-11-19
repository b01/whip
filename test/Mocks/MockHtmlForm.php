<?php namespace Whip\Test\Mocks;
/**
 * Please see the included LICENSE.txt with this source code. If no
 * LICENSE.txt was provided, then all rights for the source code in
 * this file are reserved by Khalifah Khalil Shabazz
 */

use Whip\Form;
use Whip\HtmlForm;

/**
 * Class MockHtmlForm
 *
 * @package \Whip\Test\Mocks
 */
class MockHtmlForm extends HtmlForm
{
    public static $id = 'form-mock';
    public $messages = [];

    /**
     * @inheritDoc
     */
    protected function getMessages(): array
    {
        return $this->messages;
    }

    /**
     * @inheritDoc
     */
    public static function getId(): string
    {
        return self::$id;
    }

    /**
     * @inheritDoc
     */
    public function canSubmit(): bool
    {
        return true;
    }

    /**
     * @inheritDoc
     */
    public function getRenderData(): array
    {
        return parent::getRenderData();
    }

    /**
     * @inheritDoc
     */
    public function getState(): int
    {
        return parent::getState();
    }

    /**
     * @inheritDoc
     */
    public function setInput(array $requestVars): Form
    {
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function submit()
    {
        return $this;
    }

}
