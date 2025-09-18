<?php
/**
 * autoload.php
 * automatically load class files from a set of directories under a defined CLASS_PATH
 *
 * @author Afnan Khalid
 * @Reference De Montfort University, CF Ingrams - cfi@dmu.ac.uk & all labs.
 *
 * @package RoyalSilkLeicester
 */

spl_autoload_register(function ($class_name)
{
    $file_path_and_name = '';
    $directories = [];

    $file_name = $class_name . '.php';

    //Assumes all class files are stored in subdirectories of a root CLASS_PATH.
    $directories = array_diff(scandir(CLASS_PATH), array('..', '.'));

    // Loops through each subdirectory and checks for the class file named $class_name.php.
    foreach ($directories as $directory)
    {
        $file_path_and_name = CLASS_PATH . $directory . DIRSEP . $file_name;

        if (file_exists($file_path_and_name))
        {
            require_once $file_path_and_name;
            break;
        }
    }
});
