<?php namespace Whip\Views;

use Whip\Form;
use Kshabazz\Slib\Tools\Utilities;
use Whip\Renderer;

/**
 * Class View
 *
 * @package \Whip\Views
 */
abstract class View
{
    use Utilities;

    /** @var array render data. */
    protected $data;

    /** @var array of \Whip\Form */
    protected $forms;

    /** @var array Query string data when present in the request. */
    protected $queryString;

    /** @var array POST method data when present in the request. */
    protected $postBody;

    /**
     * View constructor.
     *
     * @param \Whip\Renderer $renderer
     */
    public function __construct(Renderer $renderer)
    {
        $this->renderer = $renderer;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->render();
    }

    /**
     * Add placeholder data to the view.
     *
     * @param array $data Must be a single dimension array.
     * @return $this
     */
    public function addData($key, array $data)
    {
        $this->data[$key] = array_merge($this->cleanArray($data));

        return $this;
    }

    /**
     * Render a template.
     *
     * @return string
     */
    public function render()
    {
        $this->renderer->withTemplate($this->getTemplateFile());

        $data = $this->getData();

        return $this->renderer->render($data);
    }

    /**
     * Add a form to the page.
     *
     *  Multiple forms can be added.
     * @param \Whip\Form $form
     * @return $this
     */
    public function addForm(Form $form)
    {
        $this->forms[] = $form;

        return $this;
    }

    /**
     * Get data to fill in placeholders.
     *
     * @return array
     */
    protected function getData()
    {
        // Append any form input and errors to the placeholder data.
        if (is_array($this->forms) && count($this->forms) > 0) {
            foreach($this->forms as $key => $form) {
                $this->data['form'][$key] = $form->getRenderData();
            }
        }

        return $this->data;
    }

    /**
     * Return name of a template file.
     *
     * The directory path where templates are stored will be prefixed to it when trying to locate the file.
     *
     * @return string
     */
    protected abstract function getTemplateFile();
}
