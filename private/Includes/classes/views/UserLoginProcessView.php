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
class UserLoginProcessView extends WebPageTemplateView
{
    private $authenticate_user_results; // Stores the authentication outcome
    private $output_content;            // HTML content for output

    public function __construct()
    {
        parent::__construct();
        $this->authenticate_user_results = [];
        $this->output_content = '';
    }

    public function __destruct(){}

    /**
     * Main method to generate the output page.
     * Sets the page title, generates messages, page body, and builds the final HTML page.
     */
    public function createOutputPage()
    {
        $this->setPageTitle();
        $this->createAppropriateOutputMessage();
        $this->createPageBody();
        $this->createWebPage();
    }

    public function getHtmlOutput()
    {
        return $this->html_page_output;
    }

    /**
     * Setter for authentication results.
     */
    public function setUserDetailsResult($authenticate_user_results)
    {
        $this->authenticate_user_results = $authenticate_user_results;
    }

    /**
     * Sets the page title.
     */
    private function setPageTitle()
    {
        $this->page_title = APP_NAME . ': Login User';
    }

    /**
     * Determines which output message to display:
     * - Success message if login succeeds
     * - Error message if login fails
     * - Fallback message if something went wrong
     */
    private function createAppropriateOutputMessage()
    {
        $output_content = '';

        if (isset($this->authenticate_user_results['input-error'])) {
            if ($this->authenticate_user_results['input-error'] === false) {
                if (isset($this->authenticate_user_results['authenticate-user-result'])) {
                    if ($this->authenticate_user_results['authenticate-user-result']) {
                        $output_content .= $this->createSuccessMessage();
                    } else {
                        $output_content .= $this->createErrorMessage();
                    }
                }
            } else {
                $output_content .= $this->createErrorMessage();
            }
        } else {
            $output_content .= 'Something went wrong, please try again';
        }

        $this->output_content = $output_content;
    }

    /**
     * Builds the main page body with heading and the output content.
     */
    private function createPageBody()
    {
        $page_heading = 'User Login';
        $output_content = $this->output_content;

        $this->html_page_content = <<< HTMLFORM
        <h2>$page_heading</h2>
        {$output_content}
        HTMLFORM;
    }

    /**
     * Generates the login form with error messages if validation fails.
     */
    private function createErrorMessage()
    {
        $form_method = 'post';
        $form_action = APP_ROOT_PATH;
        $page_heading = 'Login';
        $error_messages = '';

        // Include any validation errors
        if (isset($this->authenticate_user_results['validation-errors'])) {
            foreach ($this->authenticate_user_results['validation-errors'] as $field => $message) {
                $error_messages .= "<p class=\"error\">{$message}</p>\n";
            }
        }

        $page_content = <<< HTMLOUTPUT
        <div class="form-errors">
            <p>Please fix the errors below:</p>
            {$error_messages}
        </div>

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
        HTMLOUTPUT;

        return $page_content;
    }

    /**
     * Generates a success message after login
     */
    private function createSuccessMessage()
    {   
        $index = APP_ROOT_PATH;
        $user_name = $this->authenticate_user_results['user_name'];
        $page_content = <<< HTMLOUTPUT
        <p>Welcome back $user_name.</p>
        <p>You have successfully logged in.</p>
        <p><a href="$index"><button>Home</button></a></p>
        HTMLOUTPUT;

        return $page_content;
    }
}