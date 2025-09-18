<?php
/**
 * CartController.php
 *
 * Controller for managing the shopping cart in the application.
 *
 * Responsibilities:
 * - Handle adding, updating, and removing items from the cart
 * - Retrieve product data for items in the cart
 * - Calculate subtotal
 * - Build and render CartView
 * - Store the generated HTML output for the router to return
 *
 * Notes:
 * - Authored by Afnan Khalid.
 * - Template/structure (Factory usage, DatabaseWrapper, createHtmlOutput logic) 
 *   based on CF Ingrams’ lecture material for Web Application Development (CTEC2712_2023_603).
 * - Previously adapted in the "CryptoShow" project and readapted here.
 *
 * @author Afnan Khalid
 * Reference: 
 *   - CF Ingrams, De Montfort University (cfi@dmu.ac.uk) – Web Application Development (CTEC2712_2023_603) Lecture and Lab Materials
 *   - Previous project: CryptoShow – reused structure
 *   - David Adams, "Shopping Cart System with PHP and MySQL", codeshack.io – logic adapted
 * 
 * @package Royal Silk Leicester
 */

class CartController extends ControllerAbstract
{
    
    public function createHtmlOutput()
    {
        // Create DB handle and model via Factory
        $database_handle = Factory::createDatabaseWrapper();

        // Build CartModel instance
        $cartModel = Factory::buildObject('CartModel');

        // Gather input: product ID, quantity, size (POST preferred, fallback to GET)
        // Defaults: quantity = 1, size = 'S'
        $productId = $_POST['product_id'] ?? $_GET['product_id'] ?? null;
        $quantity  = $_POST['quantity'] ?? $_GET['quantity'] ?? 1;
        $size      = $_POST['size'] ?? $_GET['size'] ?? 'S';
        $removeId = $_GET['remove'] ?? null;

        // If 'remove' is set in GET, remove that item from cart — ensures cart stays in sync with user actions.
        if (isset($removeId)) {
            $cartModel->removeFromCartById((int)$_GET['remove']);
        }

        if (isset($_POST['update'])) {
            $updatedQuantities = [];

            // Loop through all POST keys to find 'quantity-{productId}' fields
            foreach ($_POST as $key => $value) {
                if (strpos($key, 'quantity-') === 0) {
                    $productIdFromKey = (int) str_replace('quantity-', '', $key);

                    // Match product ID to the correct cart array key
                    // This prevents accidental quantity changes to the wrong cart entry
                    foreach ($cartModel->getCartContents() as $cartKey => $item) {
                        if ($item['product_id'] === $productIdFromKey) {
                            // Quantities are clamped to at least 1 (no zero/negative quantities)
                            $updatedQuantities[$cartKey] = max(1, (int)$value);
                        }
                    }
                }
            }
            $cartModel->updateCart($updatedQuantities);
        }

        // Size is normalized to uppercase to maintain consistency in cart storage
        if ($productId) {
            $cartModel->addToCart((int)$productId, strtoupper($size), (int)$quantity);
        }

        // Retrieve cart contents (array of items with product_id, size, quantity)
        $cartContents = $cartModel->getCartContents();

        $products = [];               // List of product details for rendering
        $products_in_cart = [];        // product_id => quantity
        $subtotal = 0.00;              // Running total of all cart items

        // Create a new Product Model o fetch product details from the database for each item in the cart.
        $productModel = Factory::buildObject('ProductModel');
        $productModel->setDatabaseHandle($database_handle);
        // Load product details for each cart item
        foreach ($cartContents as $cartItem) {
            $pid = $cartItem['product_id'];

            // Fetch product
            $product = $productModel->fetchDressById($pid);

            if (!$product) {
                // If product no longer exists in DB, skip it (keeps cart stable)
                continue; 
            }

            // Add product price × quantity to subtotal for checkout display.
            $products[] = $product;
            $products_in_cart[$pid] = $cartItem['quantity'];
            $subtotal += ((float)$product['base_price']) * (int)$cartItem['quantity'];
        }

        // Build the CartView
        $cartView = Factory::buildObject('CartView');
        $cartView->setCartData($products, $products_in_cart, $subtotal);

        // Generate HTML page
        $cartView->createOutputPage();

        // Set controller output
        $this->html_output = $cartView->getHtmlOutput();
    }
}