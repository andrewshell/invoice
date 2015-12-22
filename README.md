Stupid Simple Invoices
======================
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/andrewshell/invoice/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/andrewshell/invoice/?branch=master)
[![Code Coverage](https://scrutinizer-ci.com/g/andrewshell/invoice/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/andrewshell/invoice/?branch=master)
[![Build Status](https://travis-ci.org/andrewshell/invoice.svg?branch=master)](https://travis-ci.org/andrewshell/invoice)

I needed a quick tool to track and generate invoices for my small random
freelance jobs.  I didn't need the advanced features of a tool like
Freshbooks (or the cost) so I whipped this up.

## How To Use
1. Clone this repo into a local folder
2. Make sure you have PHP 5.4 and composer installed
3. Run `composer install`
4. Run `./bin/run` and go to [localhost:3000](http://localhost:3000/)
5. Look at the sample invoice content in the invoices folder, create your own invoices
6. Find the invoice in the web interface and print to PDF

## Customizing
If you're technical enough to use this app you're probably technical enough
to customize it.

Fork the repo and the [twig](http://twig.sensiolabs.org/) templates are all in `src/Radar/Resources/views`

If you have specific requests for me I might consider expanding the scope of this
project.  [Submit an issue](https://github.com/andrewshell/invoice/issues) or
[Contact Me](http://blog.andrewshell.org/contact-andrew/).

Hoopla!
