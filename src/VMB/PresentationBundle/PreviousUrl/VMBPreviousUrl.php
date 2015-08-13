<?php
namespace VMB\PresentationBundle\PreviousUrl;

class VMBPreviousUrl
{
	private $router;
	
	public function __construct(\Symfony\Bundle\FrameworkBundle\Routing\Router $router)
	{
		$this->router = $router;
	}
        /*
        How to memorize more than One Previous Url?
                Solution would be to have a 'navigation history stack' which gets popped or stacked on with each page change (kernel listener implementation required), bind it to the user's session and profit from infinite "back" button click
        - Always pass and check CSRF token? Prevents most CSRF attacks
        */ 
	public function getPreviousUrl($request, $default = '/')
	{
		$pos = null;

		// Referer as passed by the header (unsafe - can be modified)
        $referer = $request->headers->get('referer');
        // Target path only has value in case of 403
        $target_p = $request->getSession()->get('_security.target_path'); 
        // Root url from which $request is executed - empty more often than not
        $baseUrl = $request->getBaseUrl();
        // Hardcoded URLs to use instead of baseUrl - not optimal and not extensible..
        // Could have an array of whitelisted IP adresses loaded from conf
        $localhost = "http://127.0.0.1:8000";
        $vmb_path = "http://edu3d.enstb.org/";

		// Can be empty if user set it to empty or browser is set not to forward referers.
        if(empty($referer)) 
        {
        	return $default;
        } else {
        	// Checking if it's an internal URL
        	$pos = strpos($referer, $vmb_path);
                $local_srv = strpos($referer, $localhost);
        	if($pos === false && $local_srv === false) 
                // Referer doesn't begin with our defined VMB Path
                // and isn't running on localhost
        	{
        		return $default;
        	} else { // Return relative path 
        		return $referer;
        	}
        }
	}
}
