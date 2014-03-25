<?php
/**
 * Style
 *
 * @author Piotr Olaszewski <piotroo89 [%] gmail dot com>
 */
namespace WSDL\XML\Styles;

interface Style
{
    public function bindingStyle();

    public function bindingUse();

    public function methodInput($parameters);

    public function methodOutput($returning);
}