<?php

/**
 * TEST: DI\TracyLoggingExtension */

use Contributte\Logging\DI\TracyLoggingExtension;
use Contributte\Logging\UniversalLogger;
use Nette\DI\Compiler;
use Nette\DI\Container;
use Nette\DI\ContainerLoader;
use Nette\Utils\AssertionException;
use Tester\Assert;
use Tester\FileMock;
use Tracy\Bridges\Nette\TracyExtension;

require_once __DIR__ . '/../../bootstrap.php';

test(function () {
	Assert::exception(function () {
		$loader = new ContainerLoader(TEMP_DIR, TRUE);
		$loader->load(function (Compiler $compiler) {
			$compiler->addExtension('logging', new TracyLoggingExtension());
			$compiler->addExtension('tracy', new TracyExtension());
		}, 1);
	}, AssertionException::class, 'The logging directory (logDir) expects to be string, NULL given.');
});

test(function () {
	$loader = new ContainerLoader(TEMP_DIR, TRUE);
	$class = $loader->load(function (Compiler $compiler) {
		$compiler->addExtension('logging', new TracyLoggingExtension());
		$compiler->addExtension('tracy', new TracyExtension());
		$compiler->loadConfig(FileMock::create('
		logging:
			logDir: some-temp-dir
', 'neon'));
	}, 2);

	/** @var Container $container */
	$container = new $class;

	Assert::type(UniversalLogger::class, $container->getService('logging.logger'));
	Assert::type(UniversalLogger::class, $container->getService('tracy.logger'));
});
