<?php

namespace ZbyRih\PSR4Helper\Commands;

use Nette\Utils\FileSystem;
use Nette\Utils\Finder;
use Nette\Utils\Strings;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use ZbyRih\PSR4Helper\Helpers\ConfigurationLoader;
use ZbyRih\PSR4Helper\DTO\Configuration;
use ZbyRih\PSR4Helper\Helpers\FolderHelper;
use ZbyRih\PSR4Helper\Helpers\OutputFacade;

final class CorrectDirectoryCaseCommand extends Command
{
	protected function configure(): void
	{
		$this->setName('update-case');
		$this->setDescription('Rename folders with wrong case `info|rename`');
		$this->addArgument('mode', InputArgument::OPTIONAL, 'info|rename', 'info', ['info', 'rename']);
		$this->addOption('file', 'f', InputOption::VALUE_OPTIONAL, 'Configuration file name', 'psr4helper.neon');
	}

	public function execute(InputInterface $input, OutputInterface $output): int
	{
		OutputFacade::init($input, $output);

		$file = $input->getOption('file');
		$mode = $input->getArgument('mode');

		$config = (new ConfigurationLoader())(FolderHelper::getCwd(), $file);

		OutputFacade::info('Repair case mismatch in directories');

		$count = 0;

		$dirs = iterator_to_array(Finder::findDirectories('**')->from($config->basePath)->getIterator());

		$shouldOmit = function (Configuration $config, array $path): bool {
			foreach ($config->excludeCaseUpdates as $omit) {
				if (in_array($omit, $path)) {
					return true;
				}
			}
			return false;
		};

		foreach ($dirs as $dir) {
			$path = explode('\\', $dir);

			if ($shouldOmit($config, $path)) {
				continue;
			}

			$last = array_pop($path);
			if (strtoupper($last[0]) != $last[0]) {
				$count++;
				$collide = '';
				$_dir = $config->cwd . '\\' . implode('\\', $path);

				if (self::folderExists(Strings::firstUpper($last), $_dir)) {
					$collide = ' - collide';
				}

				if ($mode == 'rename' || $mode == 'show') {
					$from = $config->cwd . '\\' . implode('\\', array_map(fn($v) => Strings::firstUpper($v), $path));
					echo ' -> ' . $from . '\\' . $last . PHP_EOL;
					echo '    ' . $from . '\\' . Strings::firstUpper($last) . PHP_EOL;
					if ($mode == 'rename') {
						FileSystem::rename($from . '\\' . $last, $from . '\\' . Strings::firstUpper($last));
					}
				} else {
					echo $dir . $collide . PHP_EOL;
				}
			}
		}

		OutputFacade::definitionList(
			['Number of directories with incorrect case' => (string) $count],
		);

		return Command::SUCCESS;
	}

	private static function folderExists(string $folderName, string $dir): bool
	{
		$folders = iterator_to_array(Finder::findDirectories('*')->in($dir)->getIterator());
		// get all folders in provided dir
		$folders = array_filter($folders, 'is_dir');
		// now do a case sensitive comparison
		return (bool) preg_grep("/" . $folderName . "$/", $folders);
	}
}
