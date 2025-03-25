<?php

namespace ZbyRih\PSR4Helper\Commands;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use ZbyRih\PSR4Helper\Helpers\ConfigurationLoader;
use ZbyRih\PSR4Helper\Helpers\ClassMap;
use ZbyRih\PSR4Helper\Helpers\FolderHelper;
use ZbyRih\PSR4Helper\Helpers\OutputFacade;

final class SearchClassNameByPrefixCommand extends Command
{
	protected function configure(): void
	{
		$this->setName('find');
		$this->setDescription('List of classes with a fully quantified name starting with a given value');
		$this->addArgument('startWith', InputArgument::REQUIRED, 'Start with ...', null);
		$this->addOption('file', 'f', InputOption::VALUE_OPTIONAL, 'Configuration file name', 'psr4helper.neon');
	}

	public function execute(InputInterface $input, OutputInterface $output): int
	{
		OutputFacade::init($input, $output);

		$file = $input->getOption('file');
		$startWith = $input->getArgument('startWith');

		$config = (new ConfigurationLoader())(FolderHelper::getCwd(), $file);
		$classMap = new ClassMap($config);
		[$classes,] = $classMap->load();

		$count = 0;
		foreach ($classes as $className => $_file) {
			if (str_starts_with($className, $startWith)) {
				echo $className . PHP_EOL;
				$count++;
			}
		}

		OutputFacade::definitionList(
			[sprintf('Found classes starting with `%s`', $startWith) => (string) $count],
		);

		return Command::SUCCESS;
	}
}
