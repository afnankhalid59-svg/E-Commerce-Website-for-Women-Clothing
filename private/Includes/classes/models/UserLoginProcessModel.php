<?php
/**
 * UserLoginProcessModel.php
 *
 * Handles user login authentication, fetching user profile details, and updating session data.
 * Extends ModelAbstract and uses a database wrapper for safe queries.
 *
 * Responsibilities:
 *  - Authenticate user with email and hashed password
 *  - Fetch user details from database
 *  - Update user session on successful login
 *  - Provide login result for controller or view
 * 
 * @author Afnan Khalid
 * Reference: 
 *   - CF Ingrams, De Montfort University (cfi@dmu.ac.uk) – Web Application Development (CTEC2712_2023_603) Lecture and Lab Materials
 *   - Previous project: CryptoShow – reused structure
 *   - David Adams, "Shopping Cart System with PHP and MySQL", codeshack.io – logic adapted
 * 
 * @package Royal Silk Leicester
 */
class UserLoginProcessModel extends ModelAbstract
{   
    /**
     * @var array Holds the result of the user authentication process
     */
    private array $authenticate_user_result;

    /**
     * @var array Stores validated user input (email and hashed password)
     */
    private array $validated_user_details;

    /**
     * Constructor initializes parent and internal properties
     */
    public function __construct()
    {
        parent::__construct();
        $this->authenticate_user_result = [];
        $this->validated_user_details = [];
    }

    /**
     * Destructor (empty, required for future clean-up if needed)
     */
    public function __destruct(){}

    /**
     * Set the database handle to be used for queries
     *
     * @param object $database_handle Database wrapper object
     * @return void
     */
    public function setDatabaseHandle($database_handle): void
    {
        $this->database_handle = $database_handle;
    }

    /**
     * Get the result of the user login attempt
     *
     * @return array Login result including success/failure and user details
     */
    public function getUserLoginResult(): array
    {
        return $this->authenticate_user_result;
    }

    /**
     * Set sanitized and validated user input
     *
     * @param array $sanitised_input Validated user credentials
     * @return void
     */
    public function setValidatedInput(array $sanitised_input): void
    {
        $this->validated_user_details = $sanitised_input;
    }

    /**
     * Main login process controller:
     * Authenticates the user and fetches profile info if valid.
     *
     * Updates $authenticate_user_result with success/failure and user details.
     *
     * @return void
     */
    public function processUserLogin(): void
    {
        $authenticated_user = false;
        $user_details = [];

        // Attempt authentication
        $authenticated_user = $this->authenticateUser();

        if ($authenticated_user)
        {
            // Fetch full user profile if authentication succeeded
            $user_details = $this->fetchStoredUserDetails();
            $user_details['authenticate-user-result'] = true;

            // Update session with authenticated user data
            $this->updateUserSession($user_details);
        }
        else
        {
            // Login failed
            $user_details['authenticate-user-result'] = false;
        }

        $this->authenticate_user_result = $user_details;
    }

    /**
     * Authenticates a user by verifying the email and password
     *
     * @return bool True if login is successful, false otherwise
     */
    private function authenticateUser(): bool
    {
        $authenticated_user = false;
        $user_password = $this->validated_user_details['user_hashed_password'];
        $user_email = $this->validated_user_details['validated_email'];

        // Prepare SQL query to fetch stored password for the email
        $sql_query_string = SqlQuery::queryAuthenticateUser();
        $sql_query_parameters = [':email' => $user_email];

        $this->database_handle->safeQuery($sql_query_string, $sql_query_parameters);

        $row_count = $this->database_handle->countRows();

        if ($row_count == 1)
        {
            // Fetch stored hashed password
            $user_recordset = $this->database_handle->safeFetchRow();
            $stored_password = $user_recordset[0];

            // Use BcryptWrapper to verify password
            require_once (__DIR__ . '/../framework/BcryptWrapper.php');
            $authenticated_user = \App\Security\BcryptWrapper::validatePassword($user_password, $stored_password);
        }

        return $authenticated_user;
    }

    /**
     * Fetches full user details using only the email
     *
     * @return array Associative array of user profile details
     */
    private function fetchStoredUserDetails(): array
    {
        $user_details = [];
        $user_email = $this->validated_user_details['validated_email'];

        $sql_query_string = SqlQuery::queryFetchUserDetails();
        $sql_query_parameters = [':email' => $user_email];

        $this->database_handle->safeQuery($sql_query_string, $sql_query_parameters);
        $user_recordset = $this->database_handle->safeFetchArray1();

        // Map database columns to user details array
        $user_details['user_id'] = $user_recordset['id'];
        $user_details['user_name'] = $user_recordset['name'];
        $user_details['email'] = $user_recordset['email'];
        $user_details['user_role'] = $user_recordset['role'];
        $user_details['user_city'] = $user_recordset['city'];
        $user_details['created_at'] = $user_recordset['created_at'];
        //$user_details['updated_at'] = $user_recordset['updated_at']; // optional

        return $user_details;
    }

    /**
     * Sets authenticated user session
     *
     * @param array $user_login_result User data retrieved from DB
     * @return void
     */
    public function updateUserSession(array $user_login_result): void
    {
        if (isset($user_login_result['user_id'])) {
            SessionsWrapper::setSession('user_id', $user_login_result['user_id']);
        }
    }
}