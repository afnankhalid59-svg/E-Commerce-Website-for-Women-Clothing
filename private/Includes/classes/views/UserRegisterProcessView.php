<?php
/** This class handles the view for the result of user registration.
 *  It extends a generic WebPageTemplateView, which provides common page rendering functionality.
 * 
 * @author Afnan Khalid
 * Reference: 
 *   - CF Ingrams, De Montfort University (cfi@dmu.ac.uk) – Web Application Development (CTEC2712_2023_603) Lecture and Lab Materials
 *   - Previous project: CryptoShow – reused structure
 *   - David Adams, "Shopping Cart System with PHP and MySQL", codeshack.io – logic adapted
 * 
 * @package Royal Silk Leicester
 */
class UserRegisterProcessView extends WebPageTemplateView
{
    // Stores results from attempting to store new user details
    private $store_new_user_details_results;

    // Stores the HTML content to be displayed on the page
    private $output_content;

    // Constructor: initializes properties
    public function __construct()
    {
        parent::__construct(); // Call parent constructor
        $this->store_new_user_details_results = []; // Initialize results array
        $this->output_content = ''; // Initialize output content
    }

    // Destructor: empty (could be used for cleanup if needed)
    public function __destruct(){}

    // Main method to create the output page
    public function createOutputPage()
    {
        $this->setPageTitle(); // Set the page title
        $this->createAppropriateOutputMessage(); // Decide whether to show success or error message
        $this->createPageBody(); // Build the main page content
        $this->createWebPage(); // Render the full HTML page
    }

    // Returns the final HTML output for this page
    public function getHtmlOutput()
    {
        return $this->html_page_output;
    }

    // Sets the results from storing new user details
    public function setStoreNewUserDetailsResult($store_new_user_details_results)
    {
        $this->store_new_user_details_results = $store_new_user_details_results;
    }

    // Sets the page title dynamically
    private function setPageTitle()
    {
        $this->page_title = APP_NAME . ' Register a new User';
    }

    // Determines the message to display based on the result of storing user details
    private function createAppropriateOutputMessage()
    {
        $output_content = '';
        
        // Check if there was an input error in the registration process
        if (isset($this->store_new_user_details_results['input-error']))
        {
            if ($this->store_new_user_details_results['input-error'])
            {
                // Show error message if input validation failed
                $output_content .= $this->createErrorMessage();
            }
            else
            {
                // Show success message if registration succeeded
                $output_content .= $this->createSuccessMessage();
            }
        }
        else
        {
            // Fallback message if something unexpected happened
            $output_content .= 'Ooops - something appears to have gone wrong.  Please try again later.';
        }

        $this->output_content = $output_content;
    }

    // Builds the page body content using the output message
    private function createPageBody()
    {
        $page_heading = 'Register a New User';
        $output_content = $this->output_content;

        // Use heredoc to generate the page HTML
        $this->html_page_content = <<< HTMLFORM
        <h2>$page_heading</h2>
        {$output_content}
        HTMLFORM;
    }

    // Creates the error message HTML and re-populates the form with previous input
    private function createErrorMessage()
    {
        $form_method = 'post';
        $form_action = APP_ROOT_PATH;

        $error_messages = '';
        // Retrieve previous user input to pre-fill the form
        $previous_input = $this->store_new_user_details_results['previous-input'] ?? [];

        // Display validation error messages, if any
        if (isset($this->store_new_user_details_results['validation-errors'])) {
            foreach ($this->store_new_user_details_results['validation-errors'] as $field => $message) {
                $error_messages .= "<p class=\"error\">{$message}</p>\n";
            }
        }

        // Escape previous input values to avoid XSS attacks
        $name = htmlspecialchars($previous_input['new_user_name'] ?? '');
        $surname = htmlspecialchars($previous_input['new_user_surname'] ?? '');
        $email = htmlspecialchars($previous_input['new_user_email'] ?? '');
        $address = htmlspecialchars($previous_input['new_user_address'] ?? '');
        $city = htmlspecialchars($previous_input['new_user_city'] ?? '');

        // Build the HTML form with error messages and pre-filled data
        $page_content = <<< HTMLOUTPUT
        <div class="form-errors">
            <p>Please fix the errors below:</p>
            {$error_messages}
        </div>

        <section>
            <div class="wrapper-register">
                <div id="login-form">
                    <form method="$form_method" action="$form_action">
                        <h3>Register a New User:</h3>
                        
                        <div class="form-group">
                            <label for="new_user_name">First Name:</label>
                            <input type="text" name="new_user_name" value="{$name}" placeholder="First Name (2–30 characters)">
                        </div>

                        <div class="form-group">
                            <label for="new_user_surname">Last Name:</label>
                            <input type="text" name="new_user_surname"value="{$surname}" placeholder=" Last Name (2–30 characters)">
                        </div>

                        <div class="form-group">
                            <label for="new_user_email">Email: </label>
                            <input type="email" name="new_user_email" value="{$email}" placeholder="Enter a valid email address">
                        </div>

                        <div class="form-group">
                            <label for="new_user_password">Password:</label>
                            <input type="password" name="new_user_password" placeholder="Password (8–50 characters)">
                        </div>

                        <div class="form-group">
                            <label for="new_user_address">Address:</label> 
                            <input type="text" name="new_user_address" value="{$address}" placeholder="Your address">
                        </div>

                        <div class="form-group">
                            <label for="new_user_city">City:</label>
                            <input type="text" name="new_user_city" value="{$city}" placeholder="City">
                        </div>

                        <div class="form-group">
                        <button name="route" value="process_new_user_details">Register</button>
                        </div>

                    </form>
                </div>
            </div>
        </section>
        HTMLOUTPUT;

        return $page_content;
    }

    // Creates the success message HTML after a user registers successfully
    private function createSuccessMessage()
    {
        $page_content = <<< HTMLOUTPUT
        <div class="success-message">
            <p>You have been successfully registered.</p>
            <p><a href="?route=user_login">Click here to log in.</a></p>
        </div>
        HTMLOUTPUT;

        return $page_content;
    }
}