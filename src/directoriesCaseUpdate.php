<?php

namespace ZbyRih\PSR4Helper;

use Nette\Utils\FileSystem;
use Nette\Utils\Finder;
use Nette\Utils\Strings;
use Symfony\Component\Console\Style\SymfonyStyle;

function folderExxists(string $folderName, string $dir): bool
{
	$folders = iterator_to_array(Finder::findDirectories('*')->in($dir)->getIterator());
	// get all folders in provided dir
	$folders = array_filter($folders, 'is_dir');
	//now do a case sensitive comparison
	return (bool) preg_grep("/" . $folderName . "$/", $folders);
}

function directoriesCaseUpdate(
	SymfonyStyle $style,
	Configuration $config,
	string $mode
): void {
	$count = 0;

	$dirs = iterator_to_array(Finder::findDirectories('**')->from($config->baseFolder)->getIterator());

	$shouldOmmit = function (Configuration $config, array $path): bool {
		foreach ($config->expludeCaseUpdates as $ommit) {
			if (in_array($ommit, $path)) {
				return true;
			}
		}
		return false;
	};

	foreach ($dirs as $dir) {
		$path = explode('\\', $dir);

		if ($shouldOmmit($config, $path)) {
			continue;
		}

		$last = array_pop($path);
		if (strtoupper($last[0]) != $last[0]) {
			$count++;
			$colide = '';
			$_dir = $config->cwd . '\\' . implode('\\', $path);

			if (folderExxists(Strings::firstUpper($last), $_dir)) {
				$colide = ' - colide';
			}

			if ($mode == 'rename' || $mode == 'show') {
				$from = $config->cwd . '\\' . implode('\\', array_map(fn ($v) => Strings::firstUpper($v), $path));
				echo ' -> ' . $from . '\\' . $last . PHP_EOL;
				echo '    ' . $from . '\\' . Strings::firstUpper($last) . PHP_EOL;
				if ($mode == 'rename') {
					FileSystem::rename($from . '\\' . $last, $from . '\\' . Strings::firstUpper($last));
				}
			} else {
				echo $dir . $colide . PHP_EOL;
			}
		}
	}

	$style->definitionList(
		['Počet adresářů se špatnou velikostí' => $count],
	);
}
