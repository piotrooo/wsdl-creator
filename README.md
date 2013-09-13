PHP WSDL Creator
================

Introduction
------------

WSDL creator allows generating WSDL documents based on PHP classes using annotations and reflection mechanism. This generator also give possibilty to generating overview methods and parameters with SOAP examples used in WSDL.

Simple type
-----------

Simple types are described here: <http://infohost.nmt.edu/tcc/help/pubs/rnc/xsd.html#xsd-types>

###Usage:
```
/**
* @param string $name
* @param int $age
* @return string $nameWithAge
*/
```

So you type `@param` next type one of simple types (`string`) after name of variable (`$name`).

---

You can also use `arrays` of the simple types:

###Usage:
```
/**
* @param string[] $names
* @return string $name
*/
```

In input parameter now you must define what type of array you pass (`string[]`).

###Example:
[SimpleTypeExample](examples/SimpleExampleSoapServer.php)

Wrapper type
------------

Object type
-----------

Basic
-----

WSDL is PHP creator using PHPdoc (annotations, reflection).

Usage as doc for method:

	/**
	* @desc method description
	* @param object $object1 @string=name @int=id
	* @param int $number
	* @return object $return @string=new_name @int=new_id
	*/

Advanced
--------

You can also use reflection mechanism.

	/**
	* @param wrapper $wrapper @className=UserWrapper
	* @return object $return @string=name @int=age
	*/

This give you possibility to wrapping parameters by custom class.

UserWrapper class use in web method parameter:

```php
class UserWrapper
{
    /**
     * @type int
     */
    public $id;
    /**
     * @type string
     */
    public $name;
    /**
     * @type int
     */
    public $age;
}
```

----

Supporting annotations:

	anotation   |   desc                        |   example
	---------------------------------------------------------------------------------
	@desc       |   method description          |   @desc Sample description method
	@param      |   parameter type definition   |   @param [type] $param1
	@return     |   return type definiotion     |   @return [type] $return
	
Parameters type are defined here: <http://infohost.nmt.edu/tcc/help/pubs/rnc/xsd.html#xsd-types> or if you use complex types type `object` as `[type]`. If use `wrapper` type this generate parameter wrapped by custom class.

Live example in file `ExampleSoapServer.php`.
