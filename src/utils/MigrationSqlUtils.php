<?php
/**
 * Created by IntelliJ IDEA.
 * User: Tonik
 * Date: 7. 3. 2016
 * Time: 19:13
 */

namespace SoftwareStudio\DatabaseMigration\utils;


class MigrationSqlUtils {

	public static function checkContainBOM( $s ) {

		if (substr($s, 0, 3) === "\xEF\xBB\xBF") {
			true;
		}
	}

	public static function removeBOM( $s ) {

		if (substr($s, 0, 3) === "\xEF\xBB\xBF") {
			return substr($s, 3);
		}
	}

	public static function isUTF8( $s, $encoding = 'UTF-8') {

		return $s === self::fixEncoding($s, $encoding);

	}

	/**
	 * @param $s
	 * @param string $encoding
	 * @author Nette! thank you
	 * @return string
	 */
	public static function fixEncoding($s, $encoding = 'UTF-8')
	{
		// removes xD800-xDFFF, xFEFF, x110000 and higher
		if (strcasecmp($encoding, 'UTF-8') === 0) {
			$s = str_replace("\xEF\xBB\xBF", '', $s); // remove UTF-8 BOM
		}
		if (PHP_VERSION_ID >= 50400) {
			ini_set('mbstring.substitute_character', 'none');
			return mb_convert_encoding($s, $encoding, $encoding);
		}
		return @iconv('UTF-16', $encoding . '//IGNORE', iconv($encoding, 'UTF-16//IGNORE', $s)); // intentionally @
	}

}