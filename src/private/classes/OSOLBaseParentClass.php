<?php
/** 
 *  \namespace OSOLUtils 
 *  \brief     Parent name space of all sub namespaces of this project.
 *  \details   This namespace is the root namespace.\n
 * This documentation is written in OSOLUtils::Helpers::OSOLBaseParentClass under *namespace* tag\n
 * And will be shown in Main Project &gt;&gt; Namespaces &gt;&gt; Namespaces List &gt;&gt; thisNamespacename 
 *  \author    Sreekanth Dayanand
 *  \author    [Outsource Online Internet Solutions](https://www.outsource-online.net)
 *  \copyright GNU Public License. 
 */

/*! 
 *	\namespace OSOLUtils::Helpers
 *  \brief	Holds all classes &amp; subclasses for paginator.
 *  \details   This namespace is the namespace holding important classes (OSOLPageNav &amp; OSOLMySQL) of this project.\n
 * This documentation is written in OSOLUtils::Helpers::OSOLBaseParentClass under *namespace* tag\n
 * And will be shown in Main Project &gt;&gt; Namespaces &gt;&gt; &gt;&gt; Namespaces List &gt;&gt; thisNamespacename 
 *  \author    Sreekanth Dayanand
 *  \author    [Outsource Online Internet Solutions](https://www.outsource-online.net)
 *  \copyright GNU Public License.
 */
/*!
 \class OSOLUtils::Helpers::OSOLBaseParentClass
 \brief Parent class to initiate and return singleton instances for any subclass.\n
 \details  This documentation is written in OSOLUtils::Helpers::OSOLBaseParentClass under *class* tag\n
  And will be shown in Main Project &gt;&gt; Data Structures &gt;&gt; Nampespace &gt;&gt; thisClassName
 \details   Which ever class needs to be made singleton may subclass it\n
 \par Usage:
 ```
 //assuming class OSOLPageNav extends OSOLBaseParentClass
 $pageNavInst = OSOLPageNav::get();
 ```
 *  \author    Sreekanth Dayanand
 *  \author    [Outsource Online Internet Solutions](https://www.outsource-online.net)
 *  \version   0.0.1
 *  \date      2022-2032
 *  \pre 
 1. PHP 7+ is required
 2. If not autoloaded, Class Files must be explicitly included
 
 *  \bug       No bugs found till July,2022.
 *  \warning   Improper use can crash your application
 *  \copyright GNU Public License.
*/
namespace OSOLUtils\Helpers;
class OSOLBaseParentClass
{
    protected static $instances =  array();// to solve  the issue https://stackoverflow.com/questions/17632848/php-sub-class-static-inheritance-children-share-static-variables
    
	/**
    * @brief Returns Singleton instance
    * @param none 
	* no input parameter
    * @return ClassInstance 
	* @warning 
	1. function __construct() of subclasses should be *protected*
	2. Never call Class::getInstance() in another class's constructor, that instance will be discarded from $instances array 
	* @details 
	* @par Detailed Description:
	* This method initiates & returns a singleton intstance of this class or any of its sub classes
    **/
    public static function getInstance()// Caution: never call Class::getInstance() in another class's constructor
    {

        //https://www.php.net/manual/en/reflectionclass.newinstancewithoutconstructor.php &
        //https://refactoring.guru/design-patterns/singleton/php/example
        $ref  = new \ReflectionClass( get_called_class() ) ;

        $reflectionProperty = new \ReflectionProperty(static::class, 'instances');
        $reflectionProperty->setAccessible(true);
        $instances =   $reflectionProperty->getValue();
        $intentedClass = static::class;
        //if (  $instances[static::class] == null)
        if (  !isset($instances[$intentedClass]))
        {

            // The magic.
			$arguments = func_get_args();
        	$numberOfArguments = func_num_args();
			
            $instances[$intentedClass] = new static(...$arguments /* $dbDetails */);

            $reflectionProperty->setValue(null/* null for static var */, $instances);
            
        }

        return $instances[$intentedClass] ;
    }//public static function getInstance()

}//class CoreParent
?>