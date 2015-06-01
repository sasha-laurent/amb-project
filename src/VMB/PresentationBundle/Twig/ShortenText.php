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
	
	public function shortenFilter($txt, $length = 50, $breakwords = false, $replace = '', $toggle = null)
	{
		return substr($txt, 0, $length - strlen($replace)).$replace;
	}
	
	public function getName()
	{
		return 'vmb_extension';
	}
}
