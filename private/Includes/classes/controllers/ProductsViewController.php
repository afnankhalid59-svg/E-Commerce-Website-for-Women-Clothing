<?php
/**
 * ProductsViewController.php
 *
 * Controller for displaying a paginated list of products.
 *
 * Responsibilities:
 * - Retrieve paginated products from the database
 * - Determine the current page and products per page
 * - Render ProductsView with the product list and pagination data
 * - Store the generated HTML output for the router to return
 *
 * Notes:
 * - Authored primarily by Afnan Khalid.
 * - Follows the template/structure (Factory usage, DatabaseWrapper, createHtmlOutput logic) 
 *   provided by CF Ingrams’ lecture material for Web Application Development (CTEC2712_2023_603).
 * - Some logic and ideas for handling product listing and pagination were adapted from:
 *   Codeshack.io, Shopping Cart System with PHP and MySQL, by David Adams.
 *
 * @author: Afnan Khalid
 * References:
 *   - CF Ingrams, De Montfort University, Web Application Development (CTEC2712_2023_603) lecture materials
 *   - Codeshack.io, Shopping Cart System with PHP and MySQL, David Adams
 * @package: Royal Silk Leicester
 */

class ProductsViewController extends ControllerAbstract
{
    public function createHtmlOutput()
    {
        // Create database handle via Factory
        $database_handle = Factory::createDatabaseWrapper();

        // Build the ProductsViewModel and set database
        $model = Factory::buildObject('ProductsViewModel');
        $model->setDatabaseHandle($database_handle);
        $model->setValidatedInput([]);

        // Determine current page number from GET parameters; default to 1
        $current_page = isset($_GET['p']) && is_numeric($_GET['p']) ? (int)$_GET['p'] : 1;
        $products_per_page = 4;

         // Fetch paginated products and total product count
        $products = $model->getProductsPaginated($current_page, $products_per_page);
        $total_products = $model->getResultCount();

        // Build view and render products
        $view = Factory::buildObject('ProductsView');
        $view->renderProducts($products, $total_products, $current_page, $products_per_page);
        
        // Set the controller's HTML output
        $this->html_output = $view->getHtmlOutput();
    }
}