<?php namespace Whip;

/**
 * Class View
 *
 * @package \Whip\Views
 */
abstract class View
{
    protected $data;

    /** @var \Whip\Renderer */
    protected $renderer;

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
     * @return \Whip\View
     */
    public function addData($key, array $data)
    {
        $this->data[$key] = array_merge($data);

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

        return $this->renderer->render($data);
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
