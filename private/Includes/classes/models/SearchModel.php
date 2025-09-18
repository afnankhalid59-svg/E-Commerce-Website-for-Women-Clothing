<?php
/**
 * SearchModel.php
 *
 * This class handles searching dresses in the database using a user-provided search term.
 * Extends ModelAbstract and uses a database wrapper for safe queries.
 *
 * Responsibilities:
 *  - Set database handle
 *  - Accept validated input
 *  - Perform search queries
 *  - Return search results and count
 * 
 * @author Afnan Khalid
 * Reference: 
 *   - CF Ingrams, De Montfort University (cfi@dmu.ac.uk) – Web Application Development (CTEC2712_2023_603) Lecture and Lab Materials
 *   - Previous project: CryptoShow – reused structure
 *   - David Adams, "Shopping Cart System with PHP and MySQL", codeshack.io – logic adapted
 * 
 * @package Royal Silk Leicester
 */
class SearchModel extends ModelAbstract
{
    /**
     * @var array Holds validated and sanitized input from the user
     */
    private array $sanitised_input = [];

    /**
     * @var string Search term provided by the user
     */
    private string $searchTerm = '';

    /**
     * @var array Stores results fetched from the database
     */
    private array $results = [];

    /**
     * @var int Stores total number of unique search results
     */
    private int $resultCount = 0;

    /**
     * Set the database handle to be used for queries
     *
     * @param object $database_handle Database wrapper object
     * @return void
     */
    public function setDatabaseHandle(object $database_handle)
    {
        $this->database_handle = $database_handle;
    }

    /**
     * Set sanitized input data
     *
     * @param array $sanitised_input Validated input array
     * @return void
     */
    public function setValidatedInput(array $sanitised_input)
    {
        $this->sanitised_input = $sanitised_input;
    }

    /**
     * Perform a search for dresses based on user input
     *
     * Constructs a SQL query using a LIKE statement for partial matches,
     * executes the query safely, fetches results, and counts unique dresses.
     *
     * @throws Exception If no search term is provided
     * @return array Array of matching dress data rows
     */
    public function searchDresses(): array
    {
        if (!isset($this->sanitised_input['search_term'])) {
            throw new Exception("Search term not provided to model.");
        }

        // Prepare search term for SQL LIKE query
        $term = '%' . $this->sanitised_input['search_term'] . '%';

        // Get SQL query string from SqlQuery helper
        $query = SqlQuery::querySearchDresses(); 

        // Set query parameters
        $params = [':searchTerm' => $term];

        // Execute query safely using database wrapper
        $this->database_handle->safeQuery($query, $params);

        // Fetch all matching results
        $results = $this->database_handle->safeFetchAllResults();

        // Count unique dress IDs for total result count
        $this->resultCount = count(array_unique(array_column($results, 'dress_id')));

        return $results;
    }

    /**
     * Get the number of unique search results
     *
     * @return int Total number of matching dresses
     */
    public function getResultCount(): int
    {
        return $this->resultCount;
    }
}