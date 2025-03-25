<?php

namespace ZbyRih\PSR4Helper\Commands;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use ZbyRih\PSR4Helper\Helpers\ConfigurationLoader;
use ZbyRih\PSR4Helper\Helpers\ClassMap;
use ZbyRih\PSR4Helper\Helpers\FolderHelper;
use ZbyRih\PSR4Helper\Helpers\OutputFacade;

final class ReportMultipleClassFilesCommand extends Command
{
	protected function configure(): void
	{
		$this->setName('multi');
		$this->setDescription('List of files with multiple classes and classes out of base namespace');
		$this->addOption('file', 'f', InputOption::VALUE_OPTIONAL, 'Configuration file name', 'psr4helper.neon');
	}

	public function execute(InputInterface $input, OutputInterface $output): int
	{
		OutputFacade::init($input, $output);

		$file = $input->getOption('file');

		$config = (new ConfigurationLoader())(FolderHelper::getCwd(), $file);
		$classMap = new ClassMap($config);
		[$classes, $reverse] = $classMap->load();
		[$multiClasses, $multiClassesCount] = $classMap->multiClasses($reverse);
		[$outOfNamespaceClasses, $outOfNamespaceClassesCount] = $classMap->outOfNamespace($classes);

		$classMap->printStats($classes, $multiClasses, $multiClassesCount, $outOfNamespaceClassesCount);

		OutputFacade::info('List of muli-class files');

		foreach ($multiClasses as $file => $classes) {
			echo '    ' . $file . PHP_EOL;
			foreach ($classes as $cl) {
				echo '    => ' . $cl . PHP_EOL;
			}
		}

		if (count($multiClasses) === 0) {
			OutputFacade::info('No files with multiple classes');
		}

		OutputFacade::info('List of files with classes out of base namespace');

		foreach ($outOfNamespaceClasses as $file => $classes) {
			echo '    ' . $file . PHP_EOL;
			foreach ($classes as $cl) {
				echo '    => ' . $cl . PHP_EOL;
			}
		}

		if (0 === $outOfNamespaceClassesCount) {
			OutputFacade::info('No files with classes out of base namespace');
		}

		return Command::SUCCESS;
	}
}
