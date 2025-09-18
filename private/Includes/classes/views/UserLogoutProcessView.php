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
class UserLogoutProcessView extends WebPageTemplateView
{
    private array $user_logout_results; // Stores the logout outcome
    private $output_content;            // HTML content for output

    public function __construct()
    {
        parent::__construct();
        $this->user_logout_results = [];
        $this->output_content = '';
    }

    public function __destruct(){}

    /**
     * Main method to generate the output page
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
     * Setter for logout result array
     */
    public function setUserLogoutResults(array $user_logout_result)
    {
        $this->user_logout_results = $user_logout_result;
    }

    /**
     * Sets page title
     */
    private function setPageTitle()
    {
        $this->page_title = APP_NAME . ': User Logout';
    }

    /**
     * Determines output message based on logout results
     */
    private function createAppropriateOutputMessage()
    {
        $output_content = '';

        if (isset($this->user_logout_results['logout-result'])) {
            if ($this->user_logout_results['logout-result']) {
                $output_content .= $this->createSuccessMessage();
            } else {
                $output_content .= $this->createErrorMessage();
            }
        } else {
            $output_content .= 'Ooops - something appears to have gone wrong. Please try again later.';
        }

        $this->output_content = $output_content;
    }

    /**
     * Builds page body
     */
    private function createPageBody()
    {
        $page_heading = 'User Logout';

        $this->html_page_content = <<< HTMLFORM
        <h2>$page_heading</h2>
        $this->output_content
        HTMLFORM;
    }

    /**
     * HTML for logout error message
     */
    private function createErrorMessage()
    {
        $form_method = 'post';
        $form_action = APP_ROOT_PATH;

        $page_content = <<< HTMLOUTPUT
        <p>Ooops - there appears to have been an error in logging you out - please try again.</p>
        <form method="$form_method" action="$form_action">
        <p><button name="route" value="user_logout">Try again</button></p>
        </form>
        HTMLOUTPUT;

        return $page_content;
    }

    /**
     * HTML for logout success message
     */
    private function createSuccessMessage()
    {
        $index = APP_ROOT_PATH;

        $page_content = <<< HTMLOUTPUT
        <p>You have been successfully logged out.</p>
        <p><a href="$index"><button>Home</button></a></p>
        HTMLOUTPUT;

        return $page_content;
    }
}