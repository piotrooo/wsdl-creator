<?php
/**
 * Style
 *
 * @author Piotr Olaszewski <piotroo89 [%] gmail dot com>
 */
namespace WSDL\XML\Styles;

use WSDL\Parser\MethodParser;

interface Style
{
    const RPC_LITERAL = 'rpc/literal';
    const RPC_ENCODED = 'rpc/encoded';
    const DOCUMENT_LITERAL_WRAPPED = 'document/literal wrapped';

    public function bindingStyle();

    public function bindingUse();

    public function methodInput(MethodParser $method);

    public function methodOutput(MethodParser $method);

    public function typeParameters(MethodParser $method);

    public function typeReturning(MethodParser $method);
}