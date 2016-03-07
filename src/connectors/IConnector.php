<?php
/**
 * Created by IntelliJ IDEA.
 * User: Tonik
 * Date: 7. 3. 2016
 * Time: 22:20
 */

namespace SoftwareStudio\DatabaseMigration\connectors;


interface IConnector {

	/**
	 * Returns last installed version of main module.
	 *
	 * @return int
	 */
	public function getProjectModuleVersion();

	public function executeMigration(MigrationItem $item);
}