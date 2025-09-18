<?php
/**
 * SearchController.php
 *
 * Controller for handling search requests and displaying results for dresses.
 *
 * Responsibilities:
 * - Validate and sanitize search input
 * - Query the database via SearchModel
 * - Pass search results to SearchView for rendering
 * - Store the generated HTML output for the router to return
 *
 * Notes:
 * - Authored primarily by Afnan Khalid.
 * - Follows the template/structure (Factory usage, DatabaseWrapper, createHtmlOutput logic) 
 *   provided by CF Ingrams’ lecture material for Web Application Development (CTEC2712_2023_603).
 * - Some logic ideas for handling search/filter were adapted from:
 *   Codeshack.io, Shopping Cart System with PHP and MySQL, by David Adams.
 *
 * @author: Afnan Khalid
 * References:
 *   - CF Ingrams, De Montfort University, Web Application Development (CTEC2712_2023_603) lecture materials
 *   - Codeshack.io, Shopping Cart System with PHP and MySQL, David Adams
 * @package: Royal Silk Leicester
 */

class SearchController extends ControllerAbstract
{
    public function createHtmlOutput()
    {
        // Handle POST request with non-empty search term
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['search_term'])) 
        {
            // Sanitize input
            $searchTerm = trim(filter_input(INPUT_POST, 'search_term', FILTER_UNSAFE_RAW));
            $searchTerm = htmlspecialchars($searchTerm, ENT_QUOTES, 'UTF-8');
            
            // Create database handle via Factory
            $database_handle = Factory::createDatabaseWrapper();

            // Build model and set database & validated input
            $model = Factory::buildObject('SearchModel');
            $model->setDatabaseHandle($database_handle);
            $model->setValidatedInput(['search_term' => $searchTerm]);
            
            // Execute search query
            $results = $model->searchDresses();
            $rows = $model-> getResultCount();

            // Build view and render search results
            $view = Factory::buildObject('SearchView');
            $view->renderSearchResults($searchTerm, $results, $rows);
            
            // Set controller's HTML output
            $this->html_output = $view->getHtmlOutput();
        } else {
            $this->html_output = "<p>No search term submitted.</p>";
        }
    }
}
