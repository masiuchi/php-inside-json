# php-inside-json
Codec for inside JSON

## Usage

```php
use InsideJson\Decoder;
use InsideJson\Encoder;

$decoder = new Decoder;
$encoder = new Encoder;

# JSON string has serialized JSON string its inside.
$json = '{"a":"{\"b\":1}"}';

# decode JSON and inside JSON
$obj = $decoder->decode($json);

# manipulate JSON data
$obj['b'] = 2;
$obj->c = 3;

# encode keeping inside JSON
$encoder->encode($obj);  # '{"a":"{\"b\":2,\"c\":3}"}'

# encode expanding inside JSON
$encoder->encode($obj->toArray());  # '{"a":{"b":2,"c":3}}'

```

## License

MIT License
https://opensource.org/licenses/mit-license.php
