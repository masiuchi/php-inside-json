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
use Exception;

/**
 * JSON decoder
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
     * Decode JSON to value
     * 
     * @param string $json JSON string
     * 
     * @return mixed
     */
    public function decode($json)
    {
        $value = $this->_decode($json);
        if (is_null($value) || is_scalar($value)) {
            return $value;
        }
        return $this->_decodeRecursively($value);
    }

    /**
     * Decode JSON to value recursively
     * 
     * @param mixed $value   a
     * @param bool  $encoded b
     * 
     * @return mixed
     */
    private function _decodeRecursively($value, $encoded = false)
    {
        if (is_null($value)) {
            return $value;
        }
        if (is_scalar($value)) {
            if (self::_looksLikeJsonString($value) == false) {
                return $value;
            }
            $decodedValue = $this->_decode($value);
            return $this->_decodeRecursively($decodedValue, true);
        }
 
        if (is_array($value)) {
            foreach ($value as $k => $v) {
                $value[$k] = $this->_decodeRecursively($v);
            }
        } elseif (is_a($value, 'stdClass')) {
            $vars = get_object_vars($value);
            foreach ($vars as $k => $v) {
                $value->$k = $this->_decodeRecursively($v);
            }
        } else {
            throw new Exception;
        }
        return new Json($value, $encoded);
    }

    /**
     * Decode JSON
     * 
     * @param string $json JSON
     * 
     * @return mixed decoded $value
     */
    private function _decode($json)
    {
        return json_decode($json, $this->assoc, $this->depth, $this->options);
    }

    /**
     * Check whether $str looks like JSON or not
     * 
     * @param string $str String to be checked
     * 
     * @return bool result
     */
    private static function _looksLikeJsonString($str)
    {
        return preg_match('/\A\s*[\[\{]/', $str);
    }
}