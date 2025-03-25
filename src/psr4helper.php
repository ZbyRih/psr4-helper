<?php

use ZbyRih\PSR4Helper\Commands as C;

include $_composer_autoload_path ?? __DIR__ . '/../vendor/autoload.php';

use Symfony\Component\Console\Application;

$application = new Application();

$application->addCommands([
	new C\CreateDefaultConfigurationCommand(),
	new C\CheckPsr4ComplianceCommand(),
	new C\SearchClassNameByPrefixCommand(),
	new C\ReportMultipleClassFilesCommand(),
	new C\CreateFilesCommand(),
	new C\CreateDirectoriesCommand(),
	new C\CorrectDirectoryCaseCommand(),
	new C\CleanGitIndexCommand(),
]);

$application->setDefaultCommand('help');

exit($application->run());
