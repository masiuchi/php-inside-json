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

use InsideJson\Decoder;
use InsideJson\Json;

/**
 * Tests for InsideJson\Decoder
 * 
 * @category InsideJson/Test
 * @package  InsideJson/Test
 * @author   Masahiro IUCHI <masahiro.iuchi@gmail.com>
 * @license  https://opensource.org/licenses/MIT MIT
 * @link     https://github.com/masiuchi/php-inside-json
 */
class DecoderTest extends TestCase
{
    protected $dec;

    /**
     * Create InsideJson\Decoder instance
     * 
     * @return void
     */
    protected function setUp(): void
    {
        $this->dec = new Decoder;
    }

    /**
     * Test decoder output equals json_decode result
     * 
     * @param mixed $value decoder input
     * 
     * @return void
     */
    protected function assertEqualsToJsonDecode($value)
    {
        $expected = json_decode($value);
        $actual = $this->dec->decode($value);
        $this->assertEquals($expected, $actual);
    }

    /**
     * Test json_decode behavior
     * 
     * @test
     * @return void
     */
    public function testJsonDecodeBehavior()
    {
        $this->assertEqualsToJsonDecode(null);
        $this->assertEqualsToJsonDecode('');
        $this->assertEqualsToJsonDecode(1);
        $this->assertEqualsToJsonDecode('abc');
    }

    /**
     * Test to decode normal JSON string
     * (associative array)
     * 
     * @test
     * @return void
     */
    public function testNormalJson()
    {
        $value = $this->dec->decode('{"a":1,"b":[2,3],"c":{"d":4}}');
        $expected = new Json(
            [
                'a' => 1,
                'b' => new Json([2, 3]),
                'c' => new Json(['d' => 4]),
            ]
        );
        $this->assertEquals($expected, $value);
    }

    /**
     * Test to decode JSON string having inside JSON
     * (associative array)
     * 
     * @test
     * @return void
     */
    public function testInsideJson()
    {
        $value = $this->dec->decode('{"a":1,"b":"[2,3]","c":"{\"d\":4}"}');
        $expected = new Json(
            [
                'a' => '1',
                'b' => new Json([2, 3], true),
                'c' => new Json(['d' => 4], true),
            ]
        );
        $this->assertEquals($expected, $value);
    }

    /**
     * Test to decode normal JSON string (array)
     * 
     * @test
     * @return void
     */
    public function testNormalArrayJson()
    {
        $value = $this->dec->decode('[1,{"a":2},[3,4]]');
        $expected = new Json(
            [
                1,
                new Json(['a' => 2]),
                new Json([3, 4]),
            ]
        );
        $this->assertEquals($expected, $value);
    }

    /**
     * Test to decode JSON string having inside JSON
     * (array)
     * 
     * @test
     * @return void
     */
    public function testArrayInsideJson()
    {
        $value = $this->dec->decode('[1,"{\"a\":2}","[3,4]"]');
        $expected = new Json(
            [
                1,
                new Json(['a' => 2], true),
                new Json([3, 4], true),
            ]
        );
        $this->assertEquals($expected, $value);
    }
}