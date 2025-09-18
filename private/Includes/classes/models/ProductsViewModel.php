<?php
/**
 * ProductsViewModel.php
 *
 * ViewModel for fetching paginated products from the database.
 *
 * Responsibilities:
 *  - Provide product data to the controller with pagination
 *  - Encapsulate SQL queries for product listing
 *  - Count total number of products in the database
 *
 * @author
 *   Afnan Khalid
 *
 * References:
 *   - CF Ingrams, De Montfort University, Web Application Development (CTEC2712_2023_603)
 *   - Codeshack.io, Shopping Cart System with PHP and MySQL, David Adams
 *
 * @package Royal Silk Leicester
 */
class ProductsViewModel extends ModelAbstract
{
    /**
     * Sanitised user input (if any is passed in future)
     * @var array
     */
    private array $validated_input = [];

    /**
     * Assign database handle from Factory
     *
     * @param object $database_handle
     */
    public function setDatabaseHandle(object $database_handle): void
    {
        $this->database_handle = $database_handle;
    }

    /**
     * Assign validated input to this model
     *
     * @param array $sanitised_input
     */
    public function setValidatedInput(array $sanitised_input): void
    {
        $this->validated_input = $sanitised_input;
    }

    /**
     * Fetch products with pagination
     */
    public function getProductsPaginated(int $currentPage, int $perPage): array
    {
        $offset = ($currentPage - 1) * $perPage;

        $sql = SqlQuery::getProductsPaginated($offset, $perPage);

        // Use safeQuery instead of prepare()
        $this->database_handle->safeQuery($sql['query'], $sql['params']);
        return $this->database_handle->safeFetchAllResults() ?: [];
    }

    /**
     * Get total number of products
     */
    public function getResultCount(): int
    {
        $sql = SqlQuery::getResultCount();
        $this->database_handle->safeQuery($sql);
        $row = $this->database_handle->safeFetchArray1();
        return (int)($row['total'] ?? 0);
    }

    /**
     * Fetch single product by ID
     */
    public function getProductById(int $productId): ?array
    {
        $sql = SqlQuery::queryFetchDressById();
        $params = [':id' => $productId];

        $this->database_handle->safeQuery($sql, $params);
        $row = $this->database_handle->safeFetchArray1();
        return $row ?: null;
    }
}
