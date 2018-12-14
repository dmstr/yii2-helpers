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
use yii\helpers\Inflector;

class AssetHash
{
    /**
     * Returns asset hashes for production
     *
     * @return \Closure
     */
    static public function byFileTimeAndLess()
    {
        return function ($path) {
            if (is_file($path)) {
                $files[] = $path;
            } else {
                $files = \yii\helpers\FileHelper::findFiles($path, ['only' => ['*.js', '*.css', '*.less']]);
            }


            $max = 0;
            foreach ($files as $file) {
                $max = max($max, filemtime($file), filectime($file));
            }

            $hash = substr(hash('sha256', $path . $max), 0, 6) .
                '-' . APP_VERSION . '-c' . \Yii::$app->cache->get('prototype.less.changed_at');
            Yii::trace(['byFileTimeAndLess', $path, count($files), Yii::$app->formatter->asRelativeTime($max), $hash],
                       __METHOD__);

            return $hash;
        };
    }

    /**
     * Returns asset hashes for development
     *
     * Format: version git less path time
     *
     *     dev-v1.0.0-ga1b2c3e-l123456/path|to|asset-tf6e5d3
     *
     * @return \Closure
     */
    static public function byFileTimeAndLessDevelopment()
    {
        return function ($path) {
            if (is_file($path)) {
                $files[] = $path;
            } else {
                $files = \yii\helpers\FileHelper::findFiles($path, ['only' => ['*.js', '*.css', '*.less']]);
            }

            $max = 0;
            foreach ($files as $file) {
                $max = max($max, filemtime($file), filectime($file));
            }

            $modificationHash = substr(hash('sha256', $max), 0, 6);

            $hash = 'dev' .
                '-v' . APP_VERSION .
                '-p' . strtr($path, ['/' => '|']) .
                '-t' . $modificationHash;
            Yii::trace(['byFileTimeAndLessDevelopment', $path, count($files), Yii::$app->formatter->asRelativeTime($max), $hash],
                       __METHOD__);

            return $hash;
        };
    }

    /**
     * Returns asset hashes for production
     *
     * Checks mtime, ctime by folder
     *
     * @return \Closure
     */
    static public function byFileTime($obfuscateHash = true)
    {
        return function ($path) use ($obfuscateHash) {
            if (is_file($path)) {
                $files[] = $path;
            } else {
                $files = \yii\helpers\FileHelper::findFiles($path, ['only' => ['*.js', '*.css', '*.less']]);
            }


            $max = 0;
            foreach ($files as $file) {
                $max = max($max, filemtime($file), filectime($file));
            }

            $hash = YII_ENV .
                '-r' . PROJECT_VERSION .
                '-v' . APP_VERSION .
                '-p' . strtr($path, ['/' => '_']) .
                '-t' . $max;
            Yii::trace(['byFileTime', $path, count($files), Yii::$app->formatter->asRelativeTime($max), $hash],
                       __METHOD__);

            if ($obfuscateHash) {
                return md5($hash);
            }

            return $hash;
        };
    }
}