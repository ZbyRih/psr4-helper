<?php

namespace ZbyRih\PSR4Helper\Helpers;

use Nette\Loaders\RobotLoader;
use ZbyRih\PSR4Helper\DTO\Configuration;
use ZbyRih\PSR4Helper\Helpers\OutputFacade;

final class ClassMap
{
	public function __construct(
		private Configuration $config,
	) {
		//
	}

	/**
	 * @return array{0: array<string, string>, 1: array<string, array<string>>}
	 */
	public function load(): array
	{
		$robot = new RobotLoader();
		$robot->addDirectory($this->config->basePath);
		$robot->rebuild();

		// returns an array of pairs class => filename
		$classes = $robot->getIndexedClasses();
		$reverse = [];

		foreach ($classes as $class => $file) {
			$reverse[$file][] = $class;
		}

		return [$classes, $reverse];
	}

	/**
	 * @param array<string, array<string>> $reverse
	 * @return array{
	 * 	0: array<string, array<string>>,
	 * 	1: int
	 * }
	 */
	function multiClasses(array $reverse): array
	{
		$multiClassesCount = 0;
		$multiClassesFiles = [];

		foreach ($reverse as $file => $_classes) {
			if (count($_classes) > 1) {
				$multiClassesFiles[$file] = $_classes;
				$multiClassesCount += count($_classes);
			}
		}

		return [$multiClassesFiles, $multiClassesCount];
	}

	/**
	 * @param array<string, string> $classes
	 * @return array{
	 * 	0: array<string, array<string>>,
	 * 	1: int
	 * }
	 */
	function outOfNamespace(array $classes): array
	{
		$nonAppClasses = [];
		$nonAppClassesCount = 0;
		foreach ($classes as $class => $file) {
			if (!str_starts_with($class, $this->config->baseNameSpace)) {
				$nonAppClasses[$file][] = $class;
				$nonAppClassesCount++;
			}
		}

		return [$nonAppClasses, $nonAppClassesCount];
	}

	/**
	 * @param array<string, string> $classes
	 * @param array<string, array<string>> $multiClassesFiles
	 */
	public function printStats(
		array $classes,
		array $multiClassesFiles,
		int $multiClassesCount,
		int $outOfNamespaceClassesCount
	): void {
		OutputFacade::definitionList(
			['Count of classes' => (string) count($classes)],
			['Count of multi-class files' => (string) count($multiClassesFiles)],
			['Count of cLasses in files with multi-classes' => (string) $multiClassesCount],
			[sprintf('Count of classes out of `%s` namespace', $this->config->baseNameSpace) => (string) $outOfNamespaceClassesCount],
		);
	}
}
