<?php
/** 
 * @author Afnan Khalid
 * Reference: 
 *   - CF Ingrams, De Montfort University (cfi@dmu.ac.uk) – Web Application Development (CTEC2712_2023_603) Lecture and Lab Materials
 *   - Previous project: CryptoShow – reused structure
 *   - David Adams, "Shopping Cart System with PHP and MySQL", codeshack.io – logic adapted
 * 
 * @package Royal Silk Leicester
 */
class UserRegisterFormView extends WebPageTemplateView
{
    public function __construct()
    {
        parent::__construct();
    }

    public function __destruct(){}

    /**
     * Builds the registration form page
     */
    public function createRegisterForm()
    {
        $this->setPageTitle();
        $this->createPageBody();
        $this->createWebPage();
    }

    public function getHtmlOutput()
    {
        return $this->html_page_output;
    }

    /**
     * Sets the page title
     */
    private function setPageTitle()
    {
        $this->page_title = APP_NAME . ' Register a new User';
    }

    /**
     * Creates the registration form HTML
     */
    private function createPageBody()
    {
        $page_heading = 'Register a New User';
        $form_method = 'post';
        $form_action = APP_ROOT_PATH;

        $this->html_page_content = <<< HTMLFORM
        <section>
            <div class="wrapper-register">
                <div id="login-form">
                    <form method="$form_method" action="$form_action">
                        <h3>$page_heading:</h3>
                        
                        <div class="form-group">
                            <label for="new_user_name">First Name:</label>
                            <input type="text" name="new_user_name" placeholder="First Name (2–30 characters)">
                        </div>

                        <div class="form-group">
                            <label for="new_user_surname">Last Name:</label>
                            <input type="text" name="new_user_surname" placeholder="Last Name (2–30 characters)">
                        </div>

                        <div class="form-group">
                            <label for="new_user_email">Email: </label>
                            <input type="email" name="new_user_email" placeholder="Enter a valid email address">
                        </div>

                        <div class="form-group">
                            <label for="new_user_password">Password:</label>
                            <input type="password" name="new_user_password" placeholder="Password (8–50 characters)">
                        </div>

                        <div class="form-group">
                            <label for="new_user_address">Address:</label> 
                            <input type="text" name="new_user_address" placeholder="Your address">
                        </div>

                        <div class="form-group">
                            <label for="new_user_city">City:</label>
                            <input type="text" name="new_user_city" placeholder="City">
                        </div>

                        <div class="form-group">
                            <button name="route" value="process_new_user_details">Register</button>
                        </div>

                    </form>
                </div>
            </div>
        </section>
        HTMLFORM;
    }
}