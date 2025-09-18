<?php
/**
 * IndexView.php
 *
 * Handles rendering of the application's main index page.
 * Extends WebPageTemplateView for templating and full-page generation.
 *
 * Responsibilities:
 *  - Set page title
 *  - Retrieve and organize product (dress) data
 *  - Generate HTML for featured categories and top products
 *  - Provide getter for the full HTML output
 * 
 * @author Afnan Khalid
 * Reference: 
 *   - CF Ingrams, De Montfort University (cfi@dmu.ac.uk) – Web Application Development (CTEC2712_2023_603) Lecture and Lab Materials
 *   - Previous project: CryptoShow – reused structure
 *   - David Adams, "Shopping Cart System with PHP and MySQL", codeshack.io – logic adapted
 * 
 * @package Royal Silk Leicester
 */
class IndexView extends WebPageTemplateView
{
    /**
     * Constructor calls parent constructor for template setup
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Destructor (empty, but defined for future cleanup if needed)
     */
    public function __destruct(){}

    /**
     * Main method to create the page
     *
     * Calls helper methods to set title, build body, and generate full page.
     */
    public function createForm()
    {
        $this->setPageTitle();
        $this->createPageBody();
        $this->createWebPage();
    }

    /**
     * Getter for the full rendered HTML page
     *
     * @return string Full HTML output
     */
    public function getHtmlOutput()
    {
        return $this->html_page_output;
    }

    /**
     * Sets the page title using the application name constant
     */
    private function setPageTitle()
    {
        $this->page_title = APP_NAME . ' Index Page';
    }

    /**
     * Creates the main content body of the index page
     *
     * Responsibilities:
     *  - Load all dresses from the database
     *  - Group dresses by category
     *  - Display top 4 dresses per category
     *  - Generate HTML sections dynamically for each category
     */
    private function createPageBody()
    {
        $media = MEDIA_PATH;  // Base path for images
        $page_heading = APP_NAME . ' demonstration';

        // Retrieve all dresses from the database
        $dresses = SqlQuery::getAllDresses();

        // Group dresses by category
        $grouped_dresses = [];
        foreach ($dresses as $dress) {
            $type = $dress['category'] ?? 'Other';  // Default to "Other" if category is missing
            $grouped_dresses[$type][] = $dress;
        }

        $all_sections_html = '';
        $section_counter = 1; // Counter for unique section class names

        // Iterate through each category and generate HTML
        foreach ($grouped_dresses as $category => $dresses_in_category) {
            $class_name = 'category-section-' . $section_counter;

            $category_html = "<h3>$category</h3><div class=\"this-is-a-grid\">";

            $counter = 0;  // Limit number of dresses displayed per category
            foreach ($dresses_in_category as $dress) {
                if ($counter >= 4) break;  // Only show top 4
                $counter++;

                $img_src = $media . ($dress['image_path'] ?? 'default-image.jpg');
                $price = isset($dress['base_price']) ? '£' . number_format($dress['base_price'], 2) : 'Unavailable';

                // Append HTML for each dress using heredoc syntax
                $category_html .= <<<ITEM
                <div class="recentlyadded content-wrapper">
                    <div class="products">
                        <a href="?route=product&id={$dress['id']}" class="custom-link">
                            <div>
                                <img src="$img_src" alt="{$dress['name']}" class="resize-image">
                                <h3>{$dress['name']}</h3>
                                <h4>$price</h4>
                            </div>
                        </a>
                    </div>
                </div>
ITEM;
            }

            $category_html .= '</div>';  // Close grid div
            $all_sections_html .= "<section class=\"$class_name\">$category_html</section>";

            $section_counter++; // Increment for next category
        }

        // Build the full main page HTML
        $this->html_page_content = <<<HTMLFORM
        <main>
            <section>
               <div class="featured">
                    <h2>$page_heading</h2>
                    <p>Browse our curated categories. Only the top 4 from each are shown.</p>
                </div>
            </section>
            $all_sections_html
            <section>
                <h2>More from Royal Silk Leicester</h2>
                <p>Explore timeless fashion pieces for every occasion.</p>
            </section>
        </main>
HTMLFORM;
    }
}