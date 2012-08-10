<?php

class CodeController {

	private function _newPaste()
	{

	}

	private function _savePaste()
	{

	}

	private function _showCode($id)
	{

	}

	public function __invoke($id)
	{
		return $this->_showCode($id);
	}

	public function __call($name, $arguments)
	{
		$internalName = emplode('-', $name);
		foreach ($internalName as &$_word)
		{
			$_word = ucwords($_word);
		}
		$internalName = implode('', $internalName);

		if (method_exists($this, $internalName))
		{
			return call_user_method_array($internalName, $this, $arguments);
		}

		$code = 404;
		$app->error(function (\Exception $e, $code) {
			return new Response('We are sorry, but something went terribly wrong.', $code);
		});
	}


}