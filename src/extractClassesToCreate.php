<?php

namespace ZbyRih\PSR4Helper;

/**
 * @param array<string, array<int, string>> $multiClassFiles
 * @return array<int, string>
 */
function exctractClassToCreate(array $multiClassFiles): array
{
	$classesToCreate = [];
	foreach ($multiClassFiles as $file => $_classes) {
		foreach ($_classes as $class) {
			$classesToCreate[] = $class;
		}
	}
	return $classesToCreate;
}
