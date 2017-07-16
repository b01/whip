<?php namespace Whip;

/**
 * Interface Renderer
 *
 * @package \Whip
 */
interface Renderer
{
    /**
     * Add data to the template engine.
     *
     * @param array $data
     * @return \Whip\Renderer
     */
    public function addData(array $data) : Renderer;

    /**
     * Render the template.
     *
     * @param array|null $data Data that will be passed to the template engine to fill in placeholders.
     * @param boolean $raw Turn off/on escaping of special characters in the output.
     * @return string
     */
    public function render(array & $data = null, bool $raw = false) : string;

    /**
     * @param string $template
     * @return \Whip\Renderer
     */
    public function withTemplate(string $template) : Renderer;
}
