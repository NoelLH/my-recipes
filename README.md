# my-recipes implementation notes

## Choice of language
I'm using PHP for this prototype as it's the accepted server-side language I have the most experience with and time is at a premium.

I also believe that Behat's support for Cucumber-style feature syntax will allow me to directly translate the feature
files provided into working behavioural tests. While Behat is new to me, I'm used to TDD with PHP and hope that the
Symfony bundle I've found will allow me to quickly write good tests for this project.

While the user interface would likely benefit from additional JavaScript to help users maintain their place in lists, I also believe that as front-end JS is not fundamental to the user experience it is good practice to make everything work without script using normal HTTP requests, for maximum accessibility and cross-device compatibility.

As a future enhancement I would look at adding JS functionality progressively, while being careful to keep things working without script and ensuring this with the tests included.

## Installing and running this project
So far the prototype has only been used locally. The default parameters use Sqlite. To install, get
[Composer](https://getcomposer.org/) and run:

`php composer.phar install`
 
To quickly start a local web server for the project - at http://localhost:8000 - run:
 
`php app/console server:start`

## Better testing

It would be advisable to add functional testing that is more tightly coupled with what actually happens in the
browser, probably [using Mink with Behat](http://behat.readthedocs.org/en/v2.5/cookbook/behat_and_mink.html) to allow
for either in-browser or headless emulation. This could let us test any future JavaScript enhancements effectively
alongside the existing Behat tests.
