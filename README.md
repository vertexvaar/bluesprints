# vertexvaar/bluesprints

## Quickstart & Documentation ;)

The "create-project" way (more quick, provides an example app):

    composer create-project vertexvaar/bluedist=dev-master
    cd bluedist/
    php -S localhost:8080 -t app/public/

The "require" way (uses BlueSprints only as library, no example app):

    mkdir bluesprints
    cd bluesprints
    composer require 'vertexvaar/bluesprints:dev-master' 'vertexvaar/bluewelcome:dev-master'
    [ENTER] #(when asked if you want to copy folders)
    php -S localhost:8000 -t resources/public/

open http://localhost:8000 in your favorite browser

## Problems?

You might find some help in the documentation (especially if you get exceptions). If you did not find what you were
searching for you can open a question issue on GitHub.

## TODO

* Let Parsedown link to the correct manual page on GitHub (exception help pages)
