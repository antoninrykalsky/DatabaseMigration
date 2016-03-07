<?php
/**
 * Created by IntelliJ IDEA.
 * User: Tonik
 * Date: 7. 3. 2016
 * Time: 16:25
 */

namespace SoftwareStudio\DatabaseMigration\builder;

use SoftwareStudio\DatabaseMigration\utils\SXtringUXtils as StringUtils;
use SoftwareStudio\DatabaseMigration\DatabaseMigrationFacade;
use SoftwareStudio\DatabaseMigration\IDatabaseMigrator;
use SoftwareStudio\DatabaseMigration\MigrationException;
use SoftwareStudio\DatabaseMigration\utils\MigrationUtils;

class MigrationBuilder {

	private static $TYPES = [IDatabaseMigrator::TYPE_SQL, IDatabaseMigrator::TYPE_PHP];

	const PROJECT_MIGRATION="{}_{}.{}";

	/**
	 * Create an file in given directory.
	 *
	 * @param \SplFileInfo $folder
	 * 		Folder to create in.
	 * @param string $type
	 * 		sql | php
	 * @param $hint
	 * 		hint in filename
	 */
	public function create(\SplFileInfo $folder, $type = IDatabaseMigrator::TYPE_SQL, $hint ) {

		if (empty($type)) {
			$type = IDatabaseMigrator::TYPE_SQL;
		}

		if (!in_array($type, self::$TYPES)) {
			throw new MigrationException(StringUtils::message("Unsupported type [{}]", $type));
		}

		$maxFile=MigrationUtils::getNextFileNumber($folder);
		$name = StringUtils::message(self::PROJECT_MIGRATION, $maxFile, MigrationUtils::convertHint($hint), $type);
		$content = "";

		$newFile = new \SplFileInfo($folder->getPathname() . '/' . $name);

		if ($newFile->isFile()) {
			throw new MigrationException("Generated file [{}] is already exists.");
		}

		DatabaseMigrationFacade::log(StringUtils::message("Creating file [{}]", $newFile->getPathname() ));
		file_put_contents($newFile->getPathname(), $content);
	}



}