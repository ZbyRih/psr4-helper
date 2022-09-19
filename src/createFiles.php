<?php

namespace ZbyRih\PSR4Helper;

use Nette\Utils\FileSystem;
use Symfony\Component\Console\Style\SymfonyStyle;

/**
 * @param array<string, array<int, string>> $multiClassFiles
 */
function createFiles(
	SymfonyStyle $style,
	Configuration $config,
	array $multiClassFiles
): void {
	$classesToCreate = exctractClassToCreate($multiClassFiles);

	foreach ($classesToCreate as $class) {
		$path = explode('\\', $class);
		$class = array_pop($path);
		$file = $config->cwd . '/' . implode('\\', $path) . '/' . $class . '.php';

		if (!file_exists($file)) {
			$_content = str_replace('{namespace}', implode('\\', $path), $config->newFilecontent);
			FileSystem::write($file, $_content);
		}
	}

	if (count($classesToCreate) === 0) {
		$style->info('No classes to create files');
	}
}
