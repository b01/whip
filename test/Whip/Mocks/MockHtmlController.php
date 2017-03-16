<?php namespace Whip\Tests\Mocks;

use Whip\Controllers\Html;

/**
 * Class MockHtmlController
 * @package \Whip\Tests\Mocks
 */
class MockHtmlController extends Html
{
    public function shouldRedirect(string $url)
    {
        return empty($url);
    }
}