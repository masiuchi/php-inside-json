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

/**
 * Inside JSON encoder
 * 
 * @category InsideJson
 * @package  InsideJson
 * @author   Masahiro IUCHI <masahiro.iuchi@gmail.com>
 * @license  https://opensource.org/licenses/MIT MIT
 * @link     https://github.com/masiuchi/php-inside-json
 */
class Encoder
{
    private $_top;

    public $options = 0;
    public $depth = 512;

    /**
     * Encode value to JSON string keeping inside JSON
     * 
     * @param mixed $value value to be encoded
     * 
     * @return string JSON string
     */
    public function encode($value)
    {
        if (is_a($value, 'InsideJson\Json') == false) {
            return $this->_encode($value);
        }

        $this->_top = $value;
        return $this->_encodeInsideJson($value);
    }

    /**
     * Encode inside JSON recursively
     * 
     * @param mixed $value value to be encoded
     * 
     * @return mixed value or JSON string
     */
    private function _encodeInsideJson($value)
    {
        if (is_null($value) || is_scalar($value)) {
            return $value;
        }
          
        foreach ($value as $k => $v) {
            $value[$k] = $this->_encodeInsideJson($v);
        }

        if ($this->_shouldEncode($value) == false) {
            return $value;
        }
        return $this->_encode($value->toArray());
    }

    /**
     * Encode value to JSON string
     * 
     * @param mixed $value value to be encoded
     * 
     * @return string JSON string
     */
    private function _encode($value)
    {
        if (version_compare(PHP_VERSION, '5.4') == 0) {
            return json_encode($value, $this->options);
        } else {
            return json_encode($value, $this->options, $this->depth);
        }
    }

    /**
     * Check whether $value should be encoded or not
     * 
     * @param $value InsideJson\Json object
     * 
     * @return bool value should be encoded or not
     */
    private function _shouldEncode($value)
    {
        return $value === $this->_top || $value->inside;
    }
}
