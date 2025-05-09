<?php

namespace ZbyRih\PSR4Helper\Commands;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use ZbyRih\PSR4Helper\Helpers\ConfigurationLoader;
use ZbyRih\PSR4Helper\Helpers\FolderHelper;
use ZbyRih\PSR4Helper\Helpers\OutputFacade;

final class CleanGitIndexCommand extends Command
{
	protected function configure(): void
	{
		$this->setName('clear-git');
		$this->setDescription('With `clear` value will remove all cached duplicate folders with mismatch case from index');
		$this->addArgument('clear', InputOption::VALUE_OPTIONAL);
		$this->addOption('file', 'f', InputOption::VALUE_OPTIONAL, 'Configuration file name', 'psr4helper.neon');
	}

	public function execute(InputInterface $input, OutputInterface $output): int
	{
		OutputFacade::init($input, $output);

		$file = $input->getOption('file');
		$clear = $input->getArgument('clear');

		$config = (new ConfigurationLoader())(FolderHelper::getCwd(), $file);

		OutputFacade::info('Clearing git index of mismatch case duplicates');

		if ($clear != 'clear') {
			OutputFacade::info('Only differencies will be shown');	
		}

		try {
			$output = self::gitExec('ls-files | xargs -n 1 dirname | uniq');
		} catch (\Exception $e) {
			OutputFacade::warning($e->getMessage());
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
			fn($subs) => array_unique($subs),
			$dirsDuplicates
		);

		$dirsDuplicates = array_filter(
			$dirsDuplicates,
			fn($subDirs) => count($subDirs) > 1
		);

		foreach ($dirsDuplicates as $dir => $subs) {
			echo '    ' . $dir . PHP_EOL;
			foreach ($subs as $sub) {
				echo '    => ' . $sub . PHP_EOL;
			}
		}

		if ($clear != 'clear') {
			return Command::SUCCESS;
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
				self::gitExec('rm --cached -rf ' . $sub);
			}
		}

		OutputFacade::definitionList(
			['git cached removed folders' => (string) $count],
		);

		return Command::SUCCESS;
	}

	/**
	 * @return array<int, string>
	 */
	private static function gitExec(string $command): array
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
}
