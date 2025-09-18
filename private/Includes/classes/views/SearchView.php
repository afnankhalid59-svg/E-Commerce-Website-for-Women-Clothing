<?php
/**
 * SearchView.php
 *
 * Handles rendering of search results for dresses.
 * Displays search term, number of results, and a grid of matching dresses.
 *
 * @author Afnan Khalid
 * Reference: 
 *   - CF Ingrams, De Montfort University (cfi@dmu.ac.uk) – Web Application Development (CTEC2712_2023_603) Lecture and Lab Materials
 *   - Previous project: CryptoShow – reused structure
 *   - David Adams, "Shopping Cart System with PHP and MySQL", codeshack.io – logic adapted
 * 
 * @package Royal Silk Leicester
 */
class SearchView extends WebPageTemplateView
{
    /**
     * Constructor
     * Calls parent constructor to initialize template and page defaults
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Destructor
     * Currently does nothing, but included for potential cleanup
     */
    public function __destruct(){}

    /**
     * Render search results page
     *
     * @param string $searchTerm The term searched for
     * @param array $results Array of matching dresses from DB
     * @param int $rows Number of rows returned
     */
    public function renderSearchResults(string $searchTerm, array $results, int $rows)
    {
        // Set the page title
        $this->setPageTitle();

        // Start building HTML content
        $html = '<main>';
        $html .= '<div class="products content-wrapper">';
        $html .= '<section>';
        $html .= '<h2>Search Results for: \'' . htmlspecialchars($searchTerm) . '\'</h2>';
        $html .= '<h3>Number of Dresses found: ' . htmlspecialchars($rows) . '</h3>';
        $html .= '</section>';

        if ($rows > 0) {
            // Group results by dress_id to combine multiple sizes
            $grouped = [];

            foreach ($results as $row) {
                $key = $row['dress_id'];
                if (!isset($grouped[$key])) {
                    $grouped[$key] = $row;
                    $grouped[$key]['sizes'] = [$row['size']];
                } else {
                    $grouped[$key]['sizes'][] = $row['size'];
                }
            }

            // Media path for images
            $media = MEDIA_PATH;
            $html .= '<div class="this-is-a-grid">';

            // Loop over grouped dresses to build HTML cards
            foreach ($grouped as $row) {
                $html .= '<div class="recentlyadded content-wrapper">';
                $html .= '<div class="products">';
                $html .= '<a href="?route=product&id=' . urlencode($row['dress_id']) . '" class="custom-link">';
                $html .= '<img src="' . htmlspecialchars($media . $row['image_path']) . '" width="200" height="200" alt="' . htmlspecialchars($row['dress_name']) . '" class="resize-image">';
                $html .= '<h3>' . htmlspecialchars($row['dress_name']) . '</h3>';
                $html .= '<h4>£' . number_format($row['price'], 2) . '</h4>';
                $html .= '</a>';
                $html .= '</div>'; 
                $html .= '</div>'; 
            }
            $html .= '</div>'; // close grid

        } else {
            // No results found
            $html .= '<p>No results found for \'' . htmlspecialchars($searchTerm) . '\'.</p>';
        }

        $html .= '</div>'; 
        $html .= '</main>';

        // Store the built HTML into the page content property
        $this->html_page_content = $html;

        // Build the full page (header + footer) using template
        $this->createWebPage();
    }

    /**
     * Public setter for results
     * Essentially a wrapper around renderSearchResults
     *
     * @param string $searchTerm
     * @param array $results
     * @param int $rows
     */
    public function setResults(string $searchTerm, array $results, int $rows): void
    {
        $this->renderSearchResults($searchTerm, $results, $rows);
    }

    /**
     * Return the full HTML page
     *
     * @return string HTML content with header/footer
     */
    public function getHtmlOutput()
    {
        return $this->html_page_output;
    }

    /**
     * Private helper to set the page title
     */
    private function setPageTitle()
    {
        $this->page_title = APP_NAME . ' Search Page';
    }
}