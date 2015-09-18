# my-recipes implementation notes

## Choice of language
I'm using PHP for this prototype as I feel that it is a good fit for the requirements, which can be mostly met
effectively with a traditional web-based approach, and it's the server-side language I already have the most
experience with.

I also believe that Behat's support for Cucumber-style feature syntax will allow me to directly translate the feature
files provided into working behavioural tests. While Behat is new to me, I'm used to TDD with PHP and hope that the
Symfony bundle I've found will allow me to quickly write good tests for this project.

While the user interface would likely benefit from additional JavaScript to help users maintain their place in lists,
I also believe that as front-end JS is not the only way to achieve the features described, it is good practice to
make everything work without script using normal HTTP requests where feasible, to maximise accessibility and
cross-device compatibility.

As a future enhancement I would look at adding JS functionality progressively, while being careful to keep things
working without scripts, and ensuring this is the case with the tests provided.

## Schema design

The Doctrine Object-Relation Mapper makes many-to-many association very easy but this has limitations if you need to do
lookups from both sides of a relation.

For this reason, I have used a Doctrine-handled many-to-many relation for 'Stars', meaning that they do not
need their own entity defined. The relation is 'owned' by the User side, making it easy to see all Stars for a user
but not all users who have starred a particular recipe. This seems a reasonable trade-off as that feature is not
currently needed and the schema can be migrated easily if it is required later.

Conversely, recipes & ingredients seem to need bi-directional lookups to implement our current features, so I have
defined the joining `RecipeIngredient` entity separately to make this simpler.

### Known schema glitch

Running `php app/console doctrine:schema:validate` currently confirms that the entity definitions are valid and
self-consistent, but seem to always claim the schema is not in sync, even after force-updating it. I suspect this
is related to our use of Doctrine relations, while SQLite's support for enforcing these properly at DBMS level is
limited. This would need further investigation but would probably be resolve by using a more fully-featured DBMS
in Production.

## Install & run

So far the prototype has only been used locally. The default parameters use SQLite. To install, get
[Composer](https://getcomposer.org/) and run:

`php composer.phar install`

### Database

An empty database `app/data/recipes.db3` is checked into the repo to make it easier to run the project without
SQLite configuration or permissions issues, but you may need to update it with:

`php app/console doctrine:schema:update --force`
`php app/console doctrine:schema:update --force --env=test`

### Running the project
 
To quickly start a local web server for the project - at http://localhost:8000 - run:
 
`php app/console server:start`

### Running tests

Once installed, you can run `bin/behat`, which is configured to test the features in `test`.

These are mostly taken verbatim from the given feature descriptions, while `test/contexts/FeatureContext.php` tells
Behat how to interpret the scenarios.

## Testing rich features

We are [using Mink with Behat](http://behat.readthedocs.org/en/v2.5/cookbook/behat_and_mink.html) to do headless
emulation of user actions for resilient functional testing. Having chosen these tools, as the link
describes we could easily extend this to do real in-browser testing. This might be more valuable in the future if
extending the system to include JavaScript and AJAX functionality.

## Next steps

Unfortunately due to limited time I have not yet been able to build the Stars feature. However a major advantage of
following BDD is that it is easy to verify that the other features are fully implemented as far as specified, and to
be assured that all aspects of these continue to work while building subsequent features.

## Likely additions

For this prototype to be used as an effective real world system, it's likely we would need some simple extensions
quite early on. It would especially benefit from an **administrative interface** for managing recipes easily
rather than by editing a local database with another tool, and fields to keep basic auditing information about who has
created and edited them. So far I have included a user management system as a dependency for later use, but not yet
required authentication.

Another top priority would be introducing more modular **unit testing** alongside our scenario-based Behat tests. We are
already using PHPUnit, so including these to test e.g. entity repository convenience methods would be a
straightforward and valuable addition to ensure code quality going forward.
