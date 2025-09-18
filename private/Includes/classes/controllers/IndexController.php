<?php
/**
 * IndexController.php
 *
 * Controller for rendering the index (home) page of the application.
 *
 * Notes:
 * - Original code by CF Ingrams for Web Application Development (CTEC2712_2023_603) lecture material.
 * - Previously used in the "CryptoShow" project and reused here without modifications.
 * - Simple and concise, no changes were made.
 *
 * Responsibilities:
 * - Build the IndexView
 * - Generate the HTML output for the index page
 *
 * @author: CF Ingrams
 * Reference: 
 *   - CF Ingrams, De Montfort University (cfi@dmu.ac.uk) – Web Application Development (CTEC2712_2023_603) Lecture and Lab Materials
 *   - Previous project: CryptoShow – reused structure
 * 
 * @package: Royal Silk Leicester
 */

class IndexController extends ControllerAbstract
{
    /**
     * Builds and sets the HTML output for the index page.
     *
     * Responsibilities:
     * - Instantiate IndexView via the Factory
     * - Create the form in the view
     * - Retrieve the HTML output and store it in $this->html_output
     */
    public function createHtmlOutput(): void
    {
        // Instantiate the view using the Factory
        $view = Factory::buildObject('IndexView');

        // Generate the form and HTML content
        $view->createForm();

        // Retrieve HTML output from the view
        $index_html_output = $view->getHtmlOutput();

        // Store the HTML output in the controller property
        $this->html_output = $index_html_output;
    }
}