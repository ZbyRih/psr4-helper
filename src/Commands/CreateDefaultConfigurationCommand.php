<?php

namespace ZbyRih\PSR4Helper\Commands;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use ZbyRih\PSR4Helper\Helpers\FolderHelper;
use ZbyRih\PSR4Helper\Helpers\OutputFacade;

final class CreateDefaultConfigurationCommand extends Command
{
	protected function configure(): void
	{
		$this->setName('init');
		$this->setDescription('Create configuration file');
		$this->addArgument('file', null, 'Configuration file name', 'psr4helper.neon');
	}

	public function execute(InputInterface $input, OutputInterface $output): int
	{
		OutputFacade::init($input, $output);

		$file = FolderHelper::resolve(FolderHelper::getCwd(), $input->getArgument('file'));

		if (file_exists($file)) {
			OutputFacade::error('Configuration file already exists.');
			return Command::FAILURE;
		}

		file_put_contents($file, <<<NEON
path: App
namespace: App\
excludeCaseUpdates:
	- templates
	- translations
excludePsr4CheckClassEndsWith:
	- Presenter
NEON);

		OutputFacade::success('Configuration file created.');
		return Command::SUCCESS;
	}
}
