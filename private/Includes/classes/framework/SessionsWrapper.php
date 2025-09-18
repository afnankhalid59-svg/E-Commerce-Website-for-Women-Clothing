<?php
/**
 * Handles all session related method, from starting a session to setting and
 * getting one to unset the session and finally check that the session is off.
 *
 *
 * @author Afnan Khalid
 * @References 
 *   - CF Ingrams, De Montfort University (cfi@dmu.ac.uk) – Web Application Development (CTEC2712_2023_603) Lecture and Lab Materials
 *   - Previous project: CryptoShow – reused structure
 * @package Royal Silk Leicester
 */
class SessionsWrapper
{
    public function __construct(){}

    public function __destruct(){}

    /**
     * Starts a session and regenerates session ID every 30 minutes to enhance security.
     * Both for loggedIn users and guest.
     * @return void
     */
    public static function startSession(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // Define session regeneration interval (30 minutes = 1800 seconds)
        $regenerationInterval = 1800;

        if (isset($_SESSION["user_id"])) {
            // If 'last_regenerated' is not set, initialize it
            if (!isset($_SESSION['last_regenerated'])) {
                self::regenerate_session_id_loggedin();
            } else {
                    // If the regeneration interval has passed, regenerate the session ID
                    if (time() - $_SESSION['last_regenerated'] >= $regenerationInterval) {
                    self::regenerate_session_id_loggedin();
                    }
                }
        } else {
            // If 'last_regenerated' is not set, initialize it
            if (!isset($_SESSION['last_regenerated'])) {
                self::regenerate_session_id();
            } else {
                    // If the regeneration interval has passed, regenerate the session ID
                    if (time() - $_SESSION['last_regenerated'] >= $regenerationInterval) {
                    self::regenerate_session_id();
                    }
                }
        }
    }

    /**
     * Regenerates the session ID of the Logged IN User to prevent session fixation attacks.
     * 
     */
    private static function regenerate_session_id_loggedin() 
    {
        session_regenerate_id(true);
        
        $userId = $_SESSION["user_id"];
        $newSessionId = session_create_id();
        $sessionId = $newSessionId . "_" . $userId; 
        session_id($sessionId);

        $_SESSION['last_regenerated'] = time();

    }

    /**
     * Regenerates the session ID to prevent session fixation attacks.
     * 
     */
    private static function regenerate_session_id() 
    {
        session_regenerate_id(true);
        $_SESSION['last_regenerated'] = time();

    }

    /**
     * Check if a user is logged in.
     * @return bool
     */
    public static function isUserLoggedIn(): bool
    {
        return isset($_SESSION['user_role']);
    }

    /**
     * Get the current logged-in user's role.
     * @return string|null
     */
    public static function getUserRole(): ?string
    {
        return $_SESSION['user_role'] ?? null;
    }

    /**
     * Check if the logged-in user has a specific role.
     * @param string $role
     * @return bool
     */
    public static function userHasRole(string $role): bool
    {
        return isset($_SESSION['user_role']) && $_SESSION['user_role'] === $role;
    }

    /**
     * Returns the session value for the given key or false if not set.
     * @param $session_key
     * @return false|mixed
     */
    public static function getSession(string $session_key): mixed
    {
        return $_SESSION[$session_key] ?? null;
    }

    /**
     * Sets a session value and returns true if successfully set, otherwise false.
     * @param $session_key
     * @param $session_value
     * @return bool
     */
    public static function setSession(string $session_key, mixed $session_value): bool
    {
        $_SESSION[$session_key] = $session_value;
        return isset($_SESSION[$session_key]) && $_SESSION[$session_key] === $session_value;
    }

    /**
     * Removes a session variable if it exists.
     * @param $session_key
     * @return void
     */
    public static function unsetSession($session_key)
    {
        if (isset($_SESSION[$session_key])) {
            unset($_SESSION[$session_key]);
        }
    }

    /**
     * Returns a string representation of the entire $_SESSION array or false if empty.
     * @return false
     */
    public static function getAllSessions()
    {
        return !empty($_SESSION) ? var_export($_SESSION, true) : null;
    }

}