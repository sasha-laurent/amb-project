<?php
namespace VMB\PresentationBundle\Twig;

class ShortenText extends \Twig_Extension
{
	public function getFilters()
	{
		return array(
			new \Twig_SimpleFilter('shortenText', array($this, 'shortenFilter'))
		);
	}
	
	public function shortenFilter($txt, $length = 50, $replace = '', $breakwords = false, $toggle = null)
	{
		if(strlen($txt) > $length)
		{
			$txt = substr($txt, 0, $length - strlen($replace));
			
			if(!$breakwords) {
				$lastSpacePos = strrpos($txt,' ');
				if($lastSpacePos) {
					$txt = substr($txt, 0, $lastSpacePos).$replace;
				}
				else {
					$txt .= $replace;
				}
			}
			else {
				$txt .= $replace;
			}
		}
		return $txt;
	}
	
	public function getName()
	{
		return 'vmb_extension';
	}
}
