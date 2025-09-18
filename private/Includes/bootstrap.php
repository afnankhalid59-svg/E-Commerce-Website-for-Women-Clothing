<?php
/**
 * bootstrap.php
 *
 * The following script:
 * Load config and autoloading.
 * Create a Router object using a Factory.
 * Run the router, which processes the request.
 * Generate the HTML.
 * Echo the HTML to the client.
 *
 * @author Afnan Khalid
 * @Reference De Montfort University, CF Ingrams - cfi@dmu.ac.uk & all labs.
 *
 * @package Royal Silk Leicester
 */

//Enables strict type checking in PHP, meaning:
//If a function expects an int and gets a string, PHP will throw a TypeError instead of trying to convert it.
declare(strict_types=1);

    include_once 'settings.php';
    include_once 'autoload.php';

    // Start the session before anything else that might use it
    SessionsWrapper::startSession();

    $router = Factory::buildObject('Router');
    $router->routing();
    $html_result = $router->getHtmlOutput();

    echo $html_result;
