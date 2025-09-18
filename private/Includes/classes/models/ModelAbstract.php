<?php
/**
 * ModelAbstract.php
 *
 * This is an abstract base class for all models in the application.
 * It defines the common structure for database interaction and input handling.
 * 
 * Concrete model classes (like CartModel) must extend this class
 * and implement methods for setting the database handle and validated input.
 *
 * @author Afnan Khalid
 * Reference: 
 *   - CF Ingrams, De Montfort University (cfi@dmu.ac.uk) – Web Application Development (CTEC2712_2023_603) Lecture and Lab Materials
 *   - Previous project: CryptoShow – reused structure
 *   - David Adams, "Shopping Cart System with PHP and MySQL", codeshack.io – logic adapted
 * 
 * @package Royal Silk Leicester
 */
abstract class ModelAbstract
{
    /**
     * Handle for database operations
     * @var object
     */
    protected object $database_handle;

    /**
     * Store messages related to database connection or operations
     * @var array
     */
    protected array $database_connection_messages;

    /**
     * Constructor
     *
     * Initializes the database handle to null and sets up
     * an empty array for database connection messages.
     */
    public function __construct()
    {
        $this->database_handle = (object)null;
        $this->database_connection_messages = [];
    }

    /**
     * Abstract method to assign a database handle
     *
     * Concrete classes must implement this to set their database connection.
     *
     * @param object $database_handle
     */
    abstract protected function setDatabaseHandle(object $database_handle);

    /**
     * Abstract method to assign validated and sanitised input
     *
     * Concrete classes must implement this to handle incoming user data safely.
     *
     * @param array $sanitised_input
     */
    abstract protected function setValidatedInput(array $sanitised_input);
}