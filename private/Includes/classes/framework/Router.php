<?php
/**
 * Router.php
 *
 * Simple front controller in PHP that handles routing, controller execution, and HTML output generation.
 *
 * It's doing the job of:
 * Determining what action (or route) was requested
 * Validating that route
 * Instantiating the appropriate controller
 * Processing the controller's output
 *
 * @author Afnan Khalid
 * @References 
 *   - CF Ingrams, De Montfort University (cfi@dmu.ac.uk) – Web Application Development (CTEC2712_2023_603) Lecture and Lab Materials
 *   - Previous project: CryptoShow – reused structure
 * @package Royal Silk Leicester
 */
class Router
{
    private $html_output;

    public function __construct()
    {
        $this->html_output = '';
    }

    public function __destruct(){}

    /**
     *This is the main method that runs the routing logic:
     *
     * Gets the requested route from POST (defaults to 'index')
     * Validates the route
     * Selects and executes the corresponding controller
     * Processes the HTML output
     *
     * @return void
     */
    public function routing()
    {
        $html_output = '';

        $selected_route = $this->setRouteName();

        try {
            // Use Validate class with exception handling
            $validate = Factory::buildObject('Validate');
            $validate->validateRoute($selected_route);

            // If no exception, route is valid — proceed to controller
            $html_output = $this->selectController($selected_route);
        } catch (ValidationException $e) {
            // else fallback to 'index' controller:
            $html_output = $this->selectController('index');
        }

        $this->html_output = $html_output;
    }

    /**
     * Read the name of the selected route from the magic global POST array and overwrite the default if necessary
     * Checks if $_POST['route'] is set if not check if $_GET['route'] is set. If not, defaults to 'index'.
     *
     * @return mixed|string
     */
    private function setRouteName()
    {
        $selected_route = 'index';
        if (isset($_POST['route'])) {// It checks if there’s a POST variable called 'route'. If $_POST['route'] exists, it uses its value.
            $selected_route = $_POST['route'];
        } elseif (isset($_GET['route'])) {
            $selected_route = $_GET['route'];
        }
        return $selected_route;
    }


    /**
     * This method maps the selected route name to the corresponding controller.
     *
     * It uses a switch statement to match routes to controller class names.
     * Each case creates an instance of a controller class via the Factory
     * Calls the controller’s createHtmlOutput() method to build the HTML output.
     * Then calls getHtmlOutput() on the controller to retrieve the HTML as a string.
     * Returns the HTML string produced by the selected controller.
     * If the route is unknown, it falls back to the 'index' route and uses the IndexController.
     *
     * @param string $selected_route
     * @return mixed|string
     */
    public function selectController($selected_route)
    {   

        switch ($selected_route)
        {
            case 'user_register':
                $controller = Factory::buildObject('UserRegisterFormController');
                break;
            case 'process_new_user_details':
                $controller = Factory::buildObject('UserRegisterProcessController');
                break;
            case 'user_login':
                $controller = Factory::buildObject('UserLoginFormController');
                break;
            case 'process_login':
                $controller = Factory::buildObject('UserLoginProcessController');
                break;
            case 'user_logout':
                $controller = Factory::buildObject('UserLogoutProcessController');
                break;
            case 'list_users':
                $controller = Factory::buildObject('ListUsersController');
                break;
            case 'search':
                $controller = Factory::buildObject('SearchController');
                break;
            case 'products':
                $controller = Factory::buildObject('ProductsViewController');
                break;
            case 'product':
                $controller = Factory::buildObject('ProductController');
                break;
            case 'cart':
                $controller = Factory::buildObject('CartController');
                break;
            case 'index':
                $controller = Factory::buildObject('IndexController');
                break;
            default:
                $controller = Factory::buildObject('IndexController');
                break;
            }
        $controller->createHtmlOutput();
        $html_output = $controller->getHtmlOutput();
        return $html_output;
    }


    /**
     * This public getter method returns the final processed HTML output stored in the private property $html_output.
     *
     * Used after the routing process completes, to output the HTML to the browser.
     * Allows external scripts to get the rendered HTML and echo it.
     *
     *
     * @return string
     */
    public function getHtmlOutput()
    {
        return $this->html_output;
    }
}
