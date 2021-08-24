<?php

declare(strict_types=1);

/*
 * This file is part of the ekino/sonata project.
 *
 * (c) Ekino
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sonata\HelpersBundle\TestHelpers\Common;

/**
 * Class TranslationTestHelper.
 *
 * @author Sylvain Rascar <sylvain.rascar@ekino.com>
 */
class TranslationTestHelper
{
    /**
     * @param string   $key
     * @param string[] $params
     * @param string   $domain
     *
     * @return string
     */
    public static function getTranslationString($key, $params, $domain)
    {
        $translationFormat = self::getTranslationFormat();

        return sprintf($translationFormat, $key, json_encode($params), $domain);
    }

    /**
     * @return string
     */
    private static function getTranslationFormat()
    {
        return <<<TRANSLATION
Key    : %s
Params : %s
Domain : %s

TRANSLATION;
    }
}
