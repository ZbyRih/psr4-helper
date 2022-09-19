<?php

namespace ZbyRih\PSR4Helper;

use Symfony\Component\Console\Style\SymfonyStyle;

/**
 * @param array<string, string> $classes
 * @param string $mode
 */
function checkingPSR4(
	SymfonyStyle $style,
	Configuration $config,
	array $classes,
	string $mode
): void {
	$countCase = 0;
	$countMissing = 0;

	$stripBase = function (string $str, string $cwd) {
		return str_replace($cwd . '\\', '', $str);
	};

	$shouldOmmited = function (Configuration $config, string $className) {
		foreach ($config->excludePsr4CheckClassEndsWith as $ommit) {
			if (str_ends_with($className, $ommit)) {
				return true;
			}
		}
		return false;
	};

	foreach ($classes as $class => $_file) {
		if ($shouldOmmited($config, $class)) {
			continue;
		}

		$file = $config->cwd . '\\' . $class . '.php';
		$rFile = realpath($file);

		if (!$rFile) {
			$countMissing++;
			if ($mode != 'case') {
				echo ' >> ' . $stripBase($_file, $config->cwd) . PHP_EOL;
				echo '    ' . $stripBase($file, $config->cwd) . PHP_EOL;
			}
			continue;
		}

		if ($file !== $rFile) {
			$countCase++;
			if ($mode != 'missing') {
				echo ' ^^ ' . $stripBase($file, $config->cwd) . PHP_EOL;
				echo '    ' . $stripBase($rFile, $config->cwd) . PHP_EOL;
			}
			continue;
		}
	}

	$style->definitionList(
		['Missing' => $countMissing],
		['Case mismatch' => $countCase],
	);
}
