<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;

class DefaultController extends Controller
{
    /**
     * @Route("/", name="homepage")
     *
     * @todo Use our root URL for something other than a redirect, once we know more about the system's organisation
     */
    public function indexAction(Request $request)
    {
        return new RedirectResponse('/recipes', 302);
    }
}
