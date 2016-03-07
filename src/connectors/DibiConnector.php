<?php
/**
 * Created by IntelliJ IDEA.
 * User: Tonik
 * Date: 7. 3. 2016
 * Time: 22:19
 */

namespace SoftwareStudio\DatabaseMigration\connectors;


use SoftwareStudio\DatabaseMigration\entity\MigrationItem;
use SoftwareStudio\DatabaseMigration\utils\SXtringUXtils as StringUtils;

class DibiConnector implements IConnector {

	const MIGRATION_TABLE = 'mlmsoft_modules';

	public function getProjectModuleVersion() {

		$rs = dibi::query(StringUtils::message("select version from {} where id=-1", self::MIGRATION_TABLE ));

		$lastMigration = $rs->fetchSingle();

		return (!is_numeric($lastMigration) ? 0 : $lastMigration);
	}


	public function executeMigration(MigrationItem $item) {

		if( empty($content)) {
			throw new MigrationException(StringUtils::message("Content of [{}] is empty.", $item->getFile()->getFilename()));
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
}