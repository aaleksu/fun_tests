<?php

namespace ForFun\FunTests\Base\Traits;

trait FunTestAliasesTrait
{
	public function __call($method, $args)
	{
		var_dump($this->alias($method));
		var_dump($args);
		if($this->methodExists($method)) {
			
			//call_user_method($this->alias($method), $this, $args);
		}
	}

	private function alias($method)
	{
		return sprintf('assert%s', ucfirst($method));
	}

	private function methodExists($method)
	{
		$methods = get_class_methods($this);

		return in_array($this->alias($method), $methods);
	}
}
