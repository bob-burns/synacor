<?php

/**
 * Class for dealing with sets of data
 */
class Set {

	/**
	 * @static
	 * @param   array     $data
	 * @param   string    $separator
	 * @return  array
	 */
	public static function expand(array $data, $separator = '.') {

		$result = array();

		foreach ($data as $flat => $value) {

			$keys   = explode($separator, $flat);
			$keys   = array_reverse($keys);
			$child  = array(
				$keys[0] => $value
			);

			array_shift($keys);

			foreach ($keys as $k) {

				$child = array(
					$k => $child
				);

			}

			$result = self::merge($result, $child);

		}

		return $result;

	}

	/**
	 * @static
	 * @param   array   $data
	 * @param   string  $separator
	 * @return  array
	 */
	public static function flatten(array $data, $separator = '.') {

		$result = array();
		$stack  = array();
		$path   = null;

		reset($data);

		while (!empty($data)) {

			$key        = key($data);
			$element    = $data[$key];

			unset($data[$key]);

			if (is_array($element)) {

				if (!empty($data)) {

					$stack[] = array($data, $path);

				}

				$data   = $element;
				$path  .= $key . $separator;

			} else {

				$result[$path . $key] = $element;

			}

			if (empty($data) && !empty($stack)) {

				list($data, $path) = array_pop($stack);

			}

		}

		return $result;

	}

	/**
	 * @static
	 * @return array
	 */
	public static function merge() {

		$args   = func_get_args();
		$return = current($args);

		while (($arg = next($args)) !== false) {

			foreach ((array)$arg as $key => $val) {

				if (!empty($return[$key]) && is_array($return[$key]) && is_array($val)) {

					$return[$key] = self::merge($return[$key], $val);

				} elseif (is_int($key)) {

					$return[] = $val;

				} else {

					$return[$key] = $val;

				}

			}

		}

		return $return;

	}

}