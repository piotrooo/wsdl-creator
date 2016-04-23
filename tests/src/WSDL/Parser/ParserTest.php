<?php
use Ouzo\Tests\Assert;
use Ouzo\Utilities\Arrays;
use WSDL\Lexer\Tokenizer;
use WSDL\Parser\Node;
use WSDL\Parser\Parser;

class ParserTest extends PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function shouldParseSimple()
    {
        //given
        $tokens = $this->tokenize('int $age');
        $parser = new Parser($tokens);

        //when
        $nodes = $parser->S();

        //then
        Assert::thatArray($nodes)
            ->extracting('type', 'name', 'isArray', 'elements')
            ->containsExactly(
                array('int'),
                array('$age'),
                array(false),
                array(array())
            );
    }

    /**
     * @test
     */
    public function shouldParseSimpleArray()
    {
        //given
        $tokens = $this->tokenize('int[] $age');
        $parser = new Parser($tokens);

        //when
        $nodes = $parser->S();

        //then
        Assert::thatArray($nodes)
            ->extracting('type', 'name', 'isArray', 'elements')
            ->containsExactly(
                array('int'),
                array('$age'),
                array(true),
                array(array())
            );
    }

    /**
     * @test
     */
    public function shouldParseObject()
    {
        //given
        $tokens = $this->tokenize('object $user { string $name bool $isActive }');
        $parser = new Parser($tokens);

        //when
        $nodes = $parser->S();

        //then
        Assert::thatArray($nodes)
            ->extracting('type', 'name', 'isArray')
            ->containsExactly(
                array('object'),
                array('$user'),
                array(false)
            );

        /** @var Node $node */
        $node = Arrays::first($nodes);
        Assert::thatArray($node->getElements())
            ->extracting('type', 'name', 'isArray', 'elements')
            ->containsExactly(
                array('string', 'bool'),
                array('$name', '$isActive'),
                array(false, false),
                array(array(), array())
            );
    }

    /**
     * @test
     */
    public function shouldParseObjectArray()
    {
        //given
        $tokens = $this->tokenize('object[] $user { string $name bool $isActive }');
        $parser = new Parser($tokens);

        //when
        $nodes = $parser->S();

        //then
        Assert::thatArray($nodes)
            ->extracting('type', 'name', 'isArray')
            ->containsExactly(
                array('object'),
                array('$user'),
                array(true)
            );

        /** @var Node $node */
        $node = Arrays::first($nodes);
        Assert::thatArray($node->getElements())
            ->extracting('type', 'name', 'isArray', 'elements')
            ->containsExactly(
                array('string', 'bool'),
                array('$name', '$isActive'),
                array(false, false),
                array(array(), array())
            );
    }

    /**
     * @test
     */
    public function shouldParseSimpleAndObject()
    {
        //given
        $tokens = $this->tokenize('int $age 
        object $user { string $name bool $isActive }');
        $parser = new Parser($tokens);

        //when
        $nodes = $parser->S();

        //then
        Assert::thatArray($nodes)
            ->extracting('type', 'name', 'isArray')
            ->containsExactly(
                array('int', 'object'),
                array('$age', '$user'),
                array(false, false)
            );

        /** @var Node $node */
        $node = $nodes[0];
        $this->assertEmpty($node->getElements());

        /** @var Node $node */
        $node = $nodes[1];
        Assert::thatArray($node->getElements())
            ->extracting('type', 'name', 'isArray', 'elements')
            ->containsExactly(
                array('string', 'bool'),
                array('$name', '$isActive'),
                array(false, false),
                array(array(), array())
            );
    }

    /**
     * @test
     */
    public function shouldParseObjectWithArrayInsideAttribute()
    {
        //given
        $tokens = $this->tokenize('object $user { string[] $name bool $isActive }');
        $parser = new Parser($tokens);

        //when
        $nodes = $parser->S();

        //then
        Assert::thatArray($nodes)
            ->extracting('type', 'name', 'isArray')
            ->containsExactly(
                array('object'),
                array('$user'),
                array(false)
            );

        /** @var Node $node */
        $node = Arrays::first($nodes);
        Assert::thatArray($node->getElements())
            ->extracting('type', 'name', 'isArray', 'elements')
            ->containsExactly(
                array('string', 'bool'),
                array('$name', '$isActive'),
                array(true, false),
                array(array(), array())
            );
    }

    /**
     * @test
     */
    public function shouldParseObjectWithClassName()
    {
        //given
        $tokens = $this->tokenize('object $user { className \Foo\Bar\Baz }');
        $parser = new Parser($tokens);

        //when
        $nodes = $parser->S();

        //then
        Assert::thatArray($nodes)
            ->extracting('type', 'name', 'isArray')
            ->containsExactly(
                array('object'),
                array('$user'),
                array(false)
            );

        /** @var Node $node */
        $node = Arrays::first($nodes);
        Assert::thatArray($node->getElements())
            ->extracting('type', 'name', 'isArray', 'elements')
            ->containsExactly(
                array('className'),
                array('\Foo\Bar\Baz'),
                array(false),
                array(array())
            );
    }

    private function tokenize($string)
    {
        $tokenizer = new Tokenizer();
        return $tokenizer->lex($string);
    }
}
