<?php

namespace ZbyRih\PSR4Helper;

use Symfony\Component\Console\Style\SymfonyStyle;

/**
 * @param array<string, array<int, string>> $multiClasses
 * @param array<string, array<int, string>> $nonAppClasses
 */
function multiClassFiles(
	SymfonyStyle $style,
	Configuration $config,
	array $multiClasses,
	array $nonAppClasses,
	int $nonAppCLassesCount
): void {
	$style->info('List of muli-class files');

	foreach ($multiClasses as $file => $classes) {
		echo '    ' . $file . PHP_EOL;
		foreach ($classes as $cl) {
			echo '    => ' . $cl . PHP_EOL;
		}
	}

	if (count($multiClasses) === 0) {
		$style->info('No files with multiple classes');
	}

	$style->info('List of files with classes out of App namespace');

	foreach ($nonAppClasses as $file => $classes) {
		echo '    ' . $file . PHP_EOL;
		foreach ($classes as $cl) {
			echo '    => ' . $cl . PHP_EOL;
		}
	}

	if (0 === $nonAppCLassesCount) {
		$style->info('No files with classes out of App namespace');
	}
}
