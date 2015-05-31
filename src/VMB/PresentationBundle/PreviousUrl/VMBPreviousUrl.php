<?php
namespace VMB\PresentationBundle\PreviousUrl;

class VMBPreviousUrl
{
	private $router;
	
	public function __construct(\Symfony\Bundle\FrameworkBundle\Routing\Router $router)
	{
		$this->router = $router;
	}
	
	public function getPreviousUrl($request, $default = '#')
	{
        $referer = $request->headers->get('referer');
        $baseUrl = $request->getBaseUrl();
        
        // If it's an intern url
        if($pos = strpos($referer, $baseUrl)) {
			$lastPath = substr($referer, $pos + strlen($baseUrl));
			try {
				// If the route can not be matched, an exception is thrown
				$this->router->match($lastPath);
			}
			catch(\Exception $e) {			
				return $default;
			}
			return $referer;
		}
		
		// if it's an external url
		return $default;
	}
}
