<?php
/**
 * Created by IntelliJ IDEA.
 * User: Tonik
 * Date: 7. 3. 2016
 * Time: 17:38
 */

namespace SoftwareStudio\DatabaseMigration\utils;


use SoftwareStudio\DatabaseMigration\utils\SXtringUXtils as StringUtils;
use SoftwareStudio\DatabaseMigration\DatabaseMigrationFacade;
use SoftwareStudio\DatabaseMigration\entity\MigrationItem;

class MigrationUtils {

	/**
	 * Returns files[] that should be migrated.
	 *
	 * @param \SplFileInfo $migrationFolder
	 * @param int $lastMigration
	 */
	public static function getFilesToMigrate(\SplFileInfo $migrationFolder, $lastMigration=0 ) {

		$filesToMigrate=[];

		$files = scandir($migrationFolder->getPathname().'\\');
		foreach( $files as $file ) {

			if (in_array($file, ['.', '..'])) {
				continue;
			}

			DatabaseMigrationFacade::log(StringUtils::message("Found file [{}]", $file ));

			$matches=[];
			if( preg_match('#^(\d+)_#', $file, $matches)) {
				if( $matches[1] > $lastMigration ) {
					$filesToMigrate[$matches[1]]= new MigrationItem($migrationFolder, $file, $matches[1]);
				}
			}
		}

		$lastMigration++;

		DatabaseMigrationFacade::log(StringUtils::message("Next file ID is [{}]", $lastMigration ));

		ksort($filesToMigrate);

		return $filesToMigrate;
	}

	/**
	 * Returns next id in folder series.
	 *
	 * @param \SplFileInfo $folder
	 */
	public static function getNextFileNumber(\SplFileInfo $folder ) {

		$maxFileId=0;
		$files = scandir($folder->getPathname().'\\');
		foreach( $files as $file ) {

			if (in_array($file, ['.', '..'])) {
				continue;
			}

			DatabaseMigrationFacade::log(StringUtils::message("Found file [{}]", $file ));

			$matches=[];
			if( preg_match('#^(\d+)_#', $file, $matches)) {
				if( $matches[1] > $maxFileId ) {
					$maxFileId=$matches[1];
				}
			}
		}

		$maxFileId++;

		DatabaseMigrationFacade::log(StringUtils::message("Next file ID is [{}]", $maxFileId ));

		return $maxFileId;
	}

	/**
	 * Returns hint to able to store as a part of filename.
	 *
	 * @param $hint
	 */
	public static function convertHint( $hint ) {
		$hint=str_replace(' ', '-', $hint);
		do {
			$hint=preg_replace('#--#', '-', $hint);
		} while (preg_match('#--#', $hint));

		return $hint;
	}

}