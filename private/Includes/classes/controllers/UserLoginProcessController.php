<?php
/**
 * UserLoginProcessController.php
 *
 * Controller for handling the user login process.
 *
 * Responsibilities:
 * - Validate and sanitize user login input
 * - Authenticate user credentials via UserLoginProcessModel
 * - Update user session if login is successful
 * - Render the login result page via UserLoginProcessView
 *
 * Notes:
 * - Authored primarily by Afnan Khalid.
 * - Follows the template/structure (Factory usage, DatabaseWrapper, createHtmlOutput logic) 
 *   provided by CF Ingrams’ lecture material for Web Application Development (CTEC2712_2023_603).
 * - Validates POST request input and manages routing if accessed incorrectly.
 *
 * @author: Afnan Khalid
 * References: 
 *   - CF Ingrams, De Montfort University (cfi@dmu.ac.uk) – Web Application Development (CTEC2712_2023_603) Lecture and Lab Materials
 * @package: Royal Silk Leicester
 */

class UserLoginProcessController extends ControllerAbstract
{
    /**
     * Handles the login process and sets the HTML output.
     *
     * Responsibilities:
     * - Verify request method is POST
     * - Validate user input
     * - Authenticate user using the model
     * - Generate the HTML output via the view
     */
    public function createHtmlOutput(): void
    {
        // Redirect if request method is not POST
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $router = Factory::buildObject('Router');
            $router->routing();
            $html_result = $router->getHtmlOutput();
            echo $html_result;
            exit;
        }

        $user_login_result = [];

        // Validate user input
        $validated_input = $this->validateUserDetails();

        // Process login if no validation errors
        if (!$validated_input['input-error']) {
            $user_login_result = $this->userLoginProcess($validated_input);
        }

        $user_login_result = array_merge($validated_input, $user_login_result);

        // Generate HTML output
        $this->html_output = $this->userLoginView($user_login_result);
    }

    /**
     * Validates user login details from POST data.
     *
     * @return array Cleaned and validated input including error flags
     */
    private function validateUserDetails(): array
    {
        $validate = Factory::buildObject('Validate');
        $database_handle = Factory::createDatabaseWrapper();
        $validate->setDatabaseHandle($database_handle);

        $tainted = $_POST;
        $cleaned = [];

        $cleaned['validated_email'] = $validate->validateEmailLogIn('user_email', $tainted);
        $cleaned['user_hashed_password'] = $validate->validatePasswordLogIn('user_password', $tainted, 50);
        $cleaned['input-error'] = $validate->hasErrors();
        $cleaned['validation-errors'] = $validate->getErrors();

        return $cleaned;
    }

    /**
     * Processes the user login via the model.
     *
     * @param array $validated_input
     * @return array Result of user login attempt
     */
    private function userLoginProcess($validated_input): array
    {
        $database_handle = Factory::createDatabaseWrapper();

        $model = Factory::buildObject('UserLoginProcessModel');
        $model->setDatabaseHandle($database_handle);
        $model->setValidatedInput($validated_input);
        $model->processUserLogin();
        $user_login_result = $model->getUserLoginResult();

        if ($user_login_result) {
            $model->updateUserSession($user_login_result);
        }
        return $user_login_result;
    }

    /**
     * Generates the HTML output for the login result page.
     *
     * @param array $user_login_result
     * @return string HTML content
     */
    private function userLoginView($user_login_result): string
    {
        $view = Factory::buildObject('UserLoginProcessView');

        $view->setUserDetailsResult($user_login_result);
        $view->createOutputPage();
        $html_output = $view->getHtmlOutput();

        return $html_output;
    }
}