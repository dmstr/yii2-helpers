<?php

namespace dmstr\helpers;

/*
 * @link http://www.diemeisterei.de/
 * @copyright Copyright (c) 2015 diemeisterei GmbH, Stuttgart
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Class Html.
 *
 * @author Sergej Kunz <s.kunz@herzogkommunikation.de>
 */
class Html extends \yii\helpers\Html
{
    /**
     * Use case:
     * \dmstr\helpers\Html::a("test link", ['create']).
     *
     * @param string $text
     * @param null   $url
     * @param array  $options
     *
     * @return string|null
     */
    public static function a($text, $url = null, $options = [])
    {
        return RouteAccess::can($url, function () use ($text, $url, $options) {
            return parent::a($text, $url, $options);
        }, function () {
            return;
        }, [
            $text,
            $options,
        ]);
    }
}
