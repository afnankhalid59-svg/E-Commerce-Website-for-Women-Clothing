<?php

/**
 * SqlQuery.php
 *
 * This class centralizes and organizes SQL query strings related to customer
 * management and error logging operations. Each method returns a prepared
 * SQL statement string with placeholders for parameter binding, promoting
 * secure and maintainable database interactions.
 * 
 * @author Afnan Khalid
 * @References 
 *   - CF Ingrams, De Montfort University (cfi@dmu.ac.uk) – Web Application Development (CTEC2712_2023_603) Lecture and Lab Materials
 *   - Previous project: CryptoShow – reused structure
 * @package Royal Silk Leicester
 */

class SqlQuery
{

    public function __construct(){}

    public function __destruct(){}

    /**
     * Returns an SQL query string to insert a new customer's details into the database.
     * Uses named parameters for secure binding.
     *
     * Parameters expected:
     * - :customer_name
     * - :customer_email
     * - :customer_hashed_password
     * - :customer_address
     * - :customer_city
     * - :customer_registered_timestamp
     *
     * @return string SQL INSERT statement.
     */
    public static function queryStoreNewUserDetails()
    {
        $sql_query_string  = 'INSERT INTO users SET ';
        $sql_query_string .= 'name = :name, ';
        $sql_query_string .= 'surname = :surname, ';
        $sql_query_string .= 'email = :email, ';
        $sql_query_string .= 'password_hash = :password_hash, ';
        $sql_query_string .= 'role = :role, ';
        $sql_query_string .= 'address = :address, ';
        $sql_query_string .= 'city = :city, ';
        $sql_query_string .= 'created_at = :created_at';
        return $sql_query_string;
    }

     /**
     * Returns an SQL query string to update the image path for a specific dress.
     * (Note: This example uses a hardcoded dress_id and image path.)
     *
     * @return string SQL UPDATE statement for dress image.
     */
    public static function UpdateDressImage()
    {
        $sql_query_string  ='UPDATE dress';
        $sql_query_string .= 'SET image_path = "media/image.jpg"';
        $sql_query_string .= 'WHERE dress_id = 1';
        return $sql_query_string;
    }

    /**
     * Get dress price by image_path filename or URL.
     *
     * @param string $imagePath Image filename or URL to search.
     * @return float|null Returns price if found, null otherwise.
     * @throws Exception On database connection or query errors.
     */
    public static function  getDressPriceByImagePath(string $imagePath): ?float
    {
        $dbDetails = databaseConnectionDetails();

        try {
            $pdo = new PDO(
                $dbDetails['pdo_dsn'],
                $dbDetails['pdo_user_name'],
                $dbDetails['pdo_user_password'],
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                ]
            );

            $sql = "SELECT price FROM dress WHERE image_path = :image_path LIMIT 1";
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':image_path', $imagePath, PDO::PARAM_STR);
            $stmt->execute();

            $result = $stmt->fetch();

            if ($result && isset($result['price'])) {
                return (float) $result['price'];
            }

            return null;

        } catch (PDOException $e) {
            throw new Exception("Database error: " . $e->getMessage());
        }
    }

     /**
     * Retrieves the description of a dress by its image path.
     *
     * @param string $imagePath Image filename or URL to search.
     * @return string|null Returns the description if found; null otherwise.
     * @throws Exception On database connection or query failure.
     */
    public static function  getDressDescriptionByImagePath(string $imagePath): ?string
    {
        $dbDetails = databaseConnectionDetails();

        try {
            $pdo = new PDO(
                $dbDetails['pdo_dsn'],
                $dbDetails['pdo_user_name'],
                $dbDetails['pdo_user_password'],
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                ]
            );

            $sql = "SELECT description FROM dress WHERE image_path = :image_path LIMIT 1";
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':image_path', $imagePath, PDO::PARAM_STR);
            $stmt->execute();

            $result = $stmt->fetch();

            if ($result && isset($result['description'])) {
                return (string) $result['description'];
            }

            return null;

        } catch (PDOException $e) {
            throw new Exception("Database error: " . $e->getMessage());
        }
    }

    /**
     * Retrieves all dresses that have at least one variant in stock.
     *
     * @return array Returns an array of dresses with their details.
     * @throws Exception On database connection or query failure.
     */
    public static function getAllDresses(): array
    {
        $dbDetails = databaseConnectionDetails();

        try {
            $pdo = new PDO(
                $dbDetails['dsn'],
                $dbDetails['dbusername'],
                $dbDetails['dbpassword'],
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                ]
            );

            $sql = "SELECT d.id, d.name, d.category, d.description, d.base_price, d.image_path
                    FROM dress d
                    WHERE EXISTS (
                        SELECT 1
                        FROM dress_variant dv
                        WHERE dv.dress_id = d.id
                        AND dv.in_stock = 1
                    )
                    ORDER BY d.id";

            $stmt = $pdo->query($sql);

            return $stmt->fetchAll();

        } catch (PDOException $e) {
            throw new Exception("Database error: " . $e->getMessage());
        }
    }

    /**
     * Returns an SQL query string to authenticate a customer by verifying
     * their email and hashed password.
     *
     * Parameters expected:
     * - :customer_email
     * - :customer_hashed_password
     *
     * @return string SQL SELECT statement to fetch hashed password.
     */
    public static function queryAuthenticateUser()
    {
        $sql_query_string  = 'SELECT password_hash FROM users ';
        $sql_query_string .= 'WHERE email = :email ';
        $sql_query_string .= 'LIMIT 1';
        return $sql_query_string;
    }

    /**
     * Returns an SQL query string to fetch basic customer details
     * by matching their email and hashed password.
     *
     * Parameters expected:
     * - :customer_email
     * - :customer_hashed_password
     *
     * @return string SQL SELECT statement to fetch customer ID, name, and email.
     */
    public static function queryFetchUserDetails()
    {
        $sql_query_string  = 'SELECT id, name, email, role, city, created_at FROM users ';
        $sql_query_string .= 'WHERE email = :email ';
        $sql_query_string .= 'LIMIT 1';
        return $sql_query_string;
    }

    /**
     * Returns an SQL query string to log an error message into the error log table.
     *
     * Parameters expected:
     * - :log_message
     *
     * @return string SQL INSERT statement to insert a log message.
     */
    public static function queryLogErrorMessage()
    {
        $sql_query_string  = 'INSERT INTO dbrsl_error_log SET log_message = :log_message';
        return $sql_query_string;
    }

    /**
    * Returns an SQL query string to search dresses by matching the concatenated
    * fields of dress name, category, size, color, material, and base price
    * against a search term using a LIKE operator for partial matching.
    *
    * The query joins the 'dress' table with the 'dress_variant' table to include
    * size information and filters results where any of the concatenated fields
    * contain the search term.
    *
    * Parameters expected:
    * - :searchTerm (string): The search term wrapped with wildcards (%) for LIKE matching.
    *
    * @return string SQL SELECT statement for searching dresses.
    */
    public static function querySearchDresses()
    {
        $sql_query_string  = "SELECT d.id AS dress_id, d.name AS dress_name, d.category, d.type, dv.size, d.color, ";
        $sql_query_string .= "d.material, d.base_price AS price, d.image_path ";
        $sql_query_string .= "FROM dress d ";
        $sql_query_string .= "INNER JOIN dress_variant dv ON d.id = dv.dress_id ";
        $sql_query_string .= "WHERE CONCAT(d.name, ' ', d.category, ' ',  d.type, ' ', dv.size, ' ', d.color, ' ', d.material, ' ', d.base_price) ";
        $sql_query_string .= "LIKE :searchTerm";
        return $sql_query_string;
    }

    /**
     * Returns an SQL query string to fetch a paginated list of dresses.
     *
     * Parameters expected:
     * - :offset (int): The starting index for pagination.
     * - :limit (int): The number of records to return.
     *
     * @param int $offset The offset from which to start fetching records.
     * @param int $limit The maximum number of records to fetch.
     * @return array Returns an array with two keys:
     *               'query' => SQL query string with placeholders
     *               'params' => associative array of parameters for binding
     */
    public static function getProductsPaginated(int $offset, int $limit): array
    {
        $query = "SELECT * FROM dress ORDER BY id DESC LIMIT :offset, :limit";
        $params = [
            ':offset' => $offset,
            ':limit' => $limit
        ];

        return [
            'query' => $query,
            'params' => $params
        ];
    }

    /**
     * get all dresses.
     *
     * @return string SQL COUNT statement.
     */
    public static function getResultCount()
    {
        $sql_query_string = "SELECT COUNT(*) AS total FROM dress";
        return $sql_query_string;
    }

    /**
     * Returns an SQL query string to fetch a dress and its variants by dress ID.
     *
     * Parameters expected:
     * - :id (int): The dress ID.
     *
     * @return string SQL SELECT statement.
     */
    public static function queryFetchDressById(): string
    {
        return "
            SELECT 
                d.id,
                d.name,
                d.description,
                d.base_price,
                d.image_path,
                dv.size,
                dv.stock_quantity
            FROM dress d
            LEFT JOIN dress_variant dv 
                ON dv.dress_id = d.id
            WHERE d.id = :id
        ";
    }

    
}


