<?php

namespace ZbyRih\PSR4Helper\Helpers;

use Nette\Neon\Neon;
use Nette\Schema\Expect as E;
use Nette\Schema\Processor;
use ZbyRih\PSR4Helper\DTO\Configuration;
use ZbyRih\PSR4Helper\Helpers\FolderHelper;

final class ConfigurationLoader
{
	public function __invoke(string $cwd, string $file = 'psr4helper.neon'): Configuration
	{
		$neon = (new Neon())->decodeFile(FolderHelper::resolve($cwd, $file));

		$schema = E::structure([
			'path' => E::string(),
			'namespace' => E::string(),
			'excludeCaseUpdates' => E::arrayOf(E::string()),
			'excludePsr4CheckClassEndsWith' => E::arrayOf(E::string()),
		]);

		$processor = new Processor();
		$normalized = $processor->process($schema, $neon);

		$config = new Configuration();
		$config->cwd = FolderHelper::normalize($cwd);
		$config->basePath = FolderHelper::resolve($cwd, FolderHelper::trim($normalized->path));
		$config->baseNameSpace = rtrim($normalized->namespace, '\\') . '\\';
		$config->gitIndexCheckFolder = FolderHelper::trim($normalized->path);
		$config->excludeCaseUpdates = $normalized->excludeCaseUpdates;
		$config->excludePsr4CheckClassEndsWith = $normalized->excludePsr4CheckClassEndsWith;

		if (!file_exists($config->basePath)) {
			throw new \Exception(sprintf('Directory %s not found.', $config->basePath));
		}

		return $config;
	}
}
