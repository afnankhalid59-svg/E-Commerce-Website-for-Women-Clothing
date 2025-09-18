<?php
/**
 * UserRegisterProcessController.php
 *
 * Controller for processing user registration submissions.
 *
 * Notes:
 * - Authored by Afnan Khalid.
 * - Template and structure reused from CF Ingrams' lecture material (CTEC2712_2023_603).
 * - Logic adapted from previous project: CryptoShow.
 *
 * Responsibilities:
 * - Validate and sanitize user registration input
 * - Process and store new user details via UserRegisterProcessModel
 * - Build UserRegisterProcessView with results and generate HTML output
 *
 * @author: Afnan Khalid
 * References: 
 *   - CF Ingrams, De Montfort University (cfi@dmu.ac.uk) – Web Application Development (CTEC2712_2023_603)
 *   - Previous project: CryptoShow – reused structure
 * @package: Royal Silk Leicester
 */
class UserRegisterProcessController extends ControllerAbstract
{       
    /**
     * Handles the registration form submission and sets HTML output.
     */
    public function createHtmlOutput(): void
    {
        // Redirect if request method is not POST
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header("Location: ../?route=index");
            exit;
        }

        $register_new_user_result = [];
        $validated_input = $this->validateUserDetails();

        if (!$validated_input['input-error']) {
            $register_new_user_result = $this->userRegisterProcess($validated_input);
        }

        $register_new_user_results = array_merge($validated_input, $register_new_user_result);
        $this->html_output = $this->userRegisterView($register_new_user_results);
    }
    
    /**
     * Validates and sanitizes user input for registration.
     */
    private function validateUserDetails(): array
    {
        $validate = Factory::buildObject('Validate');
        $database_handle = Factory::createDatabaseWrapper();
        $validate->setDatabaseHandle($database_handle);

        $tainted = $_POST;
        $cleaned = [];
        $cleaned['validated_name'] = $validate->validateString('new_user_name', $tainted, 2, 30);
        $cleaned['validated_surname'] = $validate->validateString('new_user_surname', $tainted, 2, 30);
        $cleaned['validated_email'] = $validate->validateEmail('new_user_email', $tainted);
        $cleaned['user_hashed_password'] = $validate->validatePassword('new_user_password', $tainted, 50);
        $cleaned['validated_address'] = $validate->validateAddress('new_user_address', $tainted);
        $cleaned['validated_city'] = $validate->validateCity('new_user_city', $tainted);
        $cleaned['input-error'] = $validate->hasErrors();
        $cleaned['validation-errors'] = $validate->getErrors();
        $cleaned['previous-input'] = $tainted;

        return $cleaned;
    }

    /**
     * Processes registration by storing new user details in the database.
     */
    private function userRegisterProcess($validated_input): array
    {
        $database_handle = Factory::createDatabaseWrapper();
        $model = Factory::buildObject('UserRegisterProcessModel');
        $model->setDatabaseHandle($database_handle);
        $model->setValidatedInput($validated_input);
        $model->storeNewUserDetails();
        return $model->getStoreNewUserDetailsResult();
    }

    /**
     * Builds the view for registration results and generates HTML output.
     */
    private function userRegisterView($store_new_user_details_results): string
    {
        $view = Factory::buildObject('UserRegisterProcessView');
        $view->setStoreNewUserDetailsResult($store_new_user_details_results);
        $view->createOutputPage();
        return $view->getHtmlOutput();
    }
}