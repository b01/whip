<?php namespace Whip;

use Psr\Http\Message\ServerRequestInterface;

/**
 * Trait Tools
 *
 * @package Whip
 */
trait Tools
{
    /**
     * Extract form input from the Request.
     *
     * @return array
     */
    private function getScrubbedInput(ServerRequestInterface $request) : array
    {
        $getVars = $request->getQueryParams();
        $post = $request->getParsedBody();

        $postVars = \is_array($post)
            ? $post
            : [];

        $fileVars = $request->getUploadedFiles();

        $requestVars = \array_merge($getVars, $postVars, $fileVars);

        return $requestVars;
    }
}