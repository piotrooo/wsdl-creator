<?php
namespace WSDL\Builder;

use WSDL\Parser\Node;

class Parameter
{
    /**
     * @var Node
     */
    private $node;
    private $header;

    public function __construct(Node $node, $header = false)
    {
        $this->node = $node;
        $this->header = $header;
    }

    /**
     * @return Node
     */
    public function getNode()
    {
        return $this->node;
    }

    /**
     * @return boolean
     */
    public function isHeader()
    {
        return $this->header;
    }
}
