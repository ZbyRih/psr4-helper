<?php

namespace ZbyRih\PSR4Helper;

use Nette\Neon\Neon;

function loadConfiguration(string $cwd): Configuration
{
	$neon = new Neon();
	$neonConfig = $neon->decodeFile($cwd . '\psr4helper.neon');

	$config = new Configuration();
	$config->cwd = $cwd;
	$config->baseFolder = $cwd . '/' . trim($neonConfig['parameters']['path'], '/');
	$config->baseNamespace = $neonConfig['parameters']['namespace'];
	$config->expludeCaseUpdates = $neonConfig['parameters']['expludeCaseUpdates'];
	$config->gitIndexCheckFolder = trim($neonConfig['parameters']['path'], '/');
	$config->excludePsr4CheckClassEndsWith = $neonConfig['parameters']['excludePsr4CheckClassEndsWith'];

	if (!file_exists($config->baseFolder)) {
		throw new \Exception(sprintf('Directory %s not found.', $config->baseFolder));
	}

	return $config;
}
