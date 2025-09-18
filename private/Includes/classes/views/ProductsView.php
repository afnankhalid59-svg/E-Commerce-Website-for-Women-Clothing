<?php
/**
 * ProductsView.php
 *
 * Handles rendering of the products catalog page with pagination.
 * Extends WebPageTemplateView to generate full HTML pages.
 *
 * Responsibilities:
 *  - Display a list of products in a grid layout
 *  - Show product images, names, and prices
 *  - Render pagination controls if needed
 *
 * @author Afnan Khalid
 * Reference: 
 *   - CF Ingrams, De Montfort University (cfi@dmu.ac.uk) – Web Application Development (CTEC2712_2023_603) Lecture and Lab Materials
 *   - Previous project: CryptoShow – reused structure
 *   - David Adams, "Shopping Cart System with PHP and MySQL", codeshack.io – logic adapted
 * 
 * @package Royal Silk Leicester
 */
class ProductsView extends WebPageTemplateView
{
    /**
     * Array of products to display
     * @var array
     */
    private array $products = [];

    /**
     * Total number of products in store
     * @var int
     */
    private int $totalProducts = 0;

    /**
     * Current page number for pagination
     * @var int
     */
    private int $currentPage = 1;

    /**
     * Number of products displayed per page
     * @var int
     */
    private int $productsPerPage = 4;

    /**
     * Constructor sets the page title
     */
    public function __construct()
    {
        parent::__construct();
        $this->page_title = APP_NAME . ' Products Page';
    }

    /**
     * Main render function to produce the full products catalog HTML
     *
     * @param array $products Array of products to display
     * @param int $totalProducts Total number of products available
     * @param int $currentPage Current page number for pagination
     * @param int $productsPerPage Number of products to show per page
     */
    public function renderProducts(array $products, int $totalProducts, int $currentPage, int $productsPerPage): void
    {
        $this->products = $products;
        $this->totalProducts = $totalProducts;
        $this->currentPage = $currentPage;
        $this->productsPerPage = $productsPerPage;

        $media = MEDIA_PATH; // Base media path for product images
        $html = '';

        // Header section showing total number of products
        $html .= "<section>";
        $html .= "<h1>Products in store: " . htmlspecialchars($this->totalProducts) . "</h1>";
        $html .= "</section>";

        // Display products grid or "no products" message
        if (empty($this->products)) {
            $html .= '<p>No products found.</p>';
        } else {
            $html .= '<div class="this-is-a-grid">';
            foreach ($this->products as $product) {
                $html .= '<div class="recentlyadded content-wrapper">';
                $html .= '<div class="products">';
                $html .= '<a href="?route=product&id=' . urlencode($product['id']) . '" class="custom-link">';
                $html .= '<img src="' . htmlspecialchars($media . $product['image_path']) . '" width="200" height="200" alt="' . htmlspecialchars($product['name']) . '" class="resize-image">';
                $html .= '<h3>' . htmlspecialchars($product['name']) . '</h3>';
                $html .= '<h4>£' . number_format($product['base_price'], 2) . '</h4>';
                $html .= '</a>';
                $html .= '</div>'; 
                $html .= '</div>'; 
            }
            $html .= '</div>'; 
        }

        // Append pagination controls
        $html .= $this->renderPagination();

        // Wrap content in <main> and generate full page
        $this->html_page_content = '<main>' . $html . '</main>';
        $this->createWebPage();
    }

    /**
     * Generates HTML for pagination controls
     *
     * @return string HTML for pagination navigation
     */
    private function renderPagination(): string
    {
        $totalPages = (int) ceil($this->totalProducts / $this->productsPerPage);
        if ($totalPages <= 1) {
            return ''; // No pagination needed
        }

        $html = '<nav class="pagination">';

        // Previous page link
        if ($this->currentPage > 1) {
            $prevPage = $this->currentPage - 1;
            $html .= '<a href="?route=products&p=' . $prevPage . '" class="prev">Previous</a>';
        }

        // Page numbers
        for ($page = 1; $page <= $totalPages; $page++) {
            if ($page == $this->currentPage) {
                $html .= '<span class="current-page">' . $page . '</span>';
            } else {
                $html .= '<a href="?route=products&p=' . $page . '">' . $page . '</a>';
            }
        }

        // Next page link
        if ($this->currentPage < $totalPages) {
            $nextPage = $this->currentPage + 1;
            $html .= '<a href="?route=products&p=' . $nextPage . '" class="next">Next</a>';
        }

        $html .= '</nav>';

        return $html;
    }

    /**
     * Alias method for setResults, internally calls renderProducts
     *
     * @param array $products
     * @param int $totalProducts
     * @param int $currentPage
     * @param int $productsPerPage
     */
    public function setResults(array $products, int $totalProducts, int $currentPage, int $productsPerPage): void
    {
        $this->renderProducts($products, $totalProducts, $currentPage, $productsPerPage);
    }

    /**
     * Getter for full HTML page output
     *
     * @return string Rendered HTML of the products page
     */
    public function getHtmlOutput(): string
    {
        return $this->html_page_output;
    }
}