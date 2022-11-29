<?php

declare(strict_types=1);

$autoloader = require_once __DIR__ . '/../../vendor/autoload.php';

define("TEST_DIR", __DIR__);
define("TEMP_DIR", TEST_DIR . '/../tmp/' . (isset($_SERVER['argv']) ? md5(serialize($_SERVER['argv'])) : getmypid()));
Tester\Environment::setup();
ini_set("error_reporting", (string)(E_ALL & ~E_DEPRECATED));

function run(Tester\TestCase $testCase)
{
	$testCase->run(isset($_SERVER['argv'][1]) ? $_SERVER['argv'][1] : NULL);
}

abstract class TestCase extends Tester\TestCase
{

	protected function tearDown()
	{
		Mockery::close();
	}

}

return $autoloader;
