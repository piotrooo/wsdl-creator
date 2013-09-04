wsdl-creator
============

WSDL is PHP creator using PHPdoc (annotations).

Usage as doc for method:

	/**
	* @desc method description
	* @param object $object1 @string=name @int=id
	* @param int $number
	* @return object $return @string=new_name @int=new_id
	*/

Supporting annotations:

	anotation   |   desc                        |   example
	---------------------------------------------------------------------------------
	@desc       |   method description          |   @desc Sample description method
	@param      |   parameter type definition   |   @param [type] $param1
	@return     |   return type definiotion     |   @return [type] $return
	
Parameters type are defined here: <http://infohost.nmt.edu/tcc/help/pubs/rnc/xsd.html#xsd-types> or if you use complex types type `object` as `[type]`.

Live example in file `ExampleSoapServer.php`.
