# my-recipes implementation notes

## Choice of language
I'm using PHP for this prototype as it's the accepted server-side language I have the most experience with and time is at a premium.

I also believe that Behat's support for Cucumber-style feature syntax will allow me to directly translate the feature files provided into working behavioural tests.

While the user interface would likely benefit from additional JavaScript to help users maintain their place in lists, I also believe that as front-end JS is not fundamental to the user experience it is good practice to make everything work without script using normal HTTP requests, for maximum accessibility and cross-device compatibility.

As a future enhancement I would look at adding JS functionality progressively, while being careful to keep things working without script and ensuring this with the tests included.
