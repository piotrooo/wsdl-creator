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
	@param      |   parameter type definition   |   @param [param_type] $param1
	@return     |   return type definiotion     |   @return [return_type]
	
	[param_type] => string, int, integer, object @type1=name1 @type2=name2
	[return_type] => string, int, integer, object

Live example in file `ExampleSoapServer.php`.
