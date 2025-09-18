<?php
/**
 * ProductView.php
 *
 * Handles rendering of a single product page, including:
 *  - Product image, name, price, and description
 *  - Size selection with stock-aware buttons
 *  - Quantity input and Add to Cart form
 *
 * @author Afnan Khalid
 * Reference: 
 *   - CF Ingrams, De Montfort University (cfi@dmu.ac.uk) – Web Application Development (CTEC2712_2023_603) Lecture and Lab Materials
 *   - Previous project: CryptoShow – reused structure
 *   - David Adams, "Shopping Cart System with PHP and MySQL", codeshack.io – logic adapted
 * 
 * @package Royal Silk Leicester
 */
class ProductView extends WebPageTemplateView
{
    /**
     * Array holding the product data
     * @var array
     */
    private array $product = [];

    /**
     * Constructor
     *
     * @param array $product Optional product data to prefill the view
     * Sets the page title based on the product name if available
     */
    public function __construct(array $product = [])
    {
        parent::__construct();
        $this->product = $product;
        $this->page_title = $this->product['name'] ?? APP_NAME . ' Product';
        // Don't automatically build the page — controller should call createWebPage()
    }

    /**
     * Set or update product data
     *
     * @param array $product Product data array
     */
    public function setProductData(array $product): void
    {
        $this->product = $product;
        $this->page_title = $this->product['name'] ?? APP_NAME . ' Product';
    }

    /**
     * Build the inner page content (main area)
     * Does not echo; stores HTML in $this->html_page_content
     */
    private function buildContent(): void
    {
        $media = defined('MEDIA_PATH') ? MEDIA_PATH : 'media/';
        $img = htmlspecialchars($this->product['image_path'] ?? '');
        $name = htmlspecialchars($this->product['name'] ?? 'Product');
        $price = isset($this->product['base_price']) ? number_format((float)$this->product['base_price'], 2) : '0.00';
        $description = nl2br(htmlspecialchars($this->product['description'] ?? ''));
        $id = htmlspecialchars($this->product['id'] ?? '');

        // Sizes and stock quantities (default to S/M/L with 0 quantity)
        $sizes = $this->product['sizes'] ?? ['S' => 0, 'M' => 0, 'L' => 0];

        $imgSrc = $media . $img;

        // Build size buttons dynamically based on stock
        $sizeButtons = '';
        foreach (['S', 'M', 'L'] as $size) {
            $qty = (int)($sizes[$size] ?? 0);
            $class = $qty > 0 ? 'size-option' : 'size-option out-of-stock';
            $disabled = $qty > 0 ? '' : 'data-disabled="true"';
            $sizeButtons .= "<span class=\"{$class}\" data-size=\"{$size}\" data-max=\"{$qty}\" {$disabled}>{$size}</span> ";
        }

        // HTML content for the product page (main content area)
        $this->html_page_content = <<<HTML
        <main>
            <div class="product content-wrapper">
                <div class="product-img">
                    <img src="{$imgSrc}" alt="{$name}">
                </div>

                <div class="product-details">
                    <h1 class="name">{$name}</h1>
                    <div class="prices">
                        <span class="price">£{$price}</span>
                    </div>

                    <div class="size-selector">
                        <label>Choose Size:</label>
                        <div class="size-options">{$sizeButtons}</div>
                    </div>

                    <form action="?route=cart" method="post" class="product-form form">
                        <input type="hidden" name="product_id" value="{$id}">
                        <input type="hidden" name="size" id="selected-size" value="">
                        <div class="quantity-wrapper">
                            <label for="quantity">Quantity:</label>
                            <input type="number" name="quantity" id="quantity" value="1" min="1" max="1" required>
                        </div>
                        <button type="submit" class="btn" name="add-to-cart">Add To Cart</button>
                    </form>

                    <div class="description-content">
                        <p>{$description}</p>
                    </div>
                </div>
            </div>
        </main>

        <!-- JavaScript to handle size selection and quantity limits -->
        <script>
        document.addEventListener('DOMContentLoaded', function() {
            const sizeOptions = document.querySelectorAll('.size-option');
            const sizeInput = document.getElementById('selected-size');
            const quantityInput = document.getElementById('quantity');

            sizeOptions.forEach(option => {
                option.addEventListener('click', function() {
                    if (this.dataset.disabled) return;

                    // Remove active class from all, add to clicked
                    sizeOptions.forEach(o => o.classList.remove('active'));
                    this.classList.add('active');

                    // Set hidden input for selected size
                    sizeInput.value = this.dataset.size;

                    // Set max quantity based on stock
                    quantityInput.max = this.dataset.max;
                    if (parseInt(quantityInput.value) > parseInt(this.dataset.max)) {
                        quantityInput.value = this.dataset.max;
                    }
                });
            });
        });
        </script>
        HTML;
    }

    /**
     * Public render function
     * Builds content and generates full HTML page (header/footer)
     * Controller should call this; does not echo.
     */
    public function render(): void
    {
        $this->buildContent();
        parent::createWebPage(); // Build header, footer, and insert content
    }

    /**
     * Render an error page if product is missing or invalid
     *
     * @param string $message Error message to display
     */
    public function renderError(string $message): void
    {
        $this->page_title = APP_NAME . ' - Error';
        $this->html_page_content = '<main><section class="content-wrapper"><p class="error">' . htmlspecialchars($message) . '</p></section></main>';
        parent::createWebPage();
    }

    /**
     * Return full generated HTML (header + content + footer)
     *
     * @return string Generated HTML page
     */
    public function getHtmlOutput(): string
    {
        return $this->html_page_output;
    }
}