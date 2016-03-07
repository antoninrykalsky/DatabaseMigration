<?php

namespace SoftwareStudio\DatabaseMigration;

use SoftwareStudio\DatabaseMigration\utils\SXtringUXtils as StringUtils;
use SoftwareStudio\DatabaseMigration\builder\MigrationBuilder;

class DatabaseMigrationFacade implements IDatabaseMigrator {

	const SETTINGS_FILE='.settings.json';

	/** @var  String */
	private $mainFolder;

	/** @var  String */
	private $migrationFolder;

	/**
	 * DatabaseMigrationFacade constructor.
	 * @param String $migrationFolder
	 */
	public function __construct($mainFolder, $migrationFolder ) {
		$this->mainFolder = $mainFolder;
		$this->migrationFolder = $mainFolder.$migrationFolder;
	}

	public static function hello() {

		echo "hello world";
	}

	public function migrateProject() {

		$folder = $this->getMigrationFolder();

		$settings = $this->getSettings();

		// TODO more DI here
		$service = new MigrationService( $settings );
		$service->migrate( $folder );
	}

	public function migrateModules() {

		// TODO: Implement migrateProject() method.
	}

	public function createMigration($type, $hint) {

		$folder = $this->getMigrationFolder();

		$migrationBuilder =  new MigrationBuilder();
		$migrationBuilder->create($folder, $hint, $type);
	}

	public static function log($a) {
		echo $a."\n";
	}

	public function run($args) {

		if( empty($args[1])) {
			throw new MigrationException("Missing command.");
		}

		$action = $args[1];
		switch($action) {

			case IDatabaseMigrator::COMMAND_CREATE:
				$this->createMigration( $args[2], $args[3] );
				break;

			case IDatabaseMigrator::COMMAND_MIGRATE:
				$this->migrateProject( $args[2], $args[3] );
				break;

			case IDatabaseMigrator::COMMAND_CHECK:
				$this->checkSQLs();
				break;

			default:
				throw new MigrationException(StringUtils::message("Unknown command [{}]", $action));
		}
	}


	public function checkSQLs() {

		$migrationFolder = $this->getMigrationFolder();

		$settings = $this->getSettings();

		// TODO more DI here
		$service = new MigrationService( $settings );
		$service->checkSQLs( $migrationFolder );

	}

	public function getMigrationFolder() {

		$folder = new \SplFileInfo($this->migrationFolder);

		if (!$folder->isDir()) {
			throw new MigrationException(StringUtils::message("Passed directory [{}] does not exists.", $this->migrationFolder ));
		}
		return $folder;
	}

	public function getMainFolder() {

		$folder = new \SplFileInfo($this->mainFolder);

		if (!$folder->isDir()) {
			throw new MigrationException(StringUtils::message("Passed directory [{}] does not exists.", $this->mainFolder ));
		}
		return $folder;
	}


	private function getSettings() {

		$folder=$this->getMigrationFolder();
		$settingsPath=$folder->getPathname().'/'.self::SETTINGS_FILE;

		$settings=[];
		if( file_exists($settingsPath)) {

			$settings = json_decode(file_get_contents($settingsPath));

			if( json_last_error() > 0) {
				throw new MigrationException(StringUtils::message("Where was an error in [{}]: [{}]", self::SETTINGS_FILE, json_last_error_msg()));
			}
		}

		if( !empty( $settings->load )) {
			$settings->load = $this->getMainFolder()->getPathname() . '/' . $settings->load;
			@$settings->migrationFolder = $this->getMigrationFolder();
		}

		return $settings;
	}
}