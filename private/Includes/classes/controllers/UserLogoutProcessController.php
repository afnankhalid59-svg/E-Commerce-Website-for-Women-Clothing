<?php
/**
 * UserLogoutProcessController.php
 *
 * Handles user logout requests and generates logout confirmation view.
 *
 * Notes:
 * - Authored by Afnan Khalid.
 * - Template and structure reused from CF Ingrams' lecture material (CTEC2712_2023_603).
 * - Logic adapted from previous project: CryptoShow.
 *
 * @author: Afnan Khalid
 * References: 
 *   - CF Ingrams, De Montfort University (cfi@dmu.ac.uk) – Web Application Development (CTEC2712_2023_603)
 *   - Previous project: CryptoShow – reused structure
 * @package: Royal Silk Leicester
 */

class UserLogoutProcessController extends ControllerAbstract
{
    /**
     * Executes logout process and generates HTML output.
     */
    public function createHtmlOutput(): void
    {
        $this->html_output = $this->userLogoutView($this->userLogoutProcess());
    }

    /**
     * Terminates the user session and returns result.
     *
     * @return array Logout result
     */
    private function userLogoutProcess(): array
    {
        $model = Factory::buildObject('UserLogoutProcessModel');
        $model->deleteSession();
        return $model->getUserLogoutResult();
    }

    /**
     * Generates HTML view for logout confirmation.
     *
     * @param array $user_logout_result Result from logout process
     * @return string HTML output
     */
    private function userLogoutView(array $user_logout_result): string
    {
        $view = Factory::buildObject('UserLogoutProcessView');
        $view->setUserLogoutResults($user_logout_result);
        $view->createOutputPage();
        return $view->getHtmlOutput();
    }
}