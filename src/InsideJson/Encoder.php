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
 * JSON encoder
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
     * Encode value to JSON keeping inside JSON
     * 
     * @param mixed $value value to be encoded to JSON
     * 
     * @return string JSON string
     */
    public function encode($value)
    {
        if (is_a($value, 'InsideJson\Json') == false) {
            return $this->_encode($value);
        }

        $this->_top = $value;
        return $this->_encodeRecursively($value);
    }

    /**
     * Encode value to JSON recursively
     * 
     * @param mixed $value value to be encoded to JSON
     * 
     * @return mixed value or JSON string
     */
    private function _encodeRecursively($value)
    {
        if (is_null($value) || is_scalar($value)) {
            return $value;
        }
          
        foreach ($value as $k => $v) {
            $value[$k] = $this->_encodeRecursively($v);
        }

        if ($this->_shouldEncode($value) == false) {
            return $value;
        }
        return $this->_encode($value->toArray());
    }

    /**
     * Encode value to JSON
     * 
     * @param mixed $value data to be encoded to JSON
     * 
     * @return string JSON string
     */
    private function _encode($value)
    {
        return json_encode($value, $this->options, $this->depth);
    }

    /**
     * Check $value should be encoded or not
     * 
     * @param $value InsideJson\Json object
     * 
     * @return bool encode or not
     */
    private function _shouldEncode($value)
    {
        return $value === $this->_top || $value->isEncoded();
    }
}
