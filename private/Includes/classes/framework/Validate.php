<?php
/**
 * Validate.php
 *
 * This class centralizes input validation and sanitization logic.
 * It ensures that inputs like routes, strings, and emails meet certain criteria before the app uses them.
 * Helps protect the app from bad or malicious input by filtering and validating user data.
 *
 * @author Afnan Khalid
 * @References 
 *   - CF Ingrams, De Montfort University (cfi@dmu.ac.uk) – Web Application Development (CTEC2712_2023_603) Lecture and Lab Materials
 *   - Previous project: CryptoShow – reused structure
 * @package Royal Silk Leicester
 */

class ValidationException extends \Exception {}

class Validate
{
    private array $errors = [];

    public function __construct() {}

    public function __destruct() {}

    protected object $database_handle;

    public function setDatabaseHandle(object $database_handle): void
    {
        $this->database_handle = $database_handle;
    }

    /**
     * Check that the route name from the browser is a valid route.
     * Throws exception if invalid.
     *
     * @param string $route
     * @return bool
     * @throws ValidationException
     */
    public function validateRoute(string $route): bool
    {
        $allowed_routes = [
            'index',
            'search',
            'user_register',
            'process_new_user_details',
            'user_login',
            'process_login',
            'user_logout',
            'display-crypto-details',
            'landing-page',
            'list_users',
            'products',
            'product',
            'cart'
        ];

        if (!in_array($route, $allowed_routes, true)) {
            throw new ValidationException("invalid route: {$route}");
        }
        return true;
    }


    /**
     * Validate and sanitize a string from input array.
     *
     * @param string $field_name
     * @param array $input
     * @param int|null $min_length
     * @param int|null $max_length
     * @return string|null Sanitized string or null if invalid.
     */
    public function validateString(string $field_name, array $input, ?int $min_length = null, ?int $max_length = null): ?string
    {
        if (empty($input[$field_name])) {
            $this->errors[$field_name] = "{$field_name} is required.";
            return null;
        }

        $raw_string = $input[$field_name];
        $sanitized_string = filter_var($raw_string, FILTER_SANITIZE_SPECIAL_CHARS, FILTER_FLAG_NO_ENCODE_QUOTES);

        $length = mb_strlen($sanitized_string);

        if ($min_length !== null && $length < $min_length) {
            $this->errors[$field_name] = "{$field_name} must be at least {$min_length} characters.";
            return null;
        }
        if ($max_length !== null && $length > $max_length) {
            $this->errors[$field_name] = "{$field_name} must be at most {$max_length} characters.";
            return null;
        }

        return $sanitized_string;
    }


    /**
     * Validate and confirm an email address.
     *
     * @param string $email_field
     * @param array $input
     * @param string $email_confirm_field
     * @param int $max_length
     * @return string|null Sanitized email or null if invalid.
     */
    public function validateEmail(string $email_field, array $input): ?string
    {
        if (empty($input[$email_field])) {
            $this->errors[$email_field] = "Email is required.";
            return null;
        }

        $email = filter_var(trim($input[$email_field]), FILTER_SANITIZE_EMAIL);

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->errors[$email_field] = "Invalid email format.";
            return null;
        }

        if ($this->emailExists($email)) {
            $this->errors[$email_field] = "Email is already registered.";
            return null;
        }

        return mb_strtolower($email);
    }

    /**
     * Validate and sanitize the login email field.
     *
     * - Ensures the email is present.
     * - Validates email format.
     * - Returns the lowercase sanitized email if valid.
     *
     * @param string $email_field The key name of the email field in the input array.
     * @param array $input The array containing user input (e.g., $_POST).
     * @return string|null Sanitized email or null if invalid.
     */

    public function validateEmailLogIn(string $email_field, array $input): ?string
    {
        if (empty($input[$email_field])) {
            $this->errors[$email_field] = "Email is required.";
            return null;
        }

        $email = filter_var(trim($input[$email_field]), FILTER_SANITIZE_EMAIL);

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->errors[$email_field] = "Invalid email format.";
            return null;
        }

        if (!$this->emailExists($email)) {
        $this->errors[$email_field] = "Email not found. Please register first.";
        return null;
    }

        return mb_strtolower($email);
    }


    /**
     * Check if a given email address already exists in the database.
     *
     * This method is typically used during registration to prevent duplicate accounts.
     *
     * @param string $email The email address to check.
     * @return bool Returns true if the email exists in the users table, false otherwise.
     */
    private function emailExists(string $email): bool
    {
        $query = "SELECT email FROM users WHERE email = :email LIMIT 1";
        $params = [':email' => $email];
        $this->database_handle->safeQuery($query, $params);
        return $this->database_handle->countRows() > 0;
    }

    /**
     * Validate a user's password.
     *
     * Password must:
     * - Be present and confirmed
     * - Be at least 8 characters
     * - Contain at least one uppercase, one lowercase, one number, and one special character
     * - Not exceed the maximum length
     *
     * @param string $password_field The name of the password input field.
     * @param array $input The user-submitted form input array.
     * @param int $max_length The maximum allowed password length.
     * @return string|null Sanitized password if valid, or null if validation fails.
     */
    public function validatePassword(string $password_field, array $input, int $max_length): ?string
    {
        if (empty($input[$password_field])) {
            $this->errors[$password_field] = "Password is required.";
            return null;
        }

        $password = trim($input[$password_field]);

        // Length check
        if (mb_strlen($password) > $max_length) {
            $this->errors[$password_field] = "Password must not exceed {$max_length} characters.";
            return null;
        }

        if (mb_strlen($password) < 8) {
            $this->errors[$password_field] = "Password must be at least 8 characters long.";
            return null;
        }

        // Strength checks
        if (
            !preg_match('/[A-Z]/', $password) ||      // At least one uppercase
            !preg_match('/[a-z]/', $password) ||      // At least one lowercase
            !preg_match('/[0-9]/', $password) ||      // At least one digit
            !preg_match('/[\W_]/', $password)         // At least one special char
        ) {
            $this->errors[$password_field] = "Password must contain an uppercase letter, lowercase letter, number, and special character.";
            return null;
        }

        return $password;
    }

    
    /**
     * Validate and authenticate a user's password for login.
     *
     * - Ensures the password is present and within length limits.
     * - Fetches stored hashed password from DB via email.
     * - Uses BcryptWrapper to verify the password.
     *
     * @param string $password_field The key name of the password field in the input array.
     * @param array $input The array containing user input (e.g., $_POST).
     * @param int $max_length Maximum allowed length for the password.
     * @return string|null Returns plain password if valid, otherwise null.
     */

    public function validatePasswordLogIn(string $password_field, array $input, int $max_length): ?string
    {
        if (empty($input[$password_field])) {
            $this->errors[$password_field] = "Password is required.";
            return null;
        }

        $password = trim($input[$password_field]);

        if (mb_strlen($password) > $max_length) {
            $this->errors[$password_field] = "Password must not exceed {$max_length} characters.";
            return null;
        }

        $email = $input['user_email'] ?? null;
        if (!$email) {
            $this->errors[$password_field] = "Email must be provided to validate password.";
            return null;
        }

        return $password;
    }

    /**
     * Retrieve the stored hashed password from the database for a given email.
     *
     * @param string $email The email of the user.
     * @return string|null The stored bcrypt password hash, or null if not found.
     */
    private function getStoredPasswordHash(string $email): ?string
    {
        $query = "SELECT password_hash FROM users WHERE email = :email LIMIT 1";
        $params = [':email' => $email];
        $this->database_handle->safeQuery($query, $params);

        $result = $this->database_handle->safeFetchArray();
        return $result['password_hash'] ?? null;
    }


    /**
     * Validate a user's address ensuring it starts with a number followed by a street name.
     *
     * The address must:
     * - Be present in the input array
     * - Begin with a number (optionally followed by a letter) and then a valid street name
     * 
     * @param string $address_field The key in the input array representing the address.
     * @param array $input The user-provided input array.
     * @return string|null Sanitized address string or null if invalid.
     */
    public function validateAddress(string $address_field, array $input): ?string
    {
        if (empty($input[$address_field])) {
            $this->errors[$address_field] = "Address is required.";
            return null;
        }

        $address = trim($input[$address_field]);

        // Regex: starts with number (optionally followed by letter), then space, then at least one word
        if (!preg_match('/^\d+[a-zA-Z]?\s+[a-zA-Z\s]+$/', $address)) {
            $this->errors[$address_field] = "Address must start with a number followed by the street name (e.g., '123 Main Street').";
            return null;
        }

        return $address;
    }

    /**
     * Validate a city name.
     *
     * The city must:
     * - Be present in the input array
     * - Only contain letters, spaces, or hyphens
     *
     * @param string $city_field The key in the input array representing the city.
     * @param array $input The user-provided input array.
     * @return string|null Sanitized city name or null if invalid.
     */
    public function validateCity(string $city_field, array $input): ?string
    {
        if (empty($input[$city_field])) {
            $this->errors[$city_field] = "City name is required.";
            return null;
        }

        // Sanitize input
        $city = filter_var(trim($input[$city_field]), FILTER_SANITIZE_STRING);


        // Validate format (letters, spaces, and hyphens only)
        $pattern = '/^[a-zA-Z\s\-]+$/';
        if (!preg_match($pattern, $city)) {
            $this->errors[$city_field] = "City name can only contain letters, spaces, and hyphens.";
            return null;
        }

        return $city;
    }

    /**
     * Check if there are any validation errors.
     *
     * @return bool True if errors exist, false otherwise.
     */
    public function hasErrors(): bool
    {
        return !empty($this->errors);
    }

    /**
     * Get all validation errors.
     *
     * @return array Array of field => error message.
     */
    public function getErrors(): array
    {
        return $this->errors;
    }
}