<?php
namespace WSDL\Builder;

class ParametersBuilder
{
    public function appendParameter($node, $header)
    {
        $this->parameters[] = new Parameter($node, $header);
    }

    public static function instance()
    {
        return new self();
    }
}
