<?php
namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="RecipeRepository")
 */
class Recipe
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
     * @ORM\Column(type="text")
     */
    protected $instructions;

    /**
     * Cooking time in minutes
     *
     * @ORM\Column(type="integer")
     */
    protected $cooking_time;

    /**
     * @ORM\Column(type="text", length=200, nullable=true)
     */
    protected $image_url;

    /**
     * @ORM\OneToMany(targetEntity="RecipeIngredient", mappedBy="recipe")
     * @ORM\JoinColumn(name="recipe_id", referencedColumnName="id")
     */
    protected $recipeIngredients;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->ingredients = new \Doctrine\Common\Collections\ArrayCollection();
    }

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
     * @return Recipe
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
     * Set instructions
     *
     * @param string $instructions
     *
     * @return Recipe
     */
    public function setInstructions($instructions)
    {
        $this->instructions = $instructions;

        return $this;
    }

    /**
     * Get instructions
     *
     * @return string
     */
    public function getInstructions()
    {
        return $this->instructions;
    }

    /**
     * Set cookingTime
     *
     * @param integer $cookingTime
     *
     * @return Recipe
     */
    public function setCookingTime($cookingTime)
    {
        $this->cooking_time = $cookingTime;

        return $this;
    }

    /**
     * Get cookingTime
     *
     * @return integer
     */
    public function getCookingTime()
    {
        return $this->cooking_time;
    }

    /**
     * Set imageUrl
     *
     * @param string $imageUrl
     *
     * @return Recipe
     */
    public function setImageUrl($imageUrl)
    {
        $this->image_url = $imageUrl;

        return $this;
    }

    /**
     * Get imageUrl
     *
     * @return string
     */
    public function getImageUrl()
    {
        return $this->image_url;
    }

    /**
     * Add recipeIngredient
     *
     * @param \AppBundle\Entity\RecipeIngredient $recipeIngredient
     *
     * @return Recipe
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
