<?php
/**
 * @link http://www.diemeisterei.de/
 * @copyright Copyright (c) 2017 diemeisterei GmbH, Stuttgart
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace dmstr\helpers;

use Yii;

class AssetHash
{
    static public function byFileTimeAndLess()
    {
        return function ($path) {
            $files = \yii\helpers\FileHelper::findFiles($path, ['only' => ['*.js', '*.css', '*.less']]);

            $max = 0;
            foreach ($files as $file) {
                $max = max($max, filemtime($file), filectime($file));
            }

            $hash = substr(hash('sha256', $path.$max), 0, 6).
                '-'.APP_VERSION.'-'.\Yii::$app->cache->get('prototype.less.changed_at');
            Yii::trace([$path, count($files), Yii::$app->formatter->asRelativeTime($max), $hash], __METHOD__);

            return $hash;
        };
    }
}