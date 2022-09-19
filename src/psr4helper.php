<?php

use function ZbyRih\PSR4Helper\checkingPSR4;
use function ZbyRih\PSR4Helper\clearingGitIndex;
use function ZbyRih\PSR4Helper\createDirectories;
use function ZbyRih\PSR4Helper\createFiles;
use function ZbyRih\PSR4Helper\directoriesCaseUpdate;
use function ZbyRih\PSR4Helper\findClassesStartedWith;
use function ZbyRih\PSR4Helper\loadConfiguration;
use function ZbyRih\PSR4Helper\loadInfoAboutFiles;
use function ZbyRih\PSR4Helper\multiClassFiles;
use Symfony\Component\Console\Input\ArgvInput;
use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\Console\Style\SymfonyStyle;

include $_composer_autoload_path ?? __DIR__ . '/../vendor/autoload.php';

$definition = new InputDefinition();
$definition->addOption(new InputOption('help', null, InputOption::VALUE_NONE, 'Display this help message'));
$definition->addOption(new InputOption('list', null, InputOption::VALUE_NONE, 'List multiple classes in one file'));
$definition->addOption(new InputOption('psr4', null, InputOption::VALUE_OPTIONAL, 'List of classes with wrong folders by PSR-4 `info|case|missing`'));
$definition->addOption(new InputOption('find', null, InputOption::VALUE_OPTIONAL, 'List of classes with a fully quantified name starting with a given value'));
$definition->addOption(new InputOption('case-update', null, InputOption::VALUE_OPTIONAL, 'Rename folders with wrong case `info|rename`'));
$definition->addOption(new InputOption('create-dirs', null, InputOption::VALUE_NONE, 'Create folders by classes namspaces'));
$definition->addOption(new InputOption('create-files', null, InputOption::VALUE_NONE, 'Create files by multiple classes'));
$definition->addOption(new InputOption('git-clear', null, InputOption::VALUE_OPTIONAL, 'With `clear` value will remove all cached duplicate folders with mismatch case from index'));

$output = new ConsoleOutput();

$consoleError = false;
try {
	$input = new ArgvInput(null, $definition);
} catch (\Throwable $e) {
	$consoleError = true;
	$input = new ArgvInput([__FILE__, '--help'], $definition);
}

$style = new SymfonyStyle($input, $output);

if ($consoleError) {
	$style->error('Console input error, printing help instead.');
}

if ($input->getOption('help')) {
	$list = [];
	foreach ($definition->getOptions() as $opt) {
		$list[] = ['--' . $opt->getName() => $opt->getDescription()];
	}
	$style->definitionList('Avaible options', ...$list);
	echo "\t" . $definition->getSynopsis(false);
	exit;
}

if (!$cwd = getcwd()) {
	$style->error('Cannot determine current work dir.');
	exit;
}

try {
	$config = loadConfiguration($cwd);
} catch (\Exception $e) {
	$style->error($e->getMessage());
	exit;
}

[
	$classes,
	$reverse,
	$nonAppClasses,
	$nonAppCLassesCount,
	$multiClassesFiles,
	$mutliCLassesCount,
] = loadInfoAboutFiles($style, $config);

// -------------------------------
// výpis souborů s více než jednou třídou
// -------------------------------
if ($input->getOption('list')) {
	$style->info('List of muli-class files');

	multiClassFiles(
		$style,
		$config,
		$multiClassesFiles,
		$nonAppClasses,
		$nonAppCLassesCount
	);

	$style->success('Done');
	exit;
}

// -------------------------------
// založení adresářů které chybí podle namespaců
// -------------------------------
if ($input->getOption('create-dirs')) {
	$style->info('Creating directories');

	createDirectories(
		$style,
		$config,
		$multiClassesFiles,
	);

	$style->success('Done');
	exit;
}

// -------------------------------
// vytvožení souborů pro třídy
// -------------------------------
if ($input->getOption('create-files')) {
	$style->info('Creating files');

	createFiles(
		$style,
		$config,
		$multiClassesFiles,
	);

	$style->success('Done');
	exit;
}

// -------------------------------
// kontrola psr4
// -------------------------------
if ($mode = $input->getOption('psr4')) {
	$style->info('PSR4 checking');

	checkingPSR4(
		$style,
		$config,
		$classes,
		$mode
	);

	$style->success('Done');
	exit;
}

// -------------------------------
// nalezení occurance ve full qualify nazvu tříd
// -------------------------------

if ($startWith = $input->getOption('find')) {
	$style->info('Print classes started with ...');

	findClassesStartedWith($style, $classes, $startWith);

	$style->success('Done');
	exit;
}

// -------------------------------
// oprava velikosti pismen adresářů
// -------------------------------
if ($mode = $input->getOption('case-update')) {
	$style->info('Repair case mismatch in directories');

	directoriesCaseUpdate($style, $config, $mode);

	$style->success('Done');
	exit;
}

// -------------------------------
// git clear of directory case mismatch duplicates
// -------------------------------
if ($clear = $input->getOption('git-clear')) {
	$style->info('Clearing git index of mismatch case duplicates');

	clearingGitIndex($style, $config, $clear);

	$style->success('Done');
	exit;
}
