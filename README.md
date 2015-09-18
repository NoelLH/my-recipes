# my-recipes implementation notes

## Choice of language
I'm using PHP for this prototype as it's the accepted server-side language I have the most experience with and time is at a premium.

I also believe that Behat's support for Cucumber-style feature syntax will allow me to directly translate the feature
files provided into working behavioural tests. While Behat is new to me, I'm used to TDD with PHP and hope that the
Symfony bundle I've found will allow me to quickly write good tests for this project.

While the user interface would likely benefit from additional JavaScript to help users maintain their place in lists, I also believe that as front-end JS is not fundamental to the user experience it is good practice to make everything work without script using normal HTTP requests, for maximum accessibility and cross-device compatibility.

As a future enhancement I would look at adding JS functionality progressively, while being careful to keep things working without script and ensuring this with the tests included.

## Schema design

Doctrine ORM makes many-to-many association very easy but this has limitations if you need to do lookups from both
sides.

For this reason, I have used a Doctrine-handled many-to-many relation for 'Stars', meaning that they do not
need their own entity defined. The relation is 'owned' by the User side, making it easy to see all Stars for a user
but not all users who have starred a particular recipe. This seems a reasonable trade-off as that feature is not
currently needed and the schema can be migrated if it is later.

Conversely, recipes & ingredients seem to need bi-directional lookups to implemenet our current features, so I have
defined the joining RecipeIngredient entity separately to make this simpler.

### Known schema glitch

Running `php app/console doctrine:schema:validate` currently confirms that the entity definitions are valid and
self-consistent, but seem to always claim the schema is not in sync, even after force-updating it. I suspect this
is related to our use of Doctrine relations, while SQLite's support for enforcing these properly at DBMS level is
limited.

## Install & run

So far the prototype has only been used locally. The default parameters use SQLite. To install, get
[Composer](https://getcomposer.org/) and run:

`php composer.phar install`

### Database

An empty database `app/data/recipes.db3` is checked into the repo to make it easier to run the project without
SQLite configuration or permissions issues.

### Running the project
 
To quickly start a local web server for the project - at http://localhost:8000 - run:
 
`php app/console server:start`

## Better testing

It would be advisable to add functional testing that is more tightly coupled with what actually happens in the
browser, probably [using Mink with Behat](http://behat.readthedocs.org/en/v2.5/cookbook/behat_and_mink.html) to allow
for either in-browser or headless emulation. This could let us test any future JavaScript enhancements effectively
alongside the existing Behat tests.

## Next steps

Unfortunately due to limited time I was not able to build the starring feature. However a major advantage of
following BDD is that it is easy to verify that the other features are fully implemented as far as specified, and to
be assured that all aspects of these continue to work while building subsequent features.

## Likely additions

For this prototype to be converted to an effective real world system, it's likely we would need some simple extensions
quite early on. In particular, it would benefit from an administrative interface for managing recipes, and
fields to keep basic auditing information about who has created and edited them. So far I have included a user
management system but not yet required authentication.
