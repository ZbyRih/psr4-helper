<?php

namespace ZbyRih\PSR4Helper;

use Symfony\Component\Console\Style\SymfonyStyle;

/**
 * @param array<string, string> $classes
 * @param string $startWith
 */
function findClassesStartedWith(
	SymfonyStyle $style,
	array $classes,
	string $startWith
): void {
	$count = 0;
	foreach ($classes as $class => $_file) {
		if (str_starts_with($class, $startWith)) {
			echo $class . PHP_EOL;
			$count++;
		}
	}

	$style->definitionList(
		[sprintf('Nalezených tříd začínajícíh `%s`', $startWith) => $count],
	);
}
