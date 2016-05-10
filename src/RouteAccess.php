<?php

namespace dmstr\helpers;

/*
 * @link http://www.diemeisterei.de/
 * @copyright Copyright (c) 2015 diemeisterei GmbH, Stuttgart
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use yii\base\InvalidParamException;
use Yii;
use yii\base\Action;
use yii\web\Controller;

/**
 * Class RouteAccess.
 *
 * @author Sergej Kunz <s.kunz@herzogkommunikation.de>
 */
class RouteAccess
{
    protected static $_accessCache = [];

    /**
     * @param $route
     * @param callable $callback
     *
     * @return null|mixed
     */
    public static function can($route, \Closure $callback = null, \Closure $failCallback = null, $attributes = [])
    {
        if (is_array($route)) {
            $route = (array) $route;
            $key = json_encode($route);
            $route = static::normalizeRoute($route[0]);
        } else {
            $key = $route;
        }

        if (isset($callback)) {
            $key .= spl_object_hash($callback);
        }

        if (isset($failCallback)) {
            $key .= spl_object_hash($failCallback);
        }

        $key .= json_encode($attributes);
        $key = md5($key);

        if (!isset(static::$_accessCache[$key])) {
            self::$_accessCache[$key] = false;
            $parts = \Yii::$app->createController($route);
            if ($parts) {
                /*
                 * @var Controller
                 */
                list($controller, $actionID) = $parts;
                if (!$actionID) {
                    $actionID = 'index';
                }

                $tmpLoginUrl = Yii::$app->user->loginUrl;
                Yii::$app->user->loginUrl = null;
                try {
                    $modules = $controller->getModules();
                    if ($modules && !$modules[count($modules) - 1]->beforeAction(new Action($actionID, $controller))) {
                        throw new \Exception('not valid');
                    }

                    if ($controller->beforeAction(new Action($actionID, $controller))) {
                        if (isset($callback)) {
                            self::$_accessCache[$key] = $callback();
                        } else {
                            self::$_accessCache[$key] = true;
                        }
                    }
                } catch (\Exception $e) {
                    if (isset($failCallback)) {
                        self::$_accessCache[$key] = $failCallback();
                    }
                }

                Yii::$app->user->loginUrl = $tmpLoginUrl;
            }
        }

        return self::$_accessCache[$key];
    }

    /**
     * @param $route
     *
     * @return string
     */
    protected static function normalizeRoute($route)
    {
        $route = Yii::getAlias((string) $route);
        if (strncmp($route, '/', 1) === 0) {
            // absolute route
            return ltrim($route, '/');
        }

        // relative route
        if (Yii::$app->controller === null) {
            throw new InvalidParamException("Unable to resolve the relative route: $route. No active controller is available.");
        }

        if (strpos($route, '/') === false) {
            // empty or an action ID
            return $route === '' ? Yii::$app->controller->getRoute() : Yii::$app->controller->getUniqueId().'/'.$route;
        } else {
            // relative to module
            return ltrim(Yii::$app->controller->module->getUniqueId().'/'.$route, '/');
        }
    }
}
