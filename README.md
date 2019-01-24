HTTPlug Demo
============

This is a small demo application to show HTTPlug in a Symfony project.

Setup
-----

    git clone
    composer install
    bin/console server:run
    
Now you have / for the synchronous request and /dashboard for the asynchronous requests.

Things to Note
--------------

This application has been created by running

     composer create-project symfony/website-skeleton my-project
     composer require http
     
And then editing `config/packages/httplug.yaml` to configure the plugins.

The controllers are in `src/Controller/` and rely on autowiring.
