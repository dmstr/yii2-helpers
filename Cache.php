<?php
/**
 * @link http://www.diemeisterei.de/
 * @copyright Copyright (c) 2015 diemeisterei GmbH, Stuttgart
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
class Cache
{
	protected static $_instanceCache = [];

	/**
	 * @param callable $closure
	 * @param array $options
	 * @return mixed
	 */
	public static function exec(\Closure $closure, array $options = [], $duration = 0, $dependency = null)
	{
		$key = json_encode($options);
		$key .= spl_object_hash($closure);
		$key = md5($key);

		if (!isset(static::$_instanceCache[$key])) {
			static::$_instanceCache[$key] = \Yii::$app->cache->get($key);

			if (!isset(static::$_instanceCache[$key])) {
				static::$_instanceCache[$key] = $closure();
				\Yii::$app->cache->set($key, static::$_instanceCache[$key], $duration, $dependency);
			}
		}

		return static::$_instanceCache[$key];
	}
}