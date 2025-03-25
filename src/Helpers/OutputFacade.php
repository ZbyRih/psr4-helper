<?php

namespace ZbyRih\PSR4Helper\Helpers;

use Symfony\Component\Console\Helper\TableSeparator;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

final class OutputFacade
{
	private static SymfonyStyle $io;

	public static function init(InputInterface $input, OutputInterface $output): void
	{
		self::$io = new SymfonyStyle($input, $output);
	}

	/**
	 * @param string|array<array-key, string>|TableSeparator ...$list
	 * @return void
	 */
	public static function definitionList(string|array|TableSeparator ...$list): void
	{
		self::$io->definitionList(...$list);
	}

	public static function info(string $message): void
	{
		self::$io->info($message);
	}

	public static function success(string $message): void
	{
		self::$io->success($message);
	}

	public static function warning(string $message): void
	{
		self::$io->error($message);
	}

	public static function error(string $message): void
	{
		self::$io->error($message);
	}
}
