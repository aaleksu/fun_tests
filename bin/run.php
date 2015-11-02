#!/usr/bin/php

<?php

require_once __DIR__ . '/bootstrap.php';

use Tests\Base\Traits\FunTestOutputTrait;

class TestRunner
{
	use FunTestOutputTrait;

	private $argv;
	private $stopOnFail = false;
	private $testFile;

	public function __construct(array $argv = [])
	{
		$this->argv = $argv;
		$this->validateArgv();

		$options = getopt('', ['stop-on-fail:']);
		$this->stopOnFail = array_key_exists('stop-on-fail', $options);
	}

	public function run()
	{
		try {
			if(file_exists($this->testFile)) {
				$this->log(sprintf("Ok. Will require %s file in a while", $this->testFile));
				$this->ok('Ok');
				
				$classPathinfo = pathinfo($this->testFile);
				$className = '\\' . str_replace('/', '\\', $classPathinfo['dirname']) . '\\' . $classPathinfo['filename'];
				$testInstance = new $className();
				$testInstance->run($this->stopOnFail);
			}
			else {
				throw new \Exception('File does not exist ' . $this->argv[1]);
			}

		}
		catch(\Exception $e) {
			$this->fail($e->getMessage());
		}
	}

	private function validateArgv()
	{
		try {
			$argvLen = count($this->argv);
			foreach(range(1, $argvLen - 1) as $argvIndex) {
				if(preg_match('/\.php$/', $this->argv[$argvIndex])) {
					$this->testFile = $this->argv[$argvIndex];
				}
			}

			if($this->testFile == null) {
				throw new \Exception('Missing argument: test file');
			}

			// if($argvLen > 2) {
			// 	throw new \Exception('Too many arguments');
			// }
		}
		catch(\Exception $e) {
			$this->fail($e->getMessage());
		}
	}
}

// $stop = getopt('', ['stop-on-fail:']);
// var_dump($stop);
// die();

// var_dump($argv);
// //var_dump($argc);
// die();

$testRunner = new TestRunner($argv);
$testRunner->run();