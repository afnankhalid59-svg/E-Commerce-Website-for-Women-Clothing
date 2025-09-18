<?php
/**
 * ProductModel.php
 *
 * This is a concrete model class for product-related operations.
 * It extends ModelAbstract to handle database interactions and validated input.
 * 
 * Responsibilities include:
 * - Assigning the database handle
 * - Assigning sanitized input
 * - Fetching product (dress) data including stock quantities for sizes S, M, L
 *
 * @author Afnan Khalid
 * Reference: 
 *   - CF Ingrams, De Montfort University (cfi@dmu.ac.uk) – Web Application Development (CTEC2712_2023_603) Lecture and Lab Materials
 *   - Previous project: CryptoShow – reused structure
 *   - David Adams, "Shopping Cart System with PHP and MySQL", codeshack.io – logic adapted
 * 
 * @package Royal Silk Leicester
 */
class ProductModel extends ModelAbstract
{
    /**
     * Assign the database handle for this model
     *
     * @param object $database_handle
     */
    public function setDatabaseHandle(object $database_handle)
    {
        $this->database_handle = $database_handle;
    }

    /**
     * Assign validated and sanitized input
     *
     * @param array $sanitised_input
     */
    public function setValidatedInput(array $sanitised_input)
    {
        $this->sanitised_input = $sanitised_input;
    }

    /**
     * Fetch dress data by dress ID from the database
     *
     * Retrieves the base product information and stock quantities
     * for sizes S, M, and L. Returns an associative array if found,
     * otherwise returns null.
     *
     * @param int $dressId The ID of the dress to fetch
     * @return array|null Associative array of product data or null if not found
     */
   public function fetchDressById(int $dressId): ?array
    {
        // Get the query from SqlQuery.php
        $sql_query_string = SqlQuery::queryFetchDressById();

        $sql_query_parameters = [
            ':id' => $dressId
        ];

        // Execute a safe parameterized query
        $this->database_handle->safeQuery($sql_query_string, $sql_query_parameters);
        $rows = $this->database_handle->safeFetchAllResults();

        if (!$rows) {
            // No product found
            return null;
        }

        // Initialize the product array with base data and default size quantities
        $product = [
            'id'          => $rows[0]['id'],
            'name'        => $rows[0]['name'],
            'description' => $rows[0]['description'],
            'base_price'  => $rows[0]['base_price'],
            'image_path'  => $rows[0]['image_path'],
            'sizes'       => [
                'S' => 0,
                'M' => 0,
                'L' => 0
            ]
        ];

        // Populate size quantities from the database rows
        foreach ($rows as $row) {
            $size = strtoupper($row['size'] ?? '');
            if (isset($product['sizes'][$size])) {
                $product['sizes'][$size] = (int) $row['stock_quantity'];
            }
        }

        return $product;
    }
}