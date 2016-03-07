<?php

/**
 * Database migrator.
 *
 * Copyright (c) 2016 Antonín Rykalský
 */

require __DIR__ . '/src/IDatabaseMigrator.php';
require __DIR__ . '/src/MigrationException.php';
require __DIR__ . '/src/MigrationService.php';
require __DIR__ . '/src/builder/MigrationBuilder.php';
require __DIR__ . '/src/entity/MigrationItem.php';
require __DIR__ . '/src/utils/MigrationSqlUtils.php';
require __DIR__ . '/src/utils/MigrationUtils.php';
require __DIR__ . '/src/DatabaseMigrationFacade.php';

// It is made to execture migration from the root of web application.
// TODO - allow to parse file with overide this settings
$m = new SoftwareStudio\DatabaseMigration\DatabaseMigrationFacade(getcwd(), '/_migrate');

try {
	$m->run($argv);
} catch( SoftwareStudio\DatabaseMigration\MigrationException $e ) {
	echo $e->getMessage(); exit;
}
