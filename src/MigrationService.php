<?php
/**
 * Created by IntelliJ IDEA.
 * User: Tonik
 * Date: 7. 3. 2016
 * Time: 17:20
 */

namespace SoftwareStudio\DatabaseMigration;

use SoftwareStudio\DatabaseMigration\utils\SXtringUXtils as StringUtils;
use SoftwareStudio\DatabaseMigration\entity\MigrationItem;
use SoftwareStudio\DatabaseMigration\utils\MigrationSqlUtils;
use SoftwareStudio\DatabaseMigration\utils\MigrationUtils;
use dibi;

class MigrationService {

	private $settings;

	/**
	 * MigrationService constructor.
	 */
	public function __construct( $settings ) {
		$this->settings = $settings;
	}



	public function migrate(\SplFileInfo $migrationFolder ) {

		$lastMigration=0;

		if( !empty($this->settings->load) && file_exists(($this->settings->load))) {

			// we are functional app wired
			require_once $this->settings->load;

			$rs = dibi::query("select version from mlmsoft_modules where id=-1");

			$lastMigration = $rs->fetchSingle();
			$lastMigration = (!is_numeric($lastMigration) ? 0 : $lastMigration);
		}

		$filesToMigrate = MigrationUtils::getFilesToMigrate( $migrationFolder, $lastMigration );

		/* @var MigrationItem $file */
		foreach( $filesToMigrate as $migrationItem ) {

			$this->doMigration($migrationItem);
		}
	}

	private function doMigration( MigrationItem $item ) {



		DatabaseMigrationFacade::log(StringUtils::message("Doing migration for [{}] / [{}]", $item->getOrderNumber(), $item->getFile()->getFilename() ));

		$content = file_get_contents($item->getFile()->getRealPath());

		if( empty($content)) {
			throw new MigrationException("Content is empty.");
		}

		$bom_debug=false;
		if( $bom_debug ) {
			echo $content;exit;
		}

		dibi::begin();

		try {

			dibi::nativeQuery($content);
//			dibi::loadFile($item->getFile()->getRealPath());

			DatabaseMigrationFacade::log(StringUtils::message("- migration ok" ));

			// update last exec number
			if( $item->getOrderNumber() == 1 ) {
				dibi::query("insert into mlmsoft_modules values(-1, %i, 1)", $item->getOrderNumber());
			} else {
				dibi::query("update mlmsoft_modules set version=%i where id=-1", $item->getOrderNumber());
			}

		} catch( \DibiException $e ) {

			$verbose = false;

			if( $verbose) {
				throw new MigrationException(StringUtils::message("- migration of [{}] failed \n\n {}", $item->getFile()->getFilename(), $e->getTraceAsString() ));
			} else {
				throw new MigrationException(StringUtils::message("- migration of [{}] failed \n\n {}", $item->getFile()->getFilename(), $e->getMessage() ));
			}
		}

		dibi::commit();

	}

	public function checkSQLs(\SplFileInfo $migrationFolder, $wrongs ) {

		$filesToMigrate = MigrationUtils::getFilesToMigrate( $migrationFolder, 0 );

		$wrongs = [];

		/* @var MigrationItem $migrationItem */
		foreach( $filesToMigrate as $migrationItem ) {

			$content = file_get_contents($migrationItem->getFile()->getRealPath());

			if (!MigrationSqlUtils::isUTF8($content)) {
				$wrongs[]=StringUtils::message("File [{}] is not valit UTF-8 file", $migrationItem->getFile()->getFilename() );
				continue;
			}

			if (MigrationSqlUtils::checkContainBOM($content)) {
				file_put_contents($migrationItem->getFile()->getRealPath(), MigrationSqlUtils::removeBOM($content));
				$wrongs[]=StringUtils::message(" - file [{}] used to contained BOM, fixed", $migrationItem->getFile()->getFilename() );
			}
		}

		throw new MigrationException( implode('\n', $wrongs ));
	}


}