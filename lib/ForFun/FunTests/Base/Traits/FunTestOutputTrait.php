<?php

namespace ForFun\FunTests\Base\Traits;

trait FunTestOutputTrait
{
	protected function log($msg)
	{
		printf("\n%s\n", $this->wrapWithYellow($msg));
	}

	public function ok($msg)
	{
		$msg .= ' :)';
		printf("\n%s\n\n", $this->wrapWithGreen($msg));
	}

	public function fail($msg)
	{
		$msg .= ' :0(';
		printf("\n%s\n\n", $this->wrapWithRed($msg));
	}

	private function wrapWithYellow($msg)
	{
		return sprintf("\033[0;33m%s\033[0m", $msg);
	}

	private function wrapWithGreen($msg)
	{
		return sprintf("\033[0;32m%s\033[0m", $msg);
	}

	private function wrapWithRed($msg)
	{
		return sprintf("\033[0;31m%s\033[0m", $msg);
	}
}
