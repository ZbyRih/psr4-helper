<?php

namespace ZbyRih\PSR4Helper\DTO;

class Configuration
{
	public string $cwd;
	public string $basePath;
	public string $baseNameSpace;
	public string $gitIndexCheckFolder;

	/** @var array<int, string> */
	public array $excludeCaseUpdates = [];

	/** @var array<int, string> */
	public array $excludePsr4CheckClassEndsWith = [];

	public string $newFileContent = <<<CODE
<?php

namespace {namespace};

CODE;
}
