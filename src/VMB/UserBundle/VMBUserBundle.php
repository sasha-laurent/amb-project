<?php

namespace VMB\UserBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

class VMBUserBundle extends Bundle
{
	public function getParent()
	{
		return 'FOSUserBundle';
	}
}
