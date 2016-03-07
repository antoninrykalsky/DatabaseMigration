<?php

namespace SoftwareStudio\DatabaseMigration;


interface IDatabaseMigrator {

	const TYPE_SQL='sql';

	const TYPE_PHP='php';

	const COMMAND_CREATE = 'create';

	const COMMAND_MIGRATE = 'migrate';

	const COMMAND_CHECK = 'check';

	public function migrateProject();

	public function migrateModules();

	public function createMigration($type, $hint);
}