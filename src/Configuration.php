<?php

namespace ZbyRih\PSR4Helper;

class Configuration
{
	public string $cwd;
	public string $baseFolder;
	public string $baseNamespace;
	public string $gitIndexCheckFolder;
	/** @var array<int, string> $expludeCaseUpdates */
	public array $expludeCaseUpdates = [];
	/** @var array<int, string> $-psr4CheckClassEndsWithOmmit */
	public array $excludePsr4CheckClassEndsWith = [];
	public string $newFilecontent = <<<CODE
<?php

namespace {namespace};

CODE;
}
