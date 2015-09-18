<?php

namespace AppBundle\Entity;

/**
 * IngredientRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class IngredientRepository extends \Doctrine\ORM\EntityRepository
{
    /**
     * Gets the ingredient with the given name if any exists, or makes one if not and returns that
     *
     * @param string $ingredientName
     * @return Ingredient
     */
    public function getOrMake($ingredientName)
    {
        $ingredient = $this->findOneBy(['name' => $ingredientName]);

        if ($ingredient === null) {
            $ingredient = (new Ingredient)
                ->setName($ingredientName);
        }

        return $ingredient;
    }
}
