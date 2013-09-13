PHP WSDL Creator
================

Introduction
------------

WSDL creator allows generating WSDL documents based on PHP classes using annotations and reflection mechanism. This generator also give possibilty to generating overview methods and parameters with SOAP examples used in WSDL.

Configuration
-------------

To start working with creator you must create new `WSDLCreator` object and define for him:
* class to generate `WSDL`
* `SOAP` server location
* documnet namespace. 

```php
$wsdl = new WSDL\WSDLCreator('ClassName', 'http://localhost/wsdl-creator/ClassName.php');
$wsdl->setNamespace("http://foo.bar/");
```

`SOAP` server must be created in location specified in `WSDLCreator`.
```php
$server = new SoapServer(null, array(
    'uri' => 'http://localhost/wsdl-creator/ClassName.php'
));
$server->setClass('ClassName');
$server->handle();
```

To render `XML` use method `renderWSDL`. To properly load generator classes use composer loader which is in `vendor/autoload.php`.

Full configutaion listing:
```php
require_once 'vendor/autoload.php';

use WSDL\WSDLCreator;

if (isset($_GET['wsdl'])) {
    $wsdl = new WSDL\WSDLCreator('ClassName', 'http://localhost/wsdl-creator/ClassName.php');
    $wsdl->setNamespace("http://foo.bar/");
    $wsdl->renderWSDL();
    exit;
}

$server = new SoapServer(null, array(
    'uri' => 'http://localhost/wsdl-creator/ClassName.php'
));
$server->setClass('ClassName');
$server->handle();
```

Now if we try call address `http://localhost/wsdl-creator/ClassName.php?wsdl` you recive `WSDL` document.

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

---

###Example:
[SimpleTypeExample](examples/SimpleExampleSoapServer.php)

---

###Annotations:

- @desc Method description
- @param type $varialbe_name
- @return type $return

Wrapper type
------------

Wrapper types are user defined clases which you can generate `WSDL` complex types. Simplest example:

###Usage:
```
/**
* @param wrapper $user @className=User
* @return string $nameWithAge
*/
```

You must define class `User` with public fields and doc comments which contains field type as example:

```php
class User
{
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

This mechanism use [reflection](http://php.net/manual/en/book.reflection.php), i.e. `User` class must be visible to the generated class - possible use [namespaces](http://php.net/manual/en/language.namespaces.php) (`\Namespace\To\User`).

---

You can define arrays of wrappers:

###Usage:
```
/**
* @return wrapper[] $users @className=User
*/
```

This annotation will generate array of users.

---

###Example:
[WrapperTypeExample](examples/WrapperExampleSoapServer.php)

---

###Annotations

- @desc Method description
- @param wrapper[] @className=ClassName
- @return wrapper @className=\Namespace\To\ClassName

Object type
-----------
