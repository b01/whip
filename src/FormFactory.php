<?php namespace Whip;
/**
 * Please see the included LICENSE.txt with this source code. If no
 * LICENSE.txt was provided, then all rights for the source code in
 * this file are reserved by Khalifah Khalil Shabazz
 */

/**
 * Class FormFactory
 *
 * @package \Whip
 */
class FormFactory
{
    /** @var array<callable> The callable MUST return a \Whip\Form */
    private $formInitializer;

    /**
     * FormFactory constructor.
     */
    public function __construct()
    {
        $this->formInitializer = [];
    }

    /**
     * Set a callable to initialize a form.
     *
     * @param string $fullClassName
     * @param callable $initializer
     * @return \Whip\FormFactory
     */
    public function set(
        string $fullClassName,
        callable $initializer,
        bool $overwrite = false
    ) {
        $exists = \class_exists($fullClassName);
        $isWhipForm = $this->isWhipForm($fullClassName);

        // Call the forms static getId() method.
        $formName = $exists && $isWhipForm
            ? \call_user_func($fullClassName . '::getId')
            : null;

        if (\array_key_exists($formName, $this->formInitializer)
            && !$overwrite) {
            throw new WhipException(WhipException::FORM_OVERWRITE, [$fullClassName, $formName]);
        }

        // The name should match the name in the HTTP request.
        $this->formInitializer[$formName] = $initializer;

        return $this;
    }

    /**
     * Determine if a full class name string is of type Whip\Form.
     *
     * @param $fullClassName
     * @return bool
     */
    private function isWhipForm($fullClassName)
    {
        $parents = \class_implements($fullClassName);
        return \in_array(Form::class, $parents, true);
    }

    /**
     * Get a new instance of a form.
     *
     * @param string $formName
     * @return null|Form
     * @throws WhipException
     */
    public function get(string $formName) : ?Form
    {
        $form = null;

        if (\array_key_exists($formName, $this->formInitializer)) {
            $form = $this->formInitializer[$formName]();
        }

        if (!$form instanceOf Form) {
            throw new WhipException(WhipException::FORM_NOT_FOUND, [$formName]);
        }

        return $form;
    }
}
