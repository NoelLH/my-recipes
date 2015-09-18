<?php
namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="IngredientRepository")
 */
class Ingredient
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer", nullable=true)
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="string", length=200)
     */
    protected $name;

    /**
     * @ORM\OneToMany(targetEntity="RecipeIngredient", mappedBy="ingredient")
     * @ORM\JoinColumn(name="id", referencedColumnName="ingredient_id")
     */
    protected $recipeIngredients;

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set name
     *
     * @param string $name
     *
     * @return Ingredient
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->recipeIngredients = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Add recipeIngredient
     *
     * @param \AppBundle\Entity\RecipeIngredient $recipeIngredient
     *
     * @return Ingredient
     */
    public function addRecipeIngredient(\AppBundle\Entity\RecipeIngredient $recipeIngredient)
    {
        $this->recipeIngredients[] = $recipeIngredient;

        return $this;
    }

    /**
     * Remove recipeIngredient
     *
     * @param \AppBundle\Entity\RecipeIngredient $recipeIngredient
     */
    public function removeRecipeIngredient(\AppBundle\Entity\RecipeIngredient $recipeIngredient)
    {
        $this->recipeIngredients->removeElement($recipeIngredient);
    }

    /**
     * Get recipeIngredients
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getRecipeIngredients()
    {
        return $this->recipeIngredients;
    }
}
