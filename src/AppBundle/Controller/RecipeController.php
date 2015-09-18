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
        $filtered = false;

        $repo = $this->getDoctrine()->getRepository('AppBundle:Recipe');
        $qb = $repo->createQueryBuilder('r')
            ->leftJoin('r.recipeIngredients', 'ri')
            ->leftJoin('ri.ingredient', 'i');

        // If the filter text field is non-empty, only return part matches on ingredient name or recipe name
        if ($request->get('filterText')) {
            $qb->where('(r.name LIKE :wildcardMatch OR i.name LIKE :wildcardMatch)')
                ->setParameter('wildcardMatch', '%' . $request->get('filterText') . '%');
            $filtered = true;
        }

        // If the maximum cooking time is non-empty, only return recipes with a shorter time in minutes
        if ($request->get('maximumCookingTime')) {
            $qb->andWhere('r.cooking_time <= :maxTime')
                ->setParameter('maxTime', intval($request->get('maximumCookingTime')));
            $filtered = true;
        }

        $recipes = $qb->getQuery()->getResult();

        $paginator  = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $recipes,
            $request->query->getInt('page', 1),
            10
        );

        return [
            'filtered' => $filtered,
            'recipes' => $pagination
        ];
    }
}
