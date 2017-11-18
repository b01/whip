<?php namespace Whip;
/**
 * Please see the included LICENSE.txt with this source code. If no
 * LICENSE.txt was provided, then all rights for the source code in
 * this file are reserved by Khalifah Khalil Shabazz
 */

use Psr\Container\ContainerInterface;

/**
 * Class FormFactory
 *
 * @package \Whip
 */
class FormFactory
{
    /** @var \Psr\Container\ContainerInterface */
    private static $container;

    public static function instantiate(string $fullFormName)
    {
        $form = null;

        if (\class_exists($fullFormName)) {
            $form = $fullFormName::instantiate(self::$container);
        }

        if (!$form instanceof Form) {
            throw new WhipException(
                WhipException::FORM_NOT_FOUND,
                [$fullFormName]
            );
        }

        return $form;
    }

    /**
     * @param ContainerInterface $container
     * @return void
     */
    public static function setContainer(ContainerInterface $container)
    {
        self::$container = $container;
    }
}
