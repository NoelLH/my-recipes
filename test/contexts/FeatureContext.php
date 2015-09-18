<?php
use AppBundle\DataFixtures\ORM\LoadUsers;
use AppBundle\Entity\Ingredient;
use AppBundle\Entity\Recipe;
use AppBundle\Entity\RecipeIngredient;
use Behat\Behat\Context\Context;
use Behat\Behat\Context\SnippetAcceptingContext;
use Behat\Behat\Tester\Exception\PendingException;
use Behat\Gherkin\Node\TableNode;
use Doctrine\Common\DataFixtures\Executor\ORMExecutor;
use Doctrine\Common\DataFixtures\Loader;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\ORM\EntityManager;
use Symfony\Component\BrowserKit\Client;
use Symfony\Component\HttpFoundation\Session\Session;

/**
 * Defines application features from the specific context.
 */
class FeatureContext implements Context, SnippetAcceptingContext
{
    /**
     * Initializes context.
     *
     * Every scenario gets its own context instance.
     * You can also pass arbitrary arguments to the
     * context constructor through behat.yml.
     * @param Session $session
     */
    public function __construct(EntityManager $em, Client $client, Session $session)
    {
        $this->em = $em;
        $this->session = $session;

        $driver = new \Behat\Mink\Driver\BrowserKitDriver($client);
        $this->browser = new \Behat\Mink\Session($driver);
        $this->browser->start();

        // Start at this page by default, so that scenarios like 'No recipes available' give a response without
        // an explicit 'visit the recipe list' step
        $this->browser->visit('http://localhost:8000/recipes');
    }

    /** @BeforeScenario */
    /** @AfterScenario */
    public function purgeTestDatabase($event)
    {
        // Purge tables
        $purger = new ORMPurger($this->em);
        $executor = new ORMExecutor($this->em, $purger);
        $executor->purge();

        // Load standard fixtures
        $userFixtures = new LoadUsers();
        $loader = new Loader();
        $loader->addFixture($userFixtures);
        $executor->execute($loader->getFixtures());
    }

    /**
     * @Given the following recipes exist in the system:
     */
    public function theFollowingRecipesExistInTheSystem(TableNode $table)
    {
        $ingredientRepo = $this->em->getRepository('AppBundle:Ingredient');

        foreach ($table->getHash() as $row) {
            $newRecipe = (new Recipe)
                ->setName($row['Name'])
                ->setCookingTime($row['Cooking Time'])
                ->setInstructions('Placeholder instructions');

            foreach (explode(', ', $row['Main Ingredients']) as $ingredientName) {
                $ingredient = $ingredientRepo->getOrMake($ingredientName);
                $this->em->persist($ingredient);

                $recipeIngredient = (new RecipeIngredient())
                    ->setRecipe($newRecipe)
                    ->setIngredient($ingredient);
                $this->em->persist($recipeIngredient);
            }

            $this->em->persist($newRecipe);
        }

        $this->em->flush();
    }

    /**
     * @When the filter term :arg1 is entered
     */
    public function theFilterTermIsEntered($arg1)
    {
        $this->browser->visit('http://localhost:8000/recipes');

        $this->browser->getPage()->fillField('filterText', $arg1);
        $this->browser->getPage()->findButton('Filter')->click();
    }

    /**
     * @Then the message :arg1 is displayed
     */
    public function theMessageIsDisplayed($arg1)
    {
        PHPUnit_Framework_Assert::assertContains(
            '<p class="info">' . htmlentities($arg1, ENT_QUOTES) . '</p>',
            $this->browser->getPage()->getContent()
        );
    }

    /**
     * Specs have no header row and a [0] index field containing recipe's name
     *
     * @Then the following recipes are displayed:
     */
    public function theFollowingRecipesAreDisplayed(TableNode $table)
    {
        $pageContent = $this->browser->getPage()->getContent();

        foreach ($table->getRows() as $row) {
            PHPUnit_Framework_Assert::assertContains(
                '>' . $row[0] . '</a></td>',
                $pageContent
            );
        }
    }

    /**
     * Specs have no header row and a [0] index field containing recipe's name
     *
     * @Then only the following recipe is displayed:
     */
    public function onlyTheFollowingRecipeIsDisplayed(TableNode $table)
    {
        $rows = $table->getRows();

        PHPUnit_Framework_Assert::assertCount(1, $rows);

        $row = $rows[0];

        $recipeNameFields = $this->browser->getPage()->findAll('css', '#recipeTable td.recipe-name');

        PHPUnit_Framework_Assert::assertCount(1, $recipeNameFields);

        /**
         * @var \Behat\Mink\Element\NodeElement $field
         */
        $field = $recipeNameFields[0];

        PHPUnit_Framework_Assert::assertEquals($row[0], $field->getText());
    }

    /**
     * @When the maximum cooking time :arg1 is selected
     */
    public function theMaximumCookingTimeIsSelected($arg1)
    {
        // UI gives users hints to enter just a number, so let's remove extra text from specs' input values for now
        $arg1 = trim(str_replace('minutes', '', $arg1));

        $this->browser->visit('http://localhost:8000/recipes');

        $this->browser->getPage()->fillField('maximumCookingTime', $arg1);
        $this->browser->getPage()->findButton('Filter')->click();
    }

    /**
     * @When a recipe is visited that cannot be found
     */
    public function aRecipeIsVisitedThatCannotBeFound()
    {
        $this->browser->visit('http://localhost:8000/recipe/-1');
    }

    /**
     * @Given the system has the following recipe cooking times:
     */
    public function theSystemHasTheFollowingRecipeCookingTimes(TableNode $table)
    {
        foreach ($table->getHash() as $row) {
            $newRecipe = (new Recipe)
                ->setName($row['Recipe'])
                ->setCookingTime($row['Cooking Time'])
                ->setInstructions('Placeholder instructions');
            $this->em->persist($newRecipe);
        }

        $this->em->flush();
    }

    /**
     * @When the :arg1 recipe is visited
     */
    public function theRecipeIsVisited($arg1)
    {
        $recipe = $this->em->getRepository('AppBundle:Recipe')
            ->findOneBy(['name' => $arg1]);

        $this->browser->visit('http://localhost:8000/recipe/' . $recipe->getId());
    }

    /**
     * @Then the cooking time of :arg1 is displayed
     */
    public function theCookingTimeOfIsDisplayed($arg1)
    {
        $time = $this->browser->getPage()->find('css', '.cooking-time')->getText();

        PHPUnit_Framework_Assert::assertEquals($arg1, $time);
    }

    /**
     * @Given the system has the following recipe image:
     */
    public function theSystemHasTheFollowingRecipeImage(TableNode $table)
    {
        foreach ($table->getHash() as $row) {
            $newRecipe = (new Recipe)
                ->setName($row['Recipe'])
                ->setCookingTime(45)
                ->setImageUrl($row['Image URL'])
                ->setInstructions('Placeholder instructions');
            $this->em->persist($newRecipe);
        }

        $this->em->flush();
    }

    /**
     * @Then the image :arg1 is displayed
     */
    public function theImageIsDisplayed($arg1)
    {
        $imageSrc = $this->browser->getPage()->find('css', '.recipe-image')->getAttribute('src');

        PHPUnit_Framework_Assert::assertEquals('/' . $arg1, $imageSrc);
    }

    /**
     * @Given the system has the following recipe ingredients:
     */
    public function theSystemHasTheFollowingRecipeIngredients(TableNode $table)
    {
        $recipeRepo = $this->em->getRepository('AppBundle:Recipe');
        $ingredientRepo = $this->em->getRepository('AppBundle:Ingredient');

        $ingredients = [];
        $RIs = [];

        foreach ($table->getHash() as $row) {
            $recipe = $recipeRepo->findOneBy(['name' => $row['Recipe']]);

            if ($recipe === null) {
                $recipe = (new Recipe)
                    ->setName($row['Recipe'])
                    ->setCookingTime(45)
                    ->setInstructions('Placeholder');
                $this->em->persist($recipe);
            }

            $ingredients[] = $ingredientRepo->getOrMake($row['Ingredient']);

            $RIs[] = (new RecipeIngredient())
                ->setIngredient($ingredients[count($ingredients) - 1])
                ->setRecipe($recipe)
                ->setQuantity($row['Quantity']);
            $this->em->persist($RIs[count($RIs) - 1]);

            $this->em->persist($recipe);
            $this->em->flush();
        }
    }

    /**
     * @Then the ingredients are listed:
     */
    public function theIngredientsAreListed(TableNode $table)
    {
        $ingredientsHtml = $this->browser->getPage()->find('css', '.ingredients-list')->getOuterHtml();

        foreach ($table->getRows() as $row) {
            PHPUnit_Framework_Assert::assertContains(
                '<li>' . $row[0] . '</li>',
                $ingredientsHtml
            );
        }
    }

    /**
     * @When there are no recipes in the system
     */
    public function thereAreNoRecipesInTheSystem()
    {
        $recipes = $this->em->getRepository('AppBundle:Recipe')->findAll();

        PHPUnit_Framework_Assert::assertCount(0, $recipes);
    }

    /**
     * @Then the recipe :arg1
     */
    public function theRecipe($arg1)
    {
        throw new PendingException();
    }

    /**
     * @Then the cooking time of :arg1
     */
    public function theCookingTimeOf($arg1)
    {
        throw new PendingException();
    }

    /**
     * @Then the main ingredients are displayed:
     */
    public function theMainIngredientsAreDisplayed(TableNode $table)
    {
        throw new PendingException();
    }

    /**
     * @When a recipe is selected
     */
    public function aRecipeIsSelected()
    {
        throw new PendingException();
    }

    /**
     * @Then I am taken to the recipe page
     */
    public function iAmTakenToTheRecipePage()
    {
        throw new PendingException();
    }

    /**
     * @Then the recipes along with their cooking time and main ingredients are displayed:
     */
    public function theRecipesAlongWithTheirCookingTimeAndMainIngredientsAreDisplayed(TableNode $table)
    {
        throw new PendingException();
    }

    /**
     * @When there are more than :arg1 recipes in the system
     */
    public function thereAreMoreThanRecipesInTheSystem($arg1)
    {
        throw new PendingException();
    }

    /**
     * @Then only the first :arg1 recipes are shown
     */
    public function onlyTheFirstRecipesAreShown($arg1)
    {
        throw new PendingException();
    }

    /**
     * @Then page navigation elements are displayed
     */
    public function pageNavigationElementsAreDisplayed()
    {
        throw new PendingException();
    }

    /**
     * @Given the user :arg1 exists in the system
     */
    public function theUserExistsInTheSystem($arg1)
    {
        throw new PendingException();
    }

    /**
     * @Given he has no starred recipes
     */
    public function heHasNoStarredRecipes()
    {
        throw new PendingException();
    }

    /**
     * @When he stars the recipe :arg1
     */
    public function heStarsTheRecipe($arg1)
    {
        throw new PendingException();
    }

    /**
     * @Then the system has the following starred recipes for :arg1:
     */
    public function theSystemHasTheFollowingStarredRecipesFor($arg1, TableNode $table)
    {
        throw new PendingException();
    }

    /**
     * @Given he has the starred recipes:
     */
    public function heHasTheStarredRecipes(TableNode $table)
    {
        throw new PendingException();
    }

    /**
     * @When he unstars the recipe :arg1
     */
    public function heUnstarsTheRecipe($arg1)
    {
        throw new PendingException();
    }

    /**
     * @Then the system has no starred recipes for :arg1
     */
    public function theSystemHasNoStarredRecipesFor($arg1)
    {
        throw new PendingException();
    }

    /**
     * @When he filters by starred recipes
     */
    public function heFiltersByStarredRecipes()
    {
        throw new PendingException();
    }

    /**
     * @Then the recipe :arg1 is displayed
     */
    public function theRecipeIsDisplayed($arg1)
    {
        throw new PendingException();
    }
}
