<?php
/**
 * Created by IntelliJ IDEA.
 * User: Tonik
 * Date: 7. 3. 2016
 * Time: 17:20
 */

namespace SoftwareStudio\DatabaseMigration;

use SoftwareStudio\DatabaseMigration\connectors\DibiConnector;
use SoftwareStudio\DatabaseMigration\connectors\IConnector;
use SoftwareStudio\DatabaseMigration\utils\SXtringUXtils as StringUtils;
use SoftwareStudio\DatabaseMigration\entity\MigrationItem;
use SoftwareStudio\DatabaseMigration\utils\MigrationSqlUtils;
use SoftwareStudio\DatabaseMigration\utils\MigrationUtils;
use dibi;

class MigrationService {

	private $settings;

	/** @var IConnector */
	private $dbConnector;

	/**
	 * MigrationService constructor.
	 */
	public function __construct( $settings ) {
		$this->settings = $settings;
	}

	/**
	 * Execute migration for given folder.
	 *
	 * @param \SplFileInfo $migrationFolder
	 */
	public function migrate(\SplFileInfo $migrationFolder ) {

		// TODO better handling with setting .. you do not need to bring piano when you forgot a cigar on it.
		if( empty($this->settings->load) || file_exists(($this->settings->load))) {
			throw new MigrationException("Database migration expect connected dibi at first version");
		}

		// we are functional app wired
		require_once $this->settings->load;

		$this->dbConnector = new DibiConnector();

		$lastMigration = $this->dbConnector->getProjectModuleVersion();

		$migrationItems = MigrationUtils::getMigrationItems( $migrationFolder, $lastMigration );

		/* @var MigrationItem $item */
		foreach( $migrationItems as $item ) {

			DatabaseMigrationFacade::log(StringUtils::message("Doing migration for [{}] / [{}]", $item->getOrderNumber(), $item->getFile()->getFilename() ));

			$this->dbConnector->executeMigration( $item );
		}
	}

	/**
	 * Checks migration files of given folder.
	 *
	 * @param \SplFileInfo $migrationFolder
	 */
	public function checkSQLs(\SplFileInfo $migrationFolder ) {

		$filesToMigrate = MigrationUtils::getMigrationItems( $migrationFolder, 0 );

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

		// pass all errors at once
		throw new MigrationException(implode('\n', $wrongs));
	}
}