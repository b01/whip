<?php namespace Whip;
/**
 * Please see the included LICENSE.txt with this source code. If no
 * LICENSE.txt was provided, then all rights for the source code in
 * this file are reserved by Khalifah Khalil Shabazz
 */

use Whip\Renderer;
use Whip\View;

/**
 * Class HtmlView
 *
 * @package \Whip\Views
 */
abstract class HtmlView extends View
{
    /**
     * @inheritdoc
     */
    public function __construct(string $title, Renderer $renderer)
    {
        parent::__construct($renderer);

        $this->addData('title', $title);
    }
}
