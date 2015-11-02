<?php

namespace ForFun\FunTests\Base;

use ForFun\FunTests\Base\Traits\FunTestOutputTrait;
//use Tests\Base\Traits\FunTestAliasesTrait;

class FunTest
{
	use FunTestOutputTrait;
	//use FunTestAliasesTrait;

	private $bytesStart;
	private $bytesEnd;

	public function run($stopOnFail = false)
	{
		$this->bytesStart = memory_get_usage();
		$methods = get_class_methods($this);
		foreach($methods as $method) {
			if(preg_match('/^test/', $method)) {
				try {
					$this->{$method}();
				}
				catch(\Exception $e) {
					$this->fail($e->getMessage());
					if($stopOnFail) {
						die();
					}
				}
			}
		}

		$this->bytesEnd = memory_get_usage();

		$this->logMemUsage();
	}

	protected function assertEq($value, $expected)
	{
		$backtrace = debug_backtrace();
		$callingFunction = $backtrace[1]['function'];

		if(is_bool($value)) {
			$value = $value ? 'true' : 'false';
		}

		if(is_bool($expected)) {
			$expected = $expected ? 'true' : 'false';
		}

		if($value == null) {
			$value = 'null';
		}

		if($expected == null) {
			$expected = 'null';
		}

		if($value == $expected) {
			$this->ok(sprintf('Got value: %s; %s method call is ok', $value, $callingFunction));
		}
		else {
			$error = sprintf(
				"!!! FUCK !!!\nShit happened in %s; Expected %s, %s given", 
				$callingFunction, 
				$expected, 
				$value
			);
			throw new \Exception($error);
		}
	}

	private function logMemUsage()
	{
		//$this->log('bytes at start: ' . $this->bytesStart);
		//$this->log('bytes at end: ' . $this->bytesEnd);
		$bUnits = ['b', 'kb', 'mb', 'gb'];
		$bytes = $this->bytesEnd - $this->bytesStart;
		//$this->log('bytes: ' . $bytes);
		$bytes = max($bytes, 0);
		$pow = floor(($bytes ? log($bytes) : 0) / log(1024)); 
		$pow = min($pow, count($bUnits) - 1);
		$bytes /= (1 << (10 * $pow));
		$this->log(sprintf("\n%.2f %s of memory used\n", round($bytes, 2), $bUnits[$pow]));
	}
}
