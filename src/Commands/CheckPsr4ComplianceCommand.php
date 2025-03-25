<?php

namespace ZbyRih\PSR4Helper\Commands;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use ZbyRih\PSR4Helper\Helpers\ConfigurationLoader;
use ZbyRih\PSR4Helper\DTO\Configuration;
use ZbyRih\PSR4Helper\Helpers\ClassMap;
use ZbyRih\PSR4Helper\Helpers\FolderHelper;
use ZbyRih\PSR4Helper\Helpers\OutputFacade;

final class CheckPsr4ComplianceCommand extends Command
{
	protected function configure(): void
	{
		$this->setName('psr4');
		$this->setDescription('List of classes with wrong folders by PSR-4');
		$this->addArgument('mode', InputArgument::OPTIONAL, 'Mode `info|case|missing`', 'info', ['info', 'case', 'missing']);
		$this->addOption('file', 'f', InputOption::VALUE_OPTIONAL, 'Configuration file name', 'psr4helper.neon');
	}

	public function execute(InputInterface $input, OutputInterface $output): int
	{
		OutputFacade::init($input, $output);

		$file = $input->getOption('file');
		$mode = $input->getArgument('mode');

		$config = (new ConfigurationLoader())(FolderHelper::getCwd(), $file);
		$classMap = new ClassMap($config);
		[$classes,] = $classMap->load();

		$countCase = 0;
		$countMissing = 0;

		$base = FolderHelper::resolve($config->cwd, '');

		$stripBase = function (string $str) use ($base) {
			return str_replace($base, '', $str);
		};

		$shouldOmitted = function (Configuration $config, string $className) {
			foreach ($config->excludePsr4CheckClassEndsWith as $omit) {
				if (str_ends_with($className, $omit)) {
					return true;
				}
			}
			return false;
		};

		foreach ($classes as $class => $_file) {
			if ($shouldOmitted($config, $class)) {
				continue;
			}

			$file = FolderHelper::resolve($config->cwd, $class . '.php');
			$rFile = realpath($file);

			if (!$rFile) {
				$countMissing++;
				if ($mode != 'case') {
					echo ' >> ' . $stripBase($_file) . PHP_EOL;
					echo '    ' . $stripBase($file) . PHP_EOL;
				}
				continue;
			}

			if ($file !== $rFile) {
				$countCase++;
				if ($mode != 'missing') {
					echo ' ^^ ' . $stripBase($file) . PHP_EOL;
					echo '    ' . $stripBase($rFile) . PHP_EOL;
				}
				continue;
			}
		}

		OutputFacade::definitionList(
			['Missing' => (string) $countMissing],
			['Case mismatch' => (string) $countCase],
		);

		return Command::SUCCESS;
	}
}
