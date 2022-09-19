<?php

namespace ZbyRih\PSR4Helper;

use Nette\Utils\FileSystem;
use Symfony\Component\Console\Style\SymfonyStyle;

/**
 * @param array<string, array<int, string>> $multiClassFiles
 */
function createDirectories(
	SymfonyStyle $style,
	Configuration $config,
	array $multiClassFiles
): void {
	$classesToCreate = exctractClassToCreate($multiClassFiles);

	foreach ($classesToCreate as $class) {
		$path = explode('\\', $class);
		$class = array_pop($path);
		$whole = '';
		foreach ($path as $part) {
			$whole .= $part . '/';
			$dir = $config->cwd . '/' . $whole;
			if (!file_exists($dir)) {
				echo $dir . PHP_EOL;
				FileSystem::createDir($dir);
			}
		}
	}

	if (count($classesToCreate) === 0) {
		$style->info('No classes to create directories');
	}
}
