<?php
/**
 * ProductController.php
 *
 * Controller for displaying a single product page in the application.
 *
 * Responsibilities:
 * - Validate product ID from GET parameters
 * - Fetch product data from the database using ProductModel
 * - Handle errors if the product does not exist
 * - Build and render ProductView with the retrieved data
 * - Store the generated HTML output for the router to return
 *
 * Notes:
 * - Authored primarily by Afnan Khalid.
 * - Follows the template/structure (Factory usage, DatabaseWrapper, createHtmlOutput logic) 
 *   provided by CF Ingrams’ lecture material for Web Application Development (CTEC2712_2023_603).
 * - Logic and ideas also adapted from:
 *   "Shopping Cart System with PHP and MySQL" by David Adams, codeshack.io
 *
 * @author: Afnan Khalid
 * References:
 *   - CF Ingrams, De Montfort University, Web Application Development (CTEC2712_2023_603) lecture materials
 *   - David Adams, "Shopping Cart System with PHP and MySQL", codeshack.io – logic adapted
 * @package: Royal Silk Leicester
 */

class ProductController extends ControllerAbstract
{
    public function createHtmlOutput(): void
    {
        // Validate 'id' parameter from GET: must exist and be numeric
        if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
            $view = Factory::buildObject('ProductView');
            $view->renderError('Product does not exist!');
            $this->html_output = $view->getHtmlOutput();
            return;
        }

        $productId = (int) $_GET['id'];

        // Create DB handle and model via Factory
        $database_handle = Factory::createDatabaseWrapper();

        $model = Factory::buildObject('ProductModel');
        $model->setDatabaseHandle($database_handle);
        // If needed in future: $model->setValidatedInput([]);

        // Fetch product data by ID; handle error if not found
        $product = $model->fetchDressById($productId);

        if (!$product) {
            $view = Factory::buildObject('ProductView');
            $view->renderError('Product does not exist!');
            $this->html_output = $view->getHtmlOutput();
            return;
        }

        // Build view, set the product data and render (do not echo here)
        $view = Factory::buildObject('ProductView');
        $view->setProductData($product);
        $view->render();

        // Controller exposes the assembled HTML output for Router to return
        $this->html_output = $view->getHtmlOutput();
    }
}