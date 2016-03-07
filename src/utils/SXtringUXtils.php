<?php
/**
 * Created by IntelliJ IDEA.
 * User: Tonik
 * Date: 7. 3. 2016
 * Time: 21:32
 */

namespace SoftwareStudio\DatabaseMigration\utils;

/**
 * Clone of basic functionality of {@link SoftwareStudio\Common\StringUtils}.
 * Duplicated so DatabaseMigration do not have other dependencies.
 *
 * @package SoftwareStudio\DatabaseMigration\utils
 */
class SXtringUXtils {

	/**
	 * Replace {} with arguments
	 * @param $message
	 * @param $args
	 */
	public static function message($message) {

		$args = func_get_args();

		$matches=[];
		preg_match_all("#\{\}#", $message, $matches);
		if( count($matches[0]) != count($args)-1) {
			throw new \Exception("Wrong argument count");
		}

		for($i=1; $i<count($args); $i++ ) { // ignore first intended
			$message = preg_replace("#\{\}#", $args[$i], $message, 1);
		}

		return $message;
	}
}