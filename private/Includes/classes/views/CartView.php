<?php
/**
 * CartView.php
 *
 * Handles the display of the shopping cart page.
 * Extends WebPageTemplateView to leverage template rendering.
 *
 * Responsibilities:
 *  - Store cart products and quantities
 *  - Calculate subtotal
 *  - Generate HTML output for the shopping cart
 *  - Provide getter for the full page HTML
 *
 * @author Afnan Khalid
 * Reference: 
 *   - CF Ingrams, De Montfort University (cfi@dmu.ac.uk) – Web Application Development (CTEC2712_2023_603) Lecture and Lab Materials
 *   - Previous project: CryptoShow – reused structure
 *   - David Adams, "Shopping Cart System with PHP and MySQL", codeshack.io – logic adapted
 * 
 * @package Royal Silk Leicester
 */
class CartView extends WebPageTemplateView
{
    /**
     * @var array All available products in the catalog
     */
    private array $products = [];

    /**
     * @var array Products added to the cart with their quantities
     */
    private array $products_in_cart = [];

    /**
     * @var float Subtotal of all products in the cart
     */
    private float $subtotal = 0.00;

    /**
     * @var array Raw cart contents passed from controller
     */
    private $cart_contents = [];

    /**
     * Set the raw cart contents from the controller
     *
     * @param array $cart_contents Associative array of product IDs and quantities
     */
    public function setCartContents(array $cart_contents): void
    {
        $this->cart_contents = $cart_contents;
    }

    /**
     * Set the products and cart-specific data for rendering
     *
     * @param array $products List of all product details
     * @param array $products_in_cart Product quantities in the cart
     * @param float $subtotal Total price of all products in the cart
     */
    public function setCartData(array $products, array $products_in_cart, float $subtotal): void
    {
        $this->products = $products;
        $this->products_in_cart = $products_in_cart;
        $this->subtotal = $subtotal;
    }

    /**
     * Generates the HTML output for the shopping cart page
     * 
     * Uses output buffering to capture content, then stores it in
     * $this->html_page_content before building the full page template.
     */
    public function createOutputPage(): void
    {
        $this->page_title = 'Shopping Cart';

        // Path to media/images
        $media = defined('MEDIA_PATH') ? MEDIA_PATH : 'media/';        

        // Start output buffering
        ob_start();
        ?>
        <main>
        <div class="cart content-wrapper">
            <h1>Shopping Cart</h1>
            <form action="?route=cart" method="post">
                <table>
                    <thead>
                        <tr>
                            <td colspan="2">Product</td>
                            <td>Price</td>
                            <td>Quantity</td>
                            <td>Total</td>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($this->products)): ?>
                            <!-- Display message if cart is empty -->
                            <tr>
                                <td colspan="5" style="text-align:center;">
                                    You have no products added in your Shopping Cart
                                </td>
                            </tr>
                        <?php else: ?>
                            <!-- Loop through each product in the cart -->
                            <?php foreach ($this->products as $product): ?>
                                <tr>
                                    <td class="img2">
                                        <a href="?route=product&id=<?= (int)$product['id'] ?>">
                                            <img src="<?= $media . htmlspecialchars($product['image_path']) ?>"
                                                 alt="<?= htmlspecialchars($product['name']) ?>">
                                        </a>
                                    </td>
                                    <td>
                                        <!-- Remove product from cart link -->
                                        <a href="?route=cart&remove=<?= (int)$product['id'] ?>" class="remove">Remove</a>
                                    </td>
                                    <td class="price">&pound;<?= number_format((float)$product['base_price'], 2) ?></td>
                                    <td class="quantity">
                                        <input type="number"
                                               name="quantity-<?= (int)$product['id'] ?>"
                                               value="<?= (int)($this->products_in_cart[$product['id']] ?? 1) ?>"
                                               min="1"
                                               required>
                                    </td>
                                    <td class="price">
                                        &pound;<?= number_format(((float)$product['base_price']) * (int)$this->products_in_cart[$product['id']], 2) ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
                <div class="buttons">
                    <input type="submit" value="Update" name="update" class="btn">
                    <input type="submit" value="Place Order" name="placeorder" class="btn">
                </div>
            </form>
        </div>
        </main>
        <?php
        // Capture buffered output
        $this->html_page_content = ob_get_clean();

        // Build full page (head + header + content + footer)
        $this->createWebPage();
    }

    /**
     * Getter to retrieve the full rendered HTML page
     *
     * @return string Full HTML output
     */
    public function getHtmlOutput(): string
    {
        return $this->html_page_output;
    }
}