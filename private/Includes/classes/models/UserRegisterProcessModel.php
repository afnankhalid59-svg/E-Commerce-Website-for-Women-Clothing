<?php
/**
 * UserRegisterProcessModel.php
 *
 * Handles the registration process for a new user, including password hashing and database storage.
 * Extends ModelAbstract.
 *
 * Responsibilities:
 *  - Validate and store new user details
 *  - Hash user password securely before storage
 *  - Return the result of the storage attempt
 * 
 * @author Afnan Khalid
 * Reference: 
 *   - CF Ingrams, De Montfort University (cfi@dmu.ac.uk) – Web Application Development (CTEC2712_2023_603) Lecture and Lab Materials
 *   - Previous project: CryptoShow – reused structure
 *   - David Adams, "Shopping Cart System with PHP and MySQL", codeshack.io – logic adapted
 * 
 * @package Royal Silk Leicester
 */
class UserRegisterProcessModel extends ModelAbstract
{
    /**
     * @var array Holds the result of storing new user details
     */
    private array $store_new_user_details_result;

    /**
     * @var array Stores validated user input for registration
     */
    private array $validated_new_user_details;

    /**
     * Constructor initializes parent and internal properties
     */
    public function __construct()
    {
        parent::__construct();
        $this->store_new_user_details_result = [];
        $this->validated_new_user_details = [];
    }

    /**
     * Destructor (empty placeholder for potential cleanup)
     */
    public function __destruct(){}

    /**
     * Set database handle for executing queries
     *
     * @param object $database_handle Database wrapper object
     */
    public function setDatabaseHandle(object $database_handle): void
    {
        $this->database_handle = $database_handle;
    }

    /**
     * Get result of storing new user details
     *
     * @return array Associative array indicating success/failure
     */
    public function getStoreNewUserDetailsResult(): array
    {
        return $this->store_new_user_details_result;
    }

    /**
     * Set validated user input to be stored
     *
     * @param array $sanitised_input Sanitised and validated registration details
     */
    public function setValidatedInput(array $sanitised_input): void
    {
        $this->validated_new_user_details = $sanitised_input;
    }

    /**
     * Main registration process:
     *  - Hashes the user's password
     *  - Prepares SQL insert query for user details
     *  - Executes safeQuery to store user in database
     *  - Updates result array to indicate success/failure
     */
    public function storeNewUserDetails()
    {
        $new_user_details_stored = false;

        // Include Bcrypt wrapper for secure password hashing
        require_once(__DIR__ . '/../framework/BcryptWrapper.php');
        $user_password = $this->validated_new_user_details['user_hashed_password'];
        $user_hashed_password = \App\Security\BcryptWrapper::hashPassword($user_password);
         
        // Timestamp for when the user is registered
        $user_registered_timestamp = date('Y-m-d H:m:s');

        // Prepare SQL query and parameters
        $sql_query_string = SqlQuery::queryStoreNewUserDetails();
        $sql_query_parameters = [
            ':name' => $this->validated_new_user_details['validated_name'],
            ':surname' => $this->validated_new_user_details['validated_surname'],
            ':email' => $this->validated_new_user_details['validated_email'],
            ':password_hash' => $user_hashed_password,
            ':role' => 'customer',
            ':address' => $this->validated_new_user_details['validated_address'],
            ':city' => $this->validated_new_user_details['validated_city'],
            ':created_at' => $user_registered_timestamp
        ];

        // Execute query
        $query_result = $this->database_handle->safeQuery($sql_query_string, $sql_query_parameters);

        // Check if one row was inserted
        $rows_inserted = $this->database_handle->countRows();
        if ($rows_inserted == 1)
        {
            $new_user_details_stored = true;
        }

        // Store the result of registration process
        $this->store_new_user_details_result['store_new_user_details_result'] = $new_user_details_stored;
    }
}