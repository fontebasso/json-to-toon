# json-to-toon

`json-to-toon` is a lightweight PHP library that converts verbose JSON into a compact, LLM-optimized format called TOON.
The TOON format preserves structure while minimizing token usage, improving context efficiency for large language models such as ChatGPT and Claude.

This implementation follows the [TOON Specification v2.0](https://github.com/toon-format/spec), supporting arrays, nested objects, primitives, and non-uniform structures — including compact representation of associative and nested data.

## Installation

You can install the library via Composer:

```bash
composer require fontebasso/json-to-toon
```

## Overview

Traditional JSON structures are often verbose and redundant, especially when serialized for language models.
`json-to-toon` introduces a concise syntax that encodes data in a tabular form, preserving semantics while reducing size.

### Example

Input JSON:

```json
[
  {"id": 1, "name": "Alice", "role": "admin"},
  {"id": 2, "name": "Bob", "role": "user"}
]
```

Converted Toon:

```txt
users[2]{id,name,role}:
1,Alice,admin
2,Bob,user
```

This representation is shorter, token-efficient, and ideal for transmitting structured context to LLMs such as ChatGPT or Claude.

## Usage

### From PHP Array

```php
<?php

use Fontebasso\JsonToToon\Toon;

require 'vendor/autoload.php';

$data = [
  ["id" => 1, "name" => "Alice", "role" => "admin"],
  ["id" => 2, "name" => "Bob", "role" => "user"]
];

$toon = Toon::encode('users', $data);

echo $toon;
```

### From JSON String

```php
<?php

use Fontebasso\JsonToToon\Toon;

require 'vendor/autoload.php';

$json = '[
  {"id": 1, "name": "Alice", "role": "admin"},
  {"id": 2, "name": "Bob", "role": "user"}
]';

$toon = Toon::encode('users', $json);

echo $toon;
```

### Output

```txt
users[2]{id,name,role}:
1,Alice,admin
2,Bob,user
```

## Compact Mode

`json-to-toon` automatically encodes associative objects using the compact TOON syntax:

```php
$data = [
  'meta' => ['a' => 1, 'b' => 2],
  'tags' => ['x', 'y'],
];

echo Toon::encode('data', $data);
```

### Output

```txt
data:
meta{a=1;b=2}
tags[2]{value}:
x
y
```

Nested arrays are represented recursively:

```txt
profile{name=Alice;skills=[PHP|Go]}
```

## Tests

Run the PHPUnit suite:

```bash
composer test
```

Open the generated coverage report at:

```bash
coverage/html/index.html
```

## Continuous Integration

The repository includes a GitHub Actions workflow that automatically runs tests on PHP 8.2, 8.3, and 8.4 for every push or pull request to main.

## License

This library is licensed under the MIT License. See the [LICENSE](LICENSE) file for details.
