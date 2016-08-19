# Open Monograph Press

This fork explores the possibilities of using OMP for delivery,
administration and printing of PhD theses. Some theses can have
embargo times, or include articles that can not be published openly for
copyright reasons, and in some cases the author may wish to only store
electronic versions of the thesis in closed archives.

> Open Monograph Press (OMP) has been developed by the Public Knowledge Project. For general information about OMP and other open research systems, visit the [PKP web site][pkp].

[![Build Status](https://travis-ci.org/pkp/omp.svg?branch=master)](https://travis-ci.org/pkp/omp)

## Documentation

You will find detailed guides in [docs](docs) folder.

## Using Git development source

Checkout submodules and copy default configuration :

    git submodule update --init --recursive
    cp config.TEMPLATE.inc.php config.inc.php

Install or update dependencies via Composer:

    cd lib/pkp
    # if you don't already have Composer installed:
    curl -sS https://getcomposer.org/installer | php
    php composer.phar update

If your PHP version supports built-in development server :

    php -S localhost:8000

See [Wiki][wiki-dev] for more complete development guide.

## Bugs / Issues

See https://github.com/pkp/pkp-lib/#issues for information on reporting issues.

## Running Tests

See [Unit Tests](https://pkp.sfu.ca/wiki/index.php?title=Unit_Tests), and also [Github Documentation for PKP Contributors](https://pkp.sfu.ca/wiki/index.php?title=Github_Documentation_for_PKP_Contributors) for Travis-based continuous integration testing.

## License

This software is released under the the [GNU General Public License][gpl-licence].

See the file [COPYING][gpl-licence] included with this distribution for the terms
of this license.

Third parties are welcome to modify and redistribute OJS in entirety or parts
according to the terms of this license. PKP also welcomes patches for
improvements or bug fixes to the software.

[pkp]: http://pkp.sfu.ca/
[readme]: docs/README
[wiki-dev]: http://pkp.sfu.ca/wiki/index.php/HOW-TO_check_out_PKP_applications_from_git
[php-unit]: http://phpunit.de/
[gpl-licence]: docs/COPYING
