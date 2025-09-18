<?php

/**
 * UserLoginFormView.php
 *
 * Handles rendering of the user login form.
 * Extends the WebPageTemplateView to include standard header/footer.
 * @author Afnan Khalid
 * Reference: 
 *   - CF Ingrams, De Montfort University (cfi@dmu.ac.uk) – Web Application Development (CTEC2712_2023_603) Lecture and Lab Materials
 *   - Previous project: CryptoShow – reused structure
 *   - David Adams, "Shopping Cart System with PHP and MySQL", codeshack.io – logic adapted
 * 
 * @package Royal Silk Leicester
*/
class UserLoginFormView extends WebPageTemplateView
{
    /**
     * Constructor
     * Calls parent constructor to initialize template defaults
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Destructor
     * Currently does nothing but included for completeness
     */
    public function __destruct(){}

    /**
     * Create the login form page
     *
     * Builds page title, body content, and then the full HTML page.
     */
    public function createLoginForm()
    {
        $this->setPageTitle();   // Sets page title
        $this->createPageBody(); // Builds the form HTML
        $this->createWebPage();  // Combines header, content, and footer
    }

    /**
     * Return the full HTML page (with header/footer)
     *
     * @return string
     */
    public function getHtmlOutput()
    {
        return $this->html_page_output;
    }

    /**
     * Set page title for login form
     * Uses APP_NAME constant to prepend app branding
     */
    private function setPageTitle()
    {
        $this->page_title = APP_NAME . ' Login';
    }

    /**
     * Build the HTML content of the login form
     *
     * Uses heredoc syntax for clean multi-line HTML embedding.
     */
    private function createPageBody()
    {
        $page_heading = 'Login';
        $form_method = 'post';
        $form_action = APP_ROOT_PATH; // Action is the root path, controller decides routing

        // HTML form structure
        $this->html_page_content = <<< HTMLFORM
        <section>
        <div class="wrapper">
            <div id="login-form">
                <form method="$form_method" action="$form_action" class="login-form">
                <h3>$page_heading</h3>

                <div class="form-group">
                    <label for="user_email">Email Address:</label>
                    <input 
                        type="email" 
                        id="user_email" 
                        name="user_email" 
                        placeholder="Enter your email address" 
                    >
                </div>

                <div class="form-group">
                    <label for="user_password">Password:</label>
                    <input 
                        type="password" 
                        id="user_password" 
                        name="user_password" 
                        placeholder="Enter your password" 
                    >
                </div>

                <div class="form-group">
                    <button type="submit" name="route" value="process_login">Login</button>
                </div>

                <p id="signup-msg">Don't have an account? <a href="?route=user_register">Register</a></p>
                </form>
            </div>
        </div>
        </section>
        HTMLFORM;
    }
}