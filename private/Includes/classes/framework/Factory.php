<?php
/**
 * Factory.php
 *
 * Basic Factory design pattern implementation.
 * Centralizes object creation, simplifies repeated tasks like:
 * - Creating objects
 * - Preparing them
 * - Injecting dependencies
 *
 * This class helps manage object instantiation, especially if the process
 * becomes more complex over time.
 *
 * @author: Afnan Khalid
 * References: 
 *   - CF Ingrams, De Montfort University (cfi@dmu.ac.uk) – Web Application Development (CTEC2712_2023_603)
 *   - Previous project: CryptoShow – reused structure
 * @package: Royal Silk Leicester
 */

class Factory
{
    public function __construct(){}

    public function __destruct(){}

    /**
     * Dynamic object creator, pass in a class name as a string, and it returns an instance of that class.
     *
     * @param $class
     * @return mixed
     */
    public static function buildObject($class) //Centralizes object creation (good for maintenance), Supports mocking or substitution (useful in testing or refactoring)
    {
        $object = new $class();
        return $object;
    }

    /**
     * Create a DatabaseWrapper object, inject database settings, establish the connection and return ready-to-use database instance.
     * @return mixed
     */
    public static function createDatabaseWrapper()
    {
        $database = Factory::buildObject('DatabaseWrapper');
        $connection_parameters = databaseConnectionDetails();
        $database->setConnectionSettings($connection_parameters);
        $database->connectToDatabase();
        return $database;
    }
}
