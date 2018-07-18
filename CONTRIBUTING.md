All contributions are welcome!
==============================

We'd love your help!

Code style
----------

To maintain code in the good shape, we using a [php-cs-fixer](https://github.com/FriendsOfPHP/PHP-CS-Fixer) tool. Library provides two built-in scripts:

* `check-code-style` for check code shape (find violations)
* `fix-code-style` for fix violations in the code

Run example:

```
composer run-script fix-code-style
```

PS. The `check-code-style` script is used in Travis to check a code is well formatted.

Tests
-----

All new features should be covered by the tests. 

Tests should be in the BDD style, prefixed by `should`. 

Sample tests method:

```php
/**
 * @test
 */
public function shouldAddTwoDigits()
{
    //given
    $calculator = new Calculator()

    //when
    $sum = $calculator->add(1, 2);

    //then
    $this->assertEquals(3, $sum);
}
```
