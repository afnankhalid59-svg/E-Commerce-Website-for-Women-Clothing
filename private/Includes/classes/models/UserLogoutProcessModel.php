<?php
/**
 * UserLogoutProcessModel.php
 *
 * Handles user logout process by destroying the session and updating logout result.
 * Extends ModelAbstract.
 *
 * Responsibilities:
 *  - Delete user session and cookies
 *  - Return logout result for controller or view
 * 
 * @author Afnan Khalid
 * Reference: 
 *   - CF Ingrams, De Montfort University (cfi@dmu.ac.uk) – Web Application Development (CTEC2712_2023_603) Lecture and Lab Materials
 *   - Previous project: CryptoShow – reused structure
 *   - David Adams, "Shopping Cart System with PHP and MySQL", codeshack.io – logic adapted
 * 
 * @package Royal Silk Leicester
 */
class UserLogoutProcessModel extends ModelAbstract
{
    /**
     * @var array Holds the result of the logout process
     */
    private array $user_logout_result;

    /**
     * Constructor initializes parent and internal properties
     */
    public function __construct()
    {
        parent::__construct();
        $this->user_logout_result = [];
    }

    /**
     * Destructor (empty, placeholder for future cleanup)
     */
    public function __destruct(){}

    /**
     * Placeholder for setting database handle (not used in logout)
     *
     * @param object $database_handle
     */
    public function setDatabaseHandle($database_handle){}

    /**
     * Get the result of the logout attempt
     *
     * @return array Logout result including success/failure and user ID
     */
    public function getUserLogoutResult(): array
    {
        return $this->user_logout_result;
    }

    /**
     * Placeholder for setting validated input (not used in logout)
     *
     * @param array $sanitised_input
     */
    public function setValidatedInput($sanitised_input){}

    /**
     * Deletes the current user session and related cookie
     *
     * - Checks if a session exists
     * - Unsets $_SESSION array and destroys session
     * - Deletes session cookie
     * - Confirms deletion by checking session file
     * - Updates $user_logout_result with outcome
     *
     * @return void
     */
    public function deleteSession(): void
    {
        $user_logout_result = false;
        $user_logout = [];
        $session_file_path_and_name = '';

        // Get current session ID and user ID
        $session_index = session_id();
        $user_logout['user_id'] = SessionsWrapper::getSession('user_id');

        if ($session_index != '')
        {
            // Determine session file path
            $session_save_path = ini_get('session.save_path');
            $session_name = ini_get('session.name');
            $session_file_path_and_name = $session_save_path . '/' . $session_name . '_' . $session_index;

            // Delete session cookie
            setcookie($session_index, "", time() - 3600);

            // Clear session variables and destroy session
            unset($_SESSION);
            session_destroy();
            session_unset();
        }

        // Verify if session file no longer exists
        if (!file_exists($session_file_path_and_name))
        {
            $user_logout_result = true;
        }

        // Store logout outcome
        $user_logout['logout-result'] = $user_logout_result;

        $this->user_logout_result = $user_logout;
    }
}