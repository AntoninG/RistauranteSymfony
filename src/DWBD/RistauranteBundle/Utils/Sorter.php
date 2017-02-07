<?php

namespace DWBD\RistauranteBundle\Utils;


class Sorter
{
	public function sortObjects(array &$objects = array(), $method = "getName", $type = "ASC")
	{
		if (empty($objects)) {
			return false;
		}

		$stringUtils = new StringUtils();
		uasort($objects, function($a, $b) use ($method, $type, $stringUtils) {
			$aa = empty($key) ? $a : ( empty($a[$key])?'':$a[$key] );
			$bb = empty($key) ? $b : ( empty($b[$key])?'':$b[$key] );
		});
	}

	public function sortArray(array &$array = array(), $key = "", $type = "ASC")
	{
		if (empty($array)) {
			return false;
		}

		$stringUtils = new StringUtils();
		uasort($array, function($a, $b) use ($key, $type, $stringUtils) {
			$aa = empty($key) ? $a : ( empty($a[$key])?'':$a[$key] );
			$bb = empty($key) ? $b : ( empty($b[$key])?'':$b[$key] );

			$aa = strtolower($stringUtils->stripAccents($aa));
			$bb = strtolower($stringUtils->stripAccents($bb));

			if($aa == $bb) {
				return 0;
			}
			switch ($type) {
				case 'ASC':
				default:
					return ($aa < $bb) ? -1 : 1;
					break;
				case 'DESC':
					return ($aa > $bb) ? -1 : 1;
					break;
			}
		});
	}
}