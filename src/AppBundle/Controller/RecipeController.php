<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class RecipeController extends Controller
{
    /**
     * Lists all recipes, paginated
     *
     * @Route("/recipes", name="recipe_list")
     * @param Request $request
     * @return Response
     * @Template()
     */
    public function listAction(Request $request)
    {
        $recipes = $this->getDoctrine()->getRepository('AppBundle:Recipe')
            ->findAll();

        $paginator  = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $recipes,
            $request->query->getInt('page', 1),
            10
        );

        return ['recipes' => $pagination];
    }
}
