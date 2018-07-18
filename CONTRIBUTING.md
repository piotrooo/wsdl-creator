All contributions are welcome!
==============================

We'd love your help!

Tests
-----

All new features should be covered by the tests. 

Tests should be in the BDD style, prefixed by `should`. 

Sample tests method:

```
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
