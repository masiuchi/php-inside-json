# php-inside-json
Codec for inside JSON

## Usage

```php
use InsideJson\Decoder;
use InsideJson\Encoder;

$decoder = new Decoder;
$encoder = new Encoder;

# JSON string has serialized JSON string its inside.
$json = '{"a":"{\"b\":1,\"c\":2}","d":3}';

# decode JSON and inside JSON at once
$obj = $decoder->decode($json);

# access decoded object by using array-access or property
$obj['a']['b'] = 4;
$obj->a->c = 5;

# access decode object by using foreach
foreach ($obj as $key1 => $value1) {
  if (is_object($obj)) {
    echo "$key1:\n";
    foreach ($value1 as $key2 => $value2) {
      echo "\t$key2: $value2\n";
    }
  } else {
    echo "$key1: $value1\n";
  }
}

# encode to JSON string keeping inside JSON
$encoder->encode($obj);  # '{"a":"{\"b\":4,\"c\":5}","d":3}'

# encode to JSON string expanding inside JSON
json_encode($obj->toArray());  # '{"a":{"b":4,"c":5},"d":3}'

```

## License

MIT License
https://opensource.org/licenses/mit-license.php
