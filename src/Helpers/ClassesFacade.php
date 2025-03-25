<?php

namespace ZbyRih\PSR4Helper\Helpers;

final class ClassesFacade
{
	/**
	 * @param array<string, array<int, string>> $multiClassFiles
	 * @return array<int, string>
	 */
	public static function flatten(array $multiClassFiles): array
	{
		$classesToCreate = [];
		foreach ($multiClassFiles as $file => $_classes) {
			foreach ($_classes as $class) {
				$classesToCreate[] = $class;
			}
		}
		return $classesToCreate;
	}
}
