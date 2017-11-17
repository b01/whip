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
    /** @var array<\Whip\Form> */
    private $forms;

    /** @var array<callable> The callable MUST return a \Whip\Form */
    private $formInitializer;

    /**
     * Set a callable to initialize a form.
     *
     * @param string $formName
     * @param callable $callback
     */
    public function set(string $formName, callable $callback)
    {
        $this->formInitializer[$formName] = $callback;
    }

    /**
     * Get a form.
     *
     * @param $formName
     * @return \Whip\Form|null
     */
    public function get(string $formName) : ?Form
    {
        $form = \array_key_exists($formName, $this->forms)
            ? $this->forms[$formName]
            : $this->instantiateForm($formName);

        return $form;
    }

    /**
     * @param string $formName
     * @return \Whip\Form|null
     * @throws \Whip\WhipException
     */
    public function instantiateForm(string $formName) : ?Form
    {
        $form = null;

        if (\array_key_exists($formName, $this->formInitializer)) {
            $form = $this->formInitializer[$formName]();
        }

        if ($form instanceOf Form) {
            throw new WhipException(WhipException::FORM_NOT_FOUND, [$formName]);
        }

        return $form;
    }
}
