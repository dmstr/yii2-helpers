<?php
namespace dmstr\helpers;

/**
 * @link http://www.diemeisterei.de/
 * @copyright Copyright (c) 2015 diemeisterei GmbH, Stuttgart
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
use Yii;
use yii\base\InvalidParamException;
use yii\web\Controller;
use yii\base\Action;

/**
 * Class Html
 * @author Sergej Kunz <s.kunz@herzogkommunikation.de>
 */
class Html extends \yii\helpers\Html
{
	protected static $_accessCache = [];

	/**
	 * Use case:
	 * \dmstr\helpers\Html::a("test link", ['create'])
	 *
	 * @param string $text
	 * @param null $url
	 * @param array $options
	 * @return string|null
	 */
	public static function a($text, $url = null, $options = [])
	{
		return static::access($url, function() use ($text, $url, $options) {
			return parent::a($text, $url, $options);
		});
	}

	/**
	 * @param $route
	 * @param callable $callback
	 * @return null|mixed
	 */
	public static function access($route, \Closure $callback, \Closure $failCallback = null)
	{
		if (is_array($route)) {
			$route = (array) $route;
			$route = static::normalizeRoute($route[0]);
		}

		if (!isset(static::$_accessCache[$route])) {
			self::$_accessCache[$route] = null;
			$parts = \Yii::$app->createController($route);
			if ($parts) {
				/**
				 * @var $controller Controller
				 */
				list($controller, $actionID) = $parts;

				try {
					if ($controller->beforeAction(new Action($actionID, $controller))) {
						self::$_accessCache[$route] = $callback();
					}
				} catch (\Exception $e) {
					self::$_accessCache[$route] = $failCallback();
				}
			}
		}

		return self::$_accessCache[$route];
	}

	/**
	 * @param $route
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
			return $route === '' ? Yii::$app->controller->getRoute() : Yii::$app->controller->getUniqueId() . '/' . $route;
		} else {
			// relative to module
			return ltrim(Yii::$app->controller->module->getUniqueId() . '/' . $route, '/');
		}
	}
}