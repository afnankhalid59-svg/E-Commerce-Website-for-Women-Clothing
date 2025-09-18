<?php
/**
 * CartModel.php
 *
 * This class provides the model for shopping cart operations.
 * It handles adding, removing, updating, and retrieving cart items
 * and stores them in the session.
 *
 * It extends the abstract Model class (ModelAbstract) to integrate
 * with database handles or validated input when needed.
 *
 * @author Afnan Khalid
 * Reference: 
 *   - CF Ingrams, De Montfort University (cfi@dmu.ac.uk) – Web Application Development (CTEC2712_2023_603) Lecture and Lab Materials
 *   - Previous project: CryptoShow – reused structure
 *   - David Adams, "Shopping Cart System with PHP and MySQL", codeshack.io – logic adapted
 * 
 * @package Royal Silk Leicester
 */
class CartModel extends ModelAbstract
{
    /**
     * Local cart storage (optional, session-based is primary)
     * @var array
     */
    private $cart = [];

    /**
     * Assign the database handle for database operations
     *
     * @param object $database_handle
     */
    public function setDatabaseHandle(object $database_handle)
    {
        $this->database_handle = $database_handle;
    }

    /**
     * Assign validated and sanitised user input
     *
     * @param array $sanitised_input
     */
    public function setValidatedInput(array $sanitised_input)
    {
        $this->sanitised_input = $sanitised_input;
    }

    /**
     * Add an item to the shopping cart with a specified size and quantity
     *
     * If the item already exists (product + size), the quantity is incremented.
     *
     * @param int $productId
     * @param string $size
     * @param int $quantity
     */
    public function addToCart(int $productId, string $size, int $quantity)
    {
        // Unique key based on product ID and size
        $cartKey = $productId . '-' . strtoupper($size);

        if (isset($_SESSION['cart'][$cartKey])) {
            // Increment quantity if item exists
            $_SESSION['cart'][$cartKey]['quantity'] += $quantity;
        } else {
            // Add new item to cart
            $_SESSION['cart'][$cartKey] = [
                'product_id' => $productId,
                'size'       => strtoupper($size),
                'quantity'   => $quantity
            ];
        }
    }

    /**
     * Retrieve all items currently in the cart
     *
     * @return array Returns cart items; empty array if cart is empty
     */
    public function getCartContents(): array
    {
        return $_SESSION['cart'] ?? [];
    }

    /**
     * Remove a cart item by its product ID
     *
     * Iterates over session cart and unsets matching items.
     *
     * @param int $productId
     */
    public function removeFromCartById(int $productId)
    {
        foreach ($_SESSION['cart'] as $key => $item) {
            if ($item['product_id'] === $productId) {
                unset($_SESSION['cart'][$key]);
            }
        }
    }

    /**
     * Update quantities of multiple cart items
     *
     * Ensures that quantities are at least 1 (no zero or negative values).
     *
     * @param array $updatedQuantities Array with cartKey => quantity
     */
    public function updateCart(array $updatedQuantities)
    {
        foreach ($updatedQuantities as $cartKey => $qty) {
            if (isset($_SESSION['cart'][$cartKey])) {
                $_SESSION['cart'][$cartKey]['quantity'] = max(1, (int)$qty);
            }
        }
    }
    
    /**
     * Clear all items from the shopping cart
     */
    public function clearCart()
    {
        $_SESSION['cart'] = [];
    }
}