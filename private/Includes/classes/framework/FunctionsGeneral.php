<?php
/**
 * General utility functions for the application.
 * Includes access control patterns to restrict parts of the app to logged-in users only.
 *
 * @author Afnan Khalid
 * @References 
 *   - CF Ingrams, De Montfort University (cfi@dmu.ac.uk) – Web Application Development (CTEC2712_2023_603) Lecture and Lab Materials
 *   - Previous project: CryptoShow – reused structure
 * @package Royal Silk Leicester
 */
class FunctionsGeneral
{
    public function __construct() {}

    public function __destruct() {}

    /**
     * Check if the user is logged in or is a guest
     * @return bool True if user is logged in, false otherwise
     */
    public static function checkLoggedIn(): bool
    {
        return (bool) SessionsWrapper::getSession('user_id');
    }
}