<?php
/** 
 * Base class for rendering a full HTML webpage template
 * 
 * @author Afnan Khalid
 * Reference: 
 *   - CF Ingrams, De Montfort University (cfi@dmu.ac.uk) – Web Application Development (CTEC2712_2023_603) Lecture and Lab Materials
 *   - Previous project: CryptoShow – reused structure
 *   - David Adams, "Shopping Cart System with PHP and MySQL", codeshack.io – logic adapted
 * 
 * @package Royal Silk Leicester
 */
class WebPageTemplateView
{
    private $menu_bar; // Stores menu bar HTML (not inherited by child classes)
    protected $page_title; // Page title, visible to child classes
    protected $html_page_content; // Main content of the page
    protected $html_page_output; // Full HTML output including header, content, footer

    // Constructor: initialize all properties
    public function __construct()
    {
        $this->page_title = '';
        $this->html_page_content = '';
        $this->html_page_output = '';
        $this->menu_bar = '';
    }

    // Destructor: empty (can be used for cleanup)
    public function __destruct(){}

    // Main function to assemble the full HTML page
    public function createWebPage()
    {
        //$this->createMenuBar(); // Optional menu bar (currently commented out)
        $this->createWebPageMetaHeadings(); // Create <head> section with meta tags, CSS, fonts
        $this->insertPageContent(); // Insert main content and header
        $this->createWebPageFooter(); // Add footer and scripts
    }

    // Private function to create meta tags, CSS links, fonts, and start <body>
    private function createWebPageMetaHeadings()
    {
        $css_filename = CSS_PATH . CSS_FILE_NAME;

        $html_output = <<< HTML
        <!doctype html>
        <html lang="en">
        <head>
            <meta name="author" content="Afnan Khalid">
            <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
            <meta http-equiv="Content-Language" content="en-gb">
            <meta name="keywords" content="Asian Dresses UK, Oriental Clothes, Pakistani dresses, Indian dresses, Chinese Dresses, Japanese Dresses, Cheongsam dresses UK, Kimono UK, Pakistani dresses online UK, Indian dresses online UK, Pakistani clothes shop in Leicester, Indian clothes shop in Leicester">
            <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no, user-scalable=no">
            <link rel="stylesheet" href="$css_filename" type="text/css">
            <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css"/>
            <link rel="preconnect" href="https://fonts.googleapis.com">
            <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
            <link href="https://fonts.googleapis.com/css2?family=Bungee+Spice&display=swap" rel="stylesheet">
            <title>$this->page_title</title>
        </head>
        <body>
        HTML;

        $this->html_page_output .= $html_output; // Append to page output
    }

    // Private function to insert the main content and header section
    private function insertPageContent()
    {
        // Determine if user is logged in
        $is_logged_in = FunctionsGeneral::checkLoggedIn();
        
        // Login or logout link depending on session
        $login_logout_html = $is_logged_in
            ? '<a href="?route=user_logout"><i class="fas fa-sign-out-alt"></i></a>'
            : '<a href="?route=user_login"><i class="fas fa-user"></i></a>';
        
        // Count items in shopping cart
        $num_items_in_cart = isset($_SESSION['cart']) ? count($_SESSION['cart']) : 0;

        // Build the header and include main content
        $html_output = <<< HTML
        <!-- Header Section -->
        <header class="site-header">
            <div class="header-container">

                <!-- Burger icon for mobile menu -->
                <div class="burger" id="burger">
                    <span></span>
                    <span></span>
                    <span></span>
                </div>

                <!-- Navigation Menu -->
                <nav id="mainNav">
                    <a href="?route=index">Home</a>
                    <a href="?route=products">Products</a>
                    <a href="#">Contact</a>
                </nav>
            
                <!-- Logo -->
                <div class="logo">
                    <a href="?route=index">
                        <img src="media/logo.png" alt="Website Logo">
                    </a>
                </div>

                <!-- Right Section: Search, Login/Logout, Cart -->
                <div class="header-right">

                    <!-- Search Bar -->
                    <div class="search">
                        <form method="post" action="">
                            <input type="hidden" name="route" value="search">
                            <input type="text" name="search_term" placeholder="Search..." size="40">
                            <button type="submit">
                                <i class="fas fa-search"></i>
                            </button>
                        </form>
                    </div>
        
                    <!-- Login/Logout Icon -->
                    <div class="login">
                        $login_logout_html
                    </div>
        
                    <!-- Shopping Cart Icon -->
                    <div class="cart">
                        <a href="?route=cart">
                            <i class="fas fa-shopping-cart"></i>
                            <span>$num_items_in_cart</span>
                        </a>
                    </div>
                </div>
            </div> 
        </header>
        $this->html_page_content
        HTML;

        $this->html_page_output .= $html_output; // Append content to full page
    }

    // Private function to create the footer and close the HTML
    private function createWebPageFooter()
    {
        $html_output = <<< HTML
                    <footer class="footer">
                        <div>
                            <p>Royal Silk Leicester</p>
                        </div>
                    </footer>
                    <script>
                        // Toggle mobile burger menu
                        const burger = document.getElementById('burger');
                        const nav = document.getElementById('mainNav');

                        burger.addEventListener('click', () => {
                            burger.classList.toggle('active');
                            nav.classList.toggle('active');
                        });
                    </script>
                </body>
            </html>
        HTML;

        $this->html_page_output .= $html_output; // Append footer
    }
}