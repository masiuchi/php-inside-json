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

use stdClass;

use InsideJson\Encoder;
use InsideJson\Json;

/**
 * Tests for InsideJson\Encoder
 * 
 * @category InsideJson/Test
 * @package  InsideJson/Test
 * @author   Masahiro IUCHI <masahiro.iuchi@gmail.com>
 * @license  https://opensource.org/licenses/MIT MIT
 * @link     https://github.com/masiuchi/php-inside-json
 */
class EncoderTest extends TestCase
{
    protected $enc;

    /**
     * Create InsideJson\Encoder instance
     * 
     * @return void
     */
    protected function setUp(): void
    {
        $this->enc = new Encoder;
    }

    /**
     * Test encoder output equals json_encode result
     * 
     * @param mixed $value encoder input
     * 
     * @return void
     */
    protected function assertEqualsToJsonEncode($value)
    {
        $expected = json_encode($value);
        $actual = $this->enc->encode($value);
        $this->assertEquals($expected, $actual);
    }

    /**
     * Test json_encode behavior
     * 
     * @test
     * @return void
     */
    public function testJsonEncodeBehavior()
    {
        $this->assertEqualsToJsonEncode('null');
        $this->assertEqualsToJsonEncode('');
        $this->assertEqualsToJsonEncode(1);
        $this->assertEqualsToJsonEncode('abc');
        $this->assertEqualsToJsonEncode([1, 2, 3]);
        $this->assertEqualsToJsonEncode(
            [ 'a' => 1, 'b' => [2, 3], 'c' => [ 'd' => 4 ] ]
        );

        $obj = new stdClass;
        $obj->a = 1;
        $obj->b = [2, 3];
        $obj->c = new stdClass;
        $obj->c->d = 4;
        $this->assertEqualsToJsonEncode($obj);

        $array = (array) $obj;
        $array['c'] = (array) $array['c'];
        $this->assertEqualsToJsonEncode($array);
    }

    /**
     * Test to encode normal object
     * 
     * @test
     * @return void
     */
    public function testNormalNestedJson()
    {
        $value = new Json(['a' => new Json(['b' => 2])]);
        $json = $this->enc->encode($value);
        $expected = '{"a":{"b":2}}';
        $this->assertEquals($expected, $json);
    }

    /**
     * Test to encode object with inside JSON
     * 
     * @test
     * @return void
     */
    public function testKeepInsideJson()
    {
        $value = new Json(['a' => new Json(['b' => 2], true)]);
        $json = $this->enc->encode($value);
        $expected = '{"a":"{\"b\":2}"}';
        $this->assertEquals($expected, $json);
    }

    /**
     * Encode object and expend inside JSON
     * 
     * @test
     * @return void
     */
    public function testExpandInsideJson()
    {
        $enc = $this->enc;
        $value = new Json(['a' => new Json(['b' => 2], true)]);
        $json = $enc->encode($value->toArray());
        $expected = '{"a":{"b":2}}';
        $this->assertEquals($expected, $json);
    }
}