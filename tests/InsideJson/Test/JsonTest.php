<?php
/**
 * Tests for InsideJson
 *
 * PHP VERSION >= 5.4
 * 
 * @category InsideJson\Test
 * @package  InsideJson\Test
 * @author   Masahiro IUCHI <masahiro.iuchi@gmail.com>
 * @license  https://opensource.org/licenses/MIT MIT
 * @version  GIT: <git_id>
 * @link     https://github.com/masiuchi/php-inside-json
 */
 namespace InsideJson\Test;

use PHPUnit\Framework\TestCase;

use InsideJson\Json;
use stdClass;

/**
 * Tests for InsideJson\Json
 * 
 * @category InsideJson/Test
 * @package  InsideJson/Test
 * @author   Masahiro IUCHI <masahiro.iuchi@gmail.com>
 * @license  https://opensource.org/licenses/MIT MIT
 * @link     https://github.com/masiuchi/php-inside-json
 */
class JsonTest extends TestCase
{
    /**
     * Test constructor without value
     * 
     * @test
     * @return void
     */
    public function testConstructorWithoutValue()
    {
        $obj = new Json;
        $this->assertInstanceOf('InsideJson\Json', $obj);
    }

    /**
     * Test constructor with null value
     * 
     * @test
     * @expectedException InvalidArgumentException
     * 
     * @return void
     */
    public function testConstructorWithNull()
    {
        new Json(null);
    }

    /**
     * Test constructor with scalar value
     * 
     * @test
     * @expectedException InvalidArgumentException
     * 
     * @return void
     */
    public function testConstructorWithScalar()
    {
        new Json(1);
    }

    /**
     * Test constructor with Json instance
     * 
     * @test
     * @expectedException InvalidArgumentException
     * 
     * @return void
     */
    public function testConstructorWithJsonInstance()
    {
        new Json(new Json);
    }

    /**
     * Test constructor with array
     * 
     * @test
     * @return void
     */
    public function testConstructorWithArray()
    {
        $obj = new Json(
            [
                1,
                [ 2, 3 ],
                [ 'a' => 4 ],
            ]
        );

        $this->assertInstanceOf('InsideJson\Json', $obj);
        $this->assertInstanceOf('InsideJson\Json', $obj[1]);
        $this->assertInstanceOf('InsideJson\Json', $obj[2]);
    }

    /**
     * Test constructor with associative array
     * 
     * @test
     * @return void
     */
    public function testConstructorWithAssociativeArray()
    {
        $obj = new Json(
            [
                'a' => [ 'b' => 1 ],
                'c' => [ 1, 2, 3 ],
                'd' => 4,
            ]
        );

        $this->assertInstanceOf('InsideJson\Json', $obj);
        $this->assertInstanceOf('InsideJson\Json', $obj['a']);
        $this->assertInstanceOf('InsideJson\Json', $obj['c']);
        $this->assertTrue(is_scalar($obj['d']));
    }

    /**
     * Test stdClass behavior
     *
     * @test
     * @return void
     */
    public function testStdClassBehavior()
    {
        $obj = new Json;
        $obj->a = 1;
        $obj->b = 2;

        $this->assertInstanceOf('InsideJson\Json', $obj);
        $this->assertEquals(1, $obj->a);
        $this->assertEquals(2, $obj->b);
        $this->assertNotTrue(isset($obj->c));
        $this->assertEquals([ 'a' => 1, 'b' => 2 ], $obj->toArray());

        unset($obj->a);

        $this->assertEquals([ 'b' => 2 ], $obj->toArray());
    }

    /**
     * Test array behavior
     * 
     * @test
     * @return void
     */
    public function testArrayBehavior()
    {
        $obj = new Json;

        $this->assertEquals(0, count($obj));

        $obj[] = 1;
        $obj[] = 2;

        $this->assertEquals(2, count($obj));
        $this->assertEquals(1, $obj[0]);
        $this->assertEquals(2, $obj[1]);
        $this->assertNotTrue(isset($obj[2]));
    }

    /**
     * Test associative array behavior
     * 
     * @test
     * @return void
     */
    public function testAssociativeArrayBehavior()
    {
        $obj = new Json;
        $obj['a'] = 1;
        $obj['b'] = 2;

        $this->assertEquals(1, $obj['a']);
        $this->assertEquals(2, $obj['b']);
        $this->assertNotTrue(isset($obj['c']));

        unset($obj['a']);

        $this->assertNotTrue(isset($obj['a']));
    }

    /**
     * Test foreach function
     * 
     * @test
     * @return void
     */
    public function testForeach()
    {
        $array = [ 'a' => 1, 'b' => 2 ];
        $obj = new Json($array);
        foreach ($obj as $key => $value) {
            $this->assertEquals($array[$key], $value);
        }
    }

    /**
     * Test nested foreach function
     * 
     * @test
     * @return void
     */
    public function testNestedForeach()
    {
        $childArray = [ 'a' => 1, 'b' => 2 ];
        $parentArray = [ 'c' => 3, 'd' => 4, 'e' => $childArray ];
        $obj = new Json($parentArray);

        foreach ($obj as $key => $value) {
            if (is_object($value)) {
                foreach ($value as $k => $v) {
                    $this->assertEquals($v, $childArray[$k]);
                }
            } else {
                $this->assertEquals($value, $parentArray[$key]);
            }
        }
    }

    /**
     * Test isInside method
     * 
     * @test
     * @return void
     */
    public function testIsInside()
    {
        $objIsInside = new Json([], true);
        $this->assertTrue($objIsInside->isInside());

        $objIsNotInside = new Json;
        $this->assertNotTrue($objIsNotInside->isInside());
    }

    /**
     * Test toArray method
     * 
     * @test
     * @return void
     */
    public function testToArray()
    {
        $value = new stdClass;
        $value->a = 1;
        $obj = new Json($value);
        $expected = [ 'a' => 1 ];
        $this->assertEquals($expected, $obj->toArray());
    }

    /**
     * Test toObject method
     * 
     * @test
     * @return void
     */
    public function testToObject()
    {
        $value = [ 'a' => 1 ];
        $obj = new Json($value);
        $expected = new stdClass;
        $expected->a = 1;
        $this->assertEquals($expected, $obj->toObject());
    }
}