<?php
/**
* @mainpage OSOL Paginator
This is a simple class that helps creating pagination links while showing master details pages.
 
 This is the `index.php` page for the project. \n 
 Since **`mainpage`*** tag is used, comments in this page will be displayed on the top of home page of **Doxygen Documentation**\n
 This project tries to explain documenting PHP projects with doxygen tags for
 
@date 20th July 2022
@copyright {This project is released under the GNU Public License.}
@author Sreekanth Dayanand

@details This details is added with `details` tag and will be displayed below all the other tags

@details This details is added with additional `details` tag, to demonstrate use of paragraphs
   
\par Paragraph title with `par` tag
first line adding line break 1\n
second line adding line break\n

<hr />
\par Paragraph title with par tag
```
<?php
//Some code / sketch
echo "Hello world";
?>
```

Code displayed with markdown tag.
For this 'EXAMPLE_PATH' (in Expert Tab &gt;&gt; Topic &gt;&gt; Input must be set to the folder where example codee files are saved
\par Including other php files with include tag
@include "examples/example1.php"
@remarks
Licence is added with ALIAS.
https://stackoverflow.com/questions/12353409/doxygen-and-license-copyright-informations

@remarks
[Read me file should be like](https://stackoverflow.com/questions/28938063/customize-treeview-in-doxygen)


Any other information that needs to be shown at the bottom of main page in documentation may be placed at the bottom in `markdown language` w/o tags


 */
 /**
* @file index.php
* @brief Front to the OSOL Paginator demo project. 
* @details Starting point of the project.\n
* This file bootstraps the operations of this project\n
* This documentation is show because *file* tag is used.\n
* This will appear under  Main Project &gt;&gt; Files &gt;&gt; File List &gt;&gt; thisFileName \n
* @warning without *file* tag, non class files are not documented\n
* Also no global variables will be documented
*
*/

/*-------------------------------CODE STARTS HERE ---------------------------------*/

/*! 
 *  \brief constant holding file path of 'private' folder.
 * @details classes , templates etc are ain private folder.\n
 For more security, private folder may be moved to a secured location.
 */
define('OTP_PRIVATE_FOLDER' , dirname(__FILE__).DIRECTORY_SEPARATOR.'private');

/*! \fn osolAutoLoadRegisterCalled() 
 *  \brief Dummy function to mention **spl_autoload_register** is called.
 *  \param function Function that maps called classes to appropriate source files.
 *  \exception std::fileNotFound No such file check the spelling of {$class}.
 *  \return void.
 */
 function osolAutoLoadRegisterCalled(){}
/**
* An example of a project-specific implementation.
*
* \brief After registering this autoload function with SPL, the following line
* would cause the function to attempt to load the \Foo\Bar\Baz\Qux class
* from /path/to/project/src/Baz/Qux.php:
*
*      new \Foo\Bar\Baz\Qux;
*
* @param string $class The fully-qualified class name.
* @return void
*/
if(!function_exists('version_compare') || version_compare(phpversion(), '5.1.0', '<'))die("Minimum version required for autoload is 5.1.0");
spl_autoload_register(function ($class) {
// project-specific namespace prefix
$prefix = 'OSOLUtils\\Helpers\\';    
// base directory for the namespace prefix
$base_dir = OTP_PRIVATE_FOLDER . DIRECTORY_SEPARATOR.'classes'.DIRECTORY_SEPARATOR;
//die($class . " " . str_replace($prefix,'',$class));
// does the class use the namespace prefix?
$len = strlen($prefix);
if (strncmp($prefix, $class, $len) !== 0) {
    // no, move to the next registered autoloader
    return;
}   
// get the relative class name
$relative_class = substr($class, $len);
// replace the namespace prefix with the base directory, replace namespace
// separators with directory separators in the relative class name, append
// with .php
$mappedFile = $base_dir . str_replace('\\', '/', $relative_class) . '.php';
try {
    if (file_exists($mappedFile)) {
        require $mappedFile;
    }
    else
    {
        //die('<p style="background:#f00">ERROR!!!!! file : '.$mappedFile. " does not exist to autoload for ".$class ."</p>");
        //throw new CustomException('ERROR!!!!! file : '.$mappedFile. " does not exist to autoload for ".$class );
        throw new Exception('ERROR!!!!! file : '.$mappedFile. " does not exist to autoload for ".$class );
    }
}
catch (Exception $e) {
  //display custom message
  $debug_trace = debug_backtrace();
  $fileAndLineno = "file : {$debug_trace[1]['file']} , Line #: {$debug_trace[1]['line']}";
  echo $e->getMessage() . $fileAndLineno;
}
});

/**
*  @brief Database configuration values
*  @details
    $DBConfig will be like
	 <pre>
	 array(
							'DB_USER' => "",
							'DB_PASS' => "",
							'DB_SERVER' => "",
							'DB_NAME' => "",
							'table_prefix' => "",
							'log_queries' => true| false,
							'query_log_type' => "file"|"echo",
							);
	 </pre>
*/
$dbConfig = array(
							'DB_USER' => "root",
							'DB_PASS' => "",
							'DB_SERVER' => "localhost",
							'DB_NAME' => "osol_test_paginator",
							'table_prefix' => "otp_",
							'log_queries' => true,
							'query_log_type' => "file"
							);
							
//$db = \OSOLUtils\Helpers\OSOLMySQL::getInstance();							
//$otpPageNav = \OSOLUtils\Helpers\PHPPaginator::getInstance();
							
require_once(OTP_PRIVATE_FOLDER . "/templates/paginator.phtml")							
?>