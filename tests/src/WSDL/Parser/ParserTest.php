<?php
use Ouzo\Tests\Assert;
use Ouzo\Tests\CatchException;
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
        $parser = $this->parser('int $age');

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
        $parser = $this->parser('int[] $age');

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
        $parser = $this->parser('object $user { string $name bool $isActive }');

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
        $parser = $this->parser('object[] $user { string $name bool $isActive }');

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
        $parser = $this->parser('int $age 
        object $user { string $name bool $isActive }');

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
        $parser = $this->parser('object $user { string[] $name bool $isActive }');

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
        $parser = $this->parser('object $user { className \Foo\Bar\Baz }');

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

    /**
     * @test
     */
    public function shouldThrowExceptionWhenSimpleTypeNotHaveType()
    {
        //given
        $parser = $this->parser('$name');

        //when
        CatchException::when($parser)->S();

        //then
        CatchException::assertThat()->hasMessage('Parse error - wrong type');
    }

    /**
     * @test
     */
    public function shouldThrowExceptionWhenSimpleTypeNotHaveName()
    {
        //given
        $parser = $this->parser('int');

        //when
        CatchException::when($parser)->S();

        //then
        CatchException::assertThat()->hasMessage('Parse error - wrong name');
    }

    /**
     * @test
     */
    public function shouldThrowExceptionWhenObjectNotHaveType()
    {
        //given
        $parser = $this->parser('$user { int $age }');

        //when
        CatchException::when($parser)->S();

        //then
        CatchException::assertThat()->hasMessage('Parse error - wrong type');
    }

    /**
     * @test
     */
    public function shouldThrowExceptionWhenObjectNotHaveName()
    {
        //given
        $parser = $this->parser('object { int $age }');

        //when
        CatchException::when($parser)->S();

        //then
        CatchException::assertThat()->hasMessage('Parse error - wrong name');
    }

    /**
     * @test
     */
    public function shouldThrowExceptionWhenInsideObjectAttributeNotHaveType()
    {
        //given
        $parser = $this->parser('object $user { $age }');

        //when
        CatchException::when($parser)->S();

        //then
        CatchException::assertThat()->hasMessage('Parse error - wrong type');
    }

    /**
     * @test
     */
    public function shouldThrowExceptionWhenInsideObjectAttributeNotHaveName()
    {
        //given
        $parser = $this->parser('object $user { int }');

        //when
        CatchException::when($parser)->S();

        //then
        CatchException::assertThat()->hasMessage('Parse error - wrong name');
    }

    /**
     * @test
     */
    public function shouldThrowExceptionWhenObjectNotHaveOpen()
    {
        //given
        $parser = $this->parser('object $user int $age }');

        //when
        CatchException::when($parser)->S();

        //then
        CatchException::assertThat()->hasMessage('Parse error - missing open object');
    }

    /**
     * @test
     */
    public function shouldThrowExceptionWhenObjectNotHaveOpenInNestedObject()
    {
        //given
        $parser = $this->parser('object $user { 
            int $age 
            object $role 
                int $id
                string $name
            }
        }');

        //when
        CatchException::when($parser)->S();

        //then
        CatchException::assertThat()->hasMessage('Parse error - missing open object');
    }

    /**
     * @test
     */
    public function shouldThrowExceptionWhenObjectNotHaveClose()
    {
        //given
        $parser = $this->parser('object $user { int $age ');

        //when
        CatchException::when($parser)->S();

        //then
        CatchException::assertThat()->hasMessage('Parse error - missing close object');
    }

    /**
     * @test
     */
    public function shouldThrowExceptionWhenObjectNotHaveCloseInNestedObject()
    {
        //given
        $parser = $this->parser('object $user { 
            int $age 
            object $role {
                int $id
                string $name
        }');

        //when
        CatchException::when($parser)->S();

        //then
        CatchException::assertThat()->hasMessage('Parse error - missing close object');
    }

    private function parser($string)
    {
        $tokenizer = new Tokenizer();
        $tokens = $tokenizer->lex($string);
        return new Parser($tokens);

    }
}
