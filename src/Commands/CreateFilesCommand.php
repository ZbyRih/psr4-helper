<?php

namespace ZbyRih\PSR4Helper\Commands;

use Nette\Utils\FileSystem;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use ZbyRih\PSR4Helper\Helpers\ConfigurationLoader;
use ZbyRih\PSR4Helper\Helpers\ClassesFacade;
use ZbyRih\PSR4Helper\Helpers\ClassMap;
use ZbyRih\PSR4Helper\Helpers\FolderHelper;
use ZbyRih\PSR4Helper\Helpers\OutputFacade;

final class CreateFilesCommand extends Command
{
	protected function configure(): void
	{
		$this->setName('create-files');
		$this->setDescription('Create files by multiple classes');
		$this->addOption('file', 'f', InputOption::VALUE_OPTIONAL, 'Configuration file name', 'psr4helper.neon');
	}

	public function execute(InputInterface $input, OutputInterface $output): int
	{
		OutputFacade::init($input, $output);

		$file = $input->getOption('file');

		$config = (new ConfigurationLoader())(FolderHelper::getCwd(), $file);
		$classMap = new ClassMap($config);
		[, $reverse] = $classMap->load();
		[$multiClasses,] = $classMap->multiClasses($reverse);

		OutputFacade::info('Creating files');

		$classesToCreate = ClassesFacade::flatten($multiClasses);

		foreach ($classesToCreate as $class) {
			$path = explode('\\', $class);
			$class = array_pop($path);
			$file = $config->cwd . '/' . implode('\\', $path) . '/' . $class . '.php';

			if (!file_exists($file)) {
				$_content = str_replace('{namespace}', implode('\\', $path), $config->newFileContent);
				FileSystem::write($file, $_content);
			}
		}

		if (count($classesToCreate) === 0) {
			OutputFacade::info('No classes to create files');
		}

		OutputFacade::success('Done');

		return Command::SUCCESS;
	}
}
