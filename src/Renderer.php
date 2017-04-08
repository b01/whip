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
     * @return array List of all data added thus far.
     */
    public function addData(array $data);

    /**
     * Render the template.
     *
     * @param array|null $data Data that will be passed to the template engine to fill in placeholders.
     * @param boolean $raw Turn off/on escaping of special characters in the output.
     * @return string
     */
    public function render(array & $data = null, bool $raw = false);

    /**
     * @param string $template
     * @return $this
     */
    public function withTemplate(string $template);
}
