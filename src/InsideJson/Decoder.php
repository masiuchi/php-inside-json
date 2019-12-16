<?php
/**
 * Codec for inside JSON
 *
 * PHP VERSION >= 5.4
 * 
 * @category InsideJson
 * @package  InsideJson
 * @author   Masahiro IUCHI <masahiro.iuchi@gmail.com>
 * @license  https://opensource.org/licenses/MIT MIT
 * @version  GIT: <git_id>
 * @link     https://github.com/masiuchi/php-inside-json
 */
namespace InsideJson;

use stdClass;
use InvalidArgumentException;

/**
 * Inside JSON decoder
 * 
 * @category InsideJson
 * @package  InsideJson
 * @author   Masahiro IUCHI <masahiro.iuchi@gmail.com>
 * @license  https://opensource.org/licenses/MIT MIT
 * @link     https://github.com/masiuchi/php-inside-json
 */
class Decoder
{
    public $assoc = false;
    public $depth = 512;
    public $options = 0;

    /**
     * Decode JSON string and inside JSON string
     * 
     * @param string $json JSON string
     * 
     * @return mixed decoded value
     */
    public function decode($json)
    {
        $value = $this->_decode($json);
        if (is_null($value) || is_scalar($value)) {
            return $value;
        }
        return $this->_decodeInsideJson($value);
    }

    /**
     * Decode inside JSON string recursively
     * 
     * @param mixed $value  value to be decoded
     * @param bool  $inside inside JSON flag
     * 
     * @return mixed decoded value
     */
    private function _decodeInsideJson($value, $inside = false)
    {
        if (is_null($value)) {
            return $value;
        }
        if (is_scalar($value)) {
            if (self::_looksLikeJsonString($value) == false) {
                return $value;
            }
            $decodedValue = $this->_decode($value);
            return $this->_decodeInsideJson($decodedValue, true);
        }
 
        if (is_array($value)) {
            foreach ($value as $k => $v) {
                $value[$k] = $this->_decodeInsideJson($v);
            }
        } elseif (is_a($value, 'stdClass')) {
            $vars = get_object_vars($value);
            foreach ($vars as $k => $v) {
                $value->$k = $this->_decodeInsideJson($v);
            }
        } else {
            throw new InvalidArgumentException;
        }
        return new Json($value, $inside);
    }

    /**
     * Decode JSON string
     * 
     * @param string $json JSON string
     * 
     * @return mixed decoded value
     */
    private function _decode($json)
    {
        return json_decode($json, $this->assoc, $this->depth, $this->options);
    }

    /**
     * Check whether $str looks like JSON string or not
     * 
     * @param string $str string to be checked
     * 
     * @return bool looks like JSON string or not
     */
    private static function _looksLikeJsonString($str)
    {
        return preg_match('/\A\s*[\[\{]/', $str);
    }
}