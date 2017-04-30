<?php namespace Whip;

use Twig_Environment;
use Twig_Loader_Filesystem;

/**
 * Class TwigRenderer
 *
 * @package \BW
 */
final class TwigRenderer implements Renderer
{
    /** @var \Twig_Environment */
    private $renderer;

    /** @var string Path to template, relative to the templates directory. */
    private $templateFile;

    /**
     * TwigRenderer constructor.
     *
     * @param string $templateDir
     */
    public function __construct(Twig_Environment $twig)
    {
        $this->renderer = $twig;
    }

    /**
     * @inheritdoc
     */
    public function addData(array $data) : void
    {
        foreach ($data as $key => $value) {
            $this->renderer->addGlobal($key, $value);
        }
    }

    /**
     * @inheritdoc
     */
    public function render(array & $data = null, bool $raw = false) {
        $template = $this->renderer->load($this->templateFile);

        return $template->render($data);
    }

    /**
     * @inheritdoc
     */
    public function withTemplate(string $templateFile)
    {
        $this->templateFile = $templateFile;

        return $this;
    }
}
