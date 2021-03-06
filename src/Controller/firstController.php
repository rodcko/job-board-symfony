<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class firstController extends AbstractController
{
	/**
	 * @Route("/")
	 */
	public function index() : Response
	{
		return new Response('
<html>
<body>
Hello world!
</body>
</html>
');	
	}

}

