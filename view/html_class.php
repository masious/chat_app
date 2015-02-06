<?php

class Html
{
	
	function element($elementName, $vars = array())
	{
		return $this->_getContents( $this->_elemsDir() . $elementName . '.php' );
	}
	
	function render($viewName, $vars = array())
	{
		echo $this->_getContents($viewName . '.php', $vars);
	}
	
	private function _elemsDir()
	{
		return VIEW_DIR . 'elements' . DS;
	}
	
	private function _getContents($file, $vars = array())
	{
		$html = $this;
		
		extract($vars);
		
		require( $file );
	}
}