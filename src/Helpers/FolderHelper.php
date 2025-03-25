<?php

namespace ZbyRih\PSR4Helper\Helpers;

final class FolderHelper
{
	public static function getCwd(): string
	{
		if (!$cwd = getcwd()) {
			throw new \Exception('Cannot determine current work dir.');
		}

		return $cwd;
	}

	/**
	 * @param string ...$paths
	 */
	public static function resolve(...$paths): string
	{
		return implode(DIRECTORY_SEPARATOR, array_map(fn($p) => self::normalize($p), $paths));
	}

	public static function trim(string $path): string
	{
		return trim(self::normalize($path), DIRECTORY_SEPARATOR);
	}

	public static function normalize(string $path): string
	{
		return str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $path);
	}
}
