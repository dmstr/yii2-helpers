<?php
/**
 * @link http://www.diemeisterei.de/
 * @copyright Copyright (c) 2017 diemeisterei GmbH, Stuttgart
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace dmstr\helpers;

use yii\web\AssetBundle;
use Yii;
use stdClass;

class SettingsAsset extends AssetBundle
{

    /**
     * @inheritdoc
    */
    public function init()
    {
        parent::init();
        $bundlesFromSettings = Yii::$app->settings->get('settingsAssetList', 'app.assets', '');
        if (!$bundlesFromSettings instanceof stdClass) {
            $bundlesFromSettings = (string)$bundlesFromSettings;
        } else {
            Yii::warning("Asset bundle from settings cannot be json");
            return;
        }
        // Split the list into an array of bundle names and remove whitespace
        $bundles = array_map('trim', explode(PHP_EOL, $bundlesFromSettings));

        foreach ($bundles as $bundle) {
            if (class_exists($bundle) && is_subclass_of($bundle, AssetBundle::class)) {
                $this->depends[] = $bundle;
            } else {
                Yii::warning("Asset bundle '$bundle' from settings does not exist");
            }
        }
    }
}
