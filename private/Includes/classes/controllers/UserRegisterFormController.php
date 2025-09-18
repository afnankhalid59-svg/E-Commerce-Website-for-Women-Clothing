<?php
/**
 * UserRegisterFormController.php
 *
 * Controller for rendering the user registration form.
 *
 * Notes:
 * - Authored by Afnan Khalid.
 * - Template and structure reused from CF Ingrams' lecture material (CTEC2712_2023_603).
 * - Logic adapted from previous project: CryptoShow.
 *
 * Responsibilities:
 * - Build the UserRegisterFormView
 * - Generate the HTML output for the registration form
 *
 * @author: Afnan Khalid
 * References: 
 *   - CF Ingrams, De Montfort University (cfi@dmu.ac.uk) – Web Application Development (CTEC2712_2023_603)
 *   - Previous project: CryptoShow – reused structure
 * @package: Royal Silk Leicester
 */

class UserRegisterFormController extends ControllerAbstract
{
    /**
     * Builds and sets the HTML output for the registration form.
     */
    public function createHtmlOutput(): void
    {
        $view = Factory::buildObject('UserRegisterFormView');
        $view->createRegisterForm();
        $this->html_output = $view->getHtmlOutput();
    }
}