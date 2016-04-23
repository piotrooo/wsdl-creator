<?php
use Ouzo\Tests\Assert;
use WSDL\Lexer\Token;
use WSDL\Lexer\Tokenizer;

class TokenizerTest extends PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function shouldTokenizeSimpleType()
    {
        //given
        $param = 'int $age';
        $tokenizer = new Tokenizer();

        //when
        $tokens = $tokenizer->lex($param);

        //then
        Assert::thatArray($tokens)
            ->extracting('getName()', 'getValue()')
            ->containsExactly(
                array(Token::TYPE, Token::NAME, Token::EOF),
                array('int', '$age', 'eof')
            );
    }

    /**
     * @test
     */
    public function shouldTokenizeSimpleTypeWithArray()
    {
        //given
        $param = 'string[] $name';
        $tokenizer = new Tokenizer();

        //when
        $tokens = $tokenizer->lex($param);

        //then
        Assert::thatArray($tokens)
            ->extracting('getName()', 'getValue()')
            ->containsExactly(
                array(Token::TYPE, Token::ARRAYS, Token::NAME, Token::EOF),
                array('string', '[]', '$name', 'eof')
            );
    }

    /**
     * @test
     */
    public function shouldTokenizeObject()
    {
        //given
        $param = 'object $name {string $firstName int $age}';
        $tokenizer = new Tokenizer();

        //when
        $tokens = $tokenizer->lex($param);

        //then
        Assert::thatArray($tokens)
            ->extracting('getName()', 'getValue()')
            ->containsExactly(
                array(Token::TYPE, Token::NAME, Token::OPEN_OBJECT, Token::TYPE, Token::NAME, Token::TYPE, Token::NAME, Token::CLOSE_OBJECT, Token::EOF),
                array('object', '$name', '{', 'string', '$firstName', 'int', '$age', '}', 'eof')
            );
    }

    /**
     * @test
     */
    public function shouldTokenizeObjectWithArray()
    {
        //given
        $param = 'object[] $name {string $firstName int $age}';
        $tokenizer = new Tokenizer();

        //when
        $tokens = $tokenizer->lex($param);

        //then
        Assert::thatArray($tokens)
            ->extracting('getName()', 'getValue()')
            ->containsExactly(
                array(Token::TYPE, Token::ARRAYS, Token::NAME, Token::OPEN_OBJECT, Token::TYPE, Token::NAME, Token::TYPE, Token::NAME, Token::CLOSE_OBJECT, Token::EOF),
                array('object', '[]', '$name', '{', 'string', '$firstName', 'int', '$age', '}', 'eof')
            );
    }

    /**
     * @test
     */
    public function shouldTokenizeNestedObjects()
    {
        //given
        $param = 'object $name { 
            object $user{
                string $firstName
                int $age
            }
            int $count
        }';
        $tokenizer = new Tokenizer();

        //when
        $tokens = $tokenizer->lex($param);

        //then
        Assert::thatArray($tokens)
            ->extracting('getName()', 'getValue()')
            ->containsExactly(
                array(
                    Token::TYPE, Token::NAME, Token::OPEN_OBJECT,
                    Token::TYPE, Token::NAME, Token::OPEN_OBJECT,
                    Token::TYPE, Token::NAME,
                    Token::TYPE, Token::NAME,
                    Token::CLOSE_OBJECT,
                    Token::TYPE, Token::NAME,
                    Token::CLOSE_OBJECT,
                    Token::EOF
                ),
                array('object', '$name', '{', 'object', '$user', '{', 'string', '$firstName', 'int', '$age', '}', 'int', '$count', '}', 'eof')
            );
    }

    /**
     * @test
     */
    public function shouldTokenizeClassWrapper()
    {
        //given
        $param = 'object $name { 
            className \Foo\Bar\Baz
        }';
        $tokenizer = new Tokenizer();

        //when
        $tokens = $tokenizer->lex($param);

        //then
        Assert::thatArray($tokens)
            ->extracting('getName()', 'getValue()')
            ->containsExactly(
                array(
                    Token::TYPE, Token::NAME, Token::OPEN_OBJECT,
                    Token::TYPE, Token::CLASS_NAME,
                    Token::CLOSE_OBJECT,
                    Token::EOF
                ),
                array('object', '$name', '{', 'className', '\Foo\Bar\Baz', '}', 'eof')
            );
    }
}
