<?php namespace Whip;

/**
 * Class TemplateFactory
 *
 * @package \Whip
 */
abstract class TemplateFactory
{
    /** @var string */
    protected static $templateDir;

    /**
     * Instantiate a template Renderer.
     *
     * @param \Whip\Renderer
     */
    public static function create() : Renderer
    {
        return self::getRenderer();
    }

    /**
     *
     * @return string
     */
    public static function getTemplateDir()
    {
        return self::$templateDir;
    }

    /**
     * Set directory where templates are stored for this application.
     *
     * @param string $dir
     * @return boolean
     */
    public static function setTemplateDir(string $dir) : bool
    {
        if (file_exists($dir)) {
            self::$templateDir = $dir;

            return true;
        }

        return false;
    }

    /**
     * @return \Whip\Renderer
     */
    abstract protected static function getRenderer() : Renderer;
}
