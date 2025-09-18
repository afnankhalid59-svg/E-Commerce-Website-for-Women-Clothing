<?php
/**
 * UserLoginFormController.php
 *
 * Controller for rendering the user login form page.
 *
 * Notes:
 * - Authored primarily by Afnan Khalid.
 * - Follows the template/structure (Factory usage, createHtmlOutput logic) 
 *   provided by CF Ingrams’ lecture material for Web Application Development (CTEC2712_2023_603).
 * - Simple and concise, no major changes were required.
 *
 * Responsibilities:
 * - Build the UserLoginFormView
 * - Generate the HTML output for the login form
 *
 * @author: Afnan Khalid
 * References: 
 *   - CF Ingrams, De Montfort University (cfi@dmu.ac.uk) – Web Application Development (CTEC2712_2023_603) Lecture and Lab Materials
 *   - Previous project: CryptoShow – reused structure
 * @package: Royal Silk Leicester
 */

class UserLoginFormController extends ControllerAbstract
{
    /**
     * Builds and sets the HTML output for the user login form.
     *
     * Responsibilities:
     * - Instantiate UserLoginFormView via the Factory
     * - Create the login form in the view
     * - Retrieve the HTML output and store it in $this->html_output
     */
    public function createHtmlOutput(): void
    {
        // Instantiate the login form view using the Factory
        $view = Factory::buildObject('UserLoginFormView');

        // Generate the login form HTML content
        $view->createLoginForm();

        // Store the HTML output in the controller property
        $this->html_output = $view->getHtmlOutput();
    }
}