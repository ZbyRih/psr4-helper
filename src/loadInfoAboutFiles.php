<?php

namespace ZbyRih\PSR4Helper;

use Nette\Loaders\RobotLoader;
use Symfony\Component\Console\Style\SymfonyStyle;

/** @return array<int, mixed>  */
function loadInfoAboutFiles(
	SymfonyStyle $style,
	Configuration $config,
): array {

	$robo = new RobotLoader();
	$robo->addDirectory($config->baseFolder);
	$robo->rebuild();

	// vrací pole dvojic třída => název souboru
	$classes = $robo->getIndexedClasses();

	$reverse = [];

	foreach ($classes as $class => $file) {
		$reverse[$file][] = $class;
	}

	$mutliCLassCount = 0;
	$multiClassFiles = [];

	foreach ($reverse as $file => $_classes) {
		if (count($_classes) > 1) {
			$multiClassFiles[$file] = $_classes;
			$mutliCLassCount += count($_classes);
		}
	}

	$nonAppClasses = [];
	$nonAppCLassesCount = 0;
	foreach ($classes as $class => $file) {
		if (!str_starts_with($class, $config->baseNamespace)) {
			$nonAppClasses[$file][] = $class;
			$nonAppCLassesCount++;
		}
	}

	$style->definitionList(
		['Count of clasess' => count($classes)],
		['Count of multi-class files' => count($multiClassFiles)],
		['Count of cLasses in files with multi-classes' => $mutliCLassCount],
		[sprintf('Count of classes out of `%s` namespace', $config->baseNamespace) => $nonAppCLassesCount],
	);

	return [
		$classes,
		$reverse,
		$nonAppClasses,
		$nonAppCLassesCount,
		$multiClassFiles,
		$mutliCLassCount,
	];
}
