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
use ArrayAccess;
use ArrayIterator;
use Countable;
use Exception;
use IteratorAggregate;

/**
 * JSON object
 * 
 * @category InsideJson
 * @package  InsideJson
 * @author   Masahiro IUCHI <masahiro.iuchi@gmail.com>
 * @license  https://opensource.org/licenses/MIT MIT
 * @link     https://github.com/masiuchi/php-inside-json
 */
class Json implements ArrayAccess, Countable, IteratorAggregate
{
    private $_value = [];
    private $_encoded = false;

    /**
     * Create Json instance from value
     * 
     * @param mixed $value   value to be Json instance
     * @param bool  $encoded encoded flag
     * 
     * @return mixed null or scalar value or Json instance
     */
    public static function toJson($value, $encoded = false)
    {
        if (is_null($value)
            || is_scalar($value)
            || is_a($value, 'InsideJson\Json')
        ) {
            return $value;
        }
        return $json = new Json($value, $encoded);
    }

    /**
     * Constructor
     * 
     * @param mixed $value   array or stdClass
     * @param bool  $encoded encoded flag
     */
    public function __construct($value = [], $encoded = false)
    {
        $this->_encoded = $encoded;
        $this->_value = $this->_initializeValue($value);
    }

    /**
     * Set value
     * 
     * @param mixed $name  key
     * @param mixed $value value
     * 
     * @return void
     */
    public function __set($name, $value)
    {
        $this->offsetSet($name, $value);
    }

    /**
     * Check set value
     * 
     * @param mixed $name key
     * 
     * @return bool set or not
     */
    public function __isset($name)
    {
        return $this->offsetExists($name);
    }

    /**
     * Unset value
     * 
     * @param mixed $name key
     * 
     * @return void
     */
    public function __unset($name)
    {
        $this->offsetUnset($name);
    }

    /**
     * Get value
     * 
     * @param mixed $name key
     * 
     * @return mixed 
     */
    public function __get($name)
    {
        return $this->offsetGet($name);
    }

    /**
     * Initialize value
     * 
     * @param mixed $value raw value
     * 
     * @return mixed initialized value
     */
    private function _initializeValue($value)
    {
        if (is_null($value) || is_scalar($value)) {
            throw new Exception;
        }

        if (is_array($value)) {
            $vars = $value;
        } elseif (is_a($value, 'stdClass')) {
            $vars = get_object_vars($value);
        } else {
            throw new Exception;
        }

        $initializedValue = [];
        foreach ($vars as $k => $v) {
            $initializedValue[$k] = self::toJson($v, $this->_encoded);
        }

        return $initializedValue;
    }

    /**
     * Set value
     * 
     * @param mixed $offset key
     * @param mixed $value  value
     * 
     * @return void
     */
    public function offsetSet($offset, $value)
    {
        if (is_null($offset)) {
            $keys = array_filter(
                array_keys($this->_value),
                function ($key) {
                    return is_numeric($key);
                }
            );
            if (count($keys) > 0) {
                $offset = max($keys) + 1;
            } else {
                $offset = 0;
            }
        }
        $this->_value[$offset] = $value;
    }

    /**
     * Check set value
     * 
     * @param mixed $offset key
     * 
     * @return bool value is set or not
     */
    public function offsetExists($offset)
    {
        return isset($this->_value[$offset]);
    }

    /**
     * Unset value
     * 
     * @param mixed $offset key
     * 
     * @return void
     */
    public function offsetUnset($offset)
    {
        unset($this->_value[$offset]);
    }

    /**
     * Get value
     * 
     * @param mixed $offset key
     * 
     * @return mixed value
     */
    public function offsetGet($offset)
    {
        return isset($this->_value[$offset]) ? $this->_value[$offset] : null;
    }

    /**
     * Count values
     * 
     * @return int count of value
     */
    public function count()
    {
        return count($this->_value);
    }

    /**
     * Support foreach function
     * 
     * @return ArrayIterator ArrayIterator instance
     */
    public function getIterator()
    {
        return new ArrayIterator($this->_value);
    }

    /**
     * Check encode or not
     * 
     * @return bool encode or not
     */
    public function isEncoded()
    {
        return $this->_encoded;
    }

    /**
     * Convert to array recursively
     * 
     * @return array converted array value
     */
    public function toArray()
    {
        $array = [];
        foreach ($this->_value as $key => $value) {
            if (is_null($value) || is_scalar($value)) {
                $array[$key] = $value;
                continue;
            }
            $array[$key] = $value->toArray();
        }
        return $array;
    }

    /**
     * Convert to stdClass instance recursively
     * 
     * @return stdClass converted stdClass instance
     */
    public function toObject()
    {
        $obj = new stdClass;
        foreach ($this->_value as $key => $value) {
            if (is_null($value) || $is_scalar($value)) {
                $obj->$key = $value;
                continue;
            }
            $obj->$key = $value->toObject();
        }
        return $obj;
    }
}