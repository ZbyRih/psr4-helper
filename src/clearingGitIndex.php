<?php

namespace ZbyRih\PSR4Helper;

use Symfony\Component\Console\Style\SymfonyStyle;

/**
 * Undocumented function
 *
 * @return array<int, string>
 */
function gitExec(string $command): array
{
	$repoPath = getcwd();
	$command = 'git -c core.quotepath=false --git-dir="' . $repoPath . '/.git" --work-tree="' . $repoPath . '" ' . $command;
	$lastLine = exec('(' . $command . ') 2>&1', $output, $return);

	if (0 !== $return) {
		throw new \Exception('nothing returned from git');
	}

	if (!$lastLine) {
		throw new \Exception('nothing returned from git');
	}

	return $output;
}

function clearingGitIndex(
	SymfonyStyle $style,
	Configuration $config,
	string $clear
): void {
	try {
		$output = gitExec('ls-files | xargs -n 1 dirname | uniq');
	} catch (\Exception $e) {
		$style->warning($e->getMessage());
		exit;
	}

	$dirs = array_filter(
		$output,
		function ($str) use ($config) {
			$lc = strtolower($str);
			return '.' !== $lc && str_starts_with($lc, strtolower($config->gitIndexCheckFolder));
		}
	);

	$dirsDuplicates = [];
	foreach ($dirs as $line) {
		$lc = strtolower($line);
		$dirsDuplicates[$lc][] = $line;
	}

	$dirsDuplicates = array_map(
		fn ($subs) => array_unique($subs),
		$dirsDuplicates
	);

	$dirsDuplicates = array_filter(
		$dirsDuplicates,
		fn ($subdirs) => count($subdirs) > 1
	);

	foreach ($dirsDuplicates as $dir => $subs) {
		echo '    ' . $dir . PHP_EOL;
		foreach ($subs as $sub) {
			echo '    => ' . $sub . PHP_EOL;
		}
	}

	if ($clear != 'clear') {
		return;
	}

	echo PHP_EOL;
	$count = 0;

	foreach ($dirsDuplicates as $subs) {
		sort($subs, SORT_FLAG_CASE);
		$first = array_pop($subs);

		echo '  let ' . $first . PHP_EOL;

		foreach ($subs as $sub) {
			$count++;
			echo '    rm --cached ' . $sub . PHP_EOL;
			gitExec('rm --cached -rf ' . $sub);
		}
	}

	$style->definitionList(
		['git cached removed folders' => $count],
	);
}
