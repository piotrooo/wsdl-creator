PHP WSDL Creator
================

[![Build Status](https://travis-ci.org/piotrooo/wsdl-creator.png?branch=master)](https://travis-ci.org/piotrooo/wsdl-creator)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/piotrooo/wsdl-creator/badges/quality-score.png)](https://scrutinizer-ci.com/g/piotrooo/wsdl-creator/)
[![Code Coverage](https://scrutinizer-ci.com/g/piotrooo/wsdl-creator/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/piotrooo/wsdl-creator/?branch=master)
[![Total Downloads](https://poser.pugx.org/piotrooo/wsdl-creator/downloads)](https://packagist.org/packages/piotrooo/wsdl-creator)
[![License](https://poser.pugx.org/piotrooo/wsdl-creator/license)](https://packagist.org/packages/piotrooo/wsdl-creator)
[![Gitter](https://badges.gitter.im/wsdl-creator/Lobby.svg)](https://gitter.im/wsdl-creator/Lobby?utm_source=badge&utm_medium=badge&utm_campaign=pr-badge)

Class annotations
=================

### @WebService

Parameters:

* `name` (`string "WebServiceAnnotations"`)
* `targetNamespace` (`string "http://foo.bar/webserviceannotations"`)
* `location` (`string "http://localhost/wsdl-creator/service.php"`)
* `ns` (`string "http://foo.bar/webserviceannotations/types"`)

### @BindingType

Parameters:

* `value` (`enum {"SOAP_11", "SOAP_12"}`)

### @SoapBinding

Parameters:

* `style` (`enum {"RPC", "DOCUMENT"}`)
* `use` (`enum {"LITERAL", "ENCODED"}`)
* `parameterStyle` (`enum {"BARE", "WRAPPED"}`)

Method annotations
==================

### @WebMethod

No parameters - mark method as a Web Service method

### @WebParam

* `param` (`string "string $userName"`) [look at the param examples section](#param-examples)
* `header` (`bool true|false`)

### @WebResult

* `param` (`string "string $uppercasedUserName"`) [look at the param examples section](#param-examples)

Param examples
==============

* `string $userName` - simple type
* `object $user { string $name int $age }` - complex type
* `int[] $numbers` - array of simple or complex types
