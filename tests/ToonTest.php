<?php

use Fontebasso\JsonToToon\Toon;
use PHPUnit\Framework\TestCase;

class ToonTest extends TestCase
{
    public function testEncodeScalarValues(): void
    {
        $this->assertEquals('value:123', Toon::encode('value', 123));
        $this->assertEquals('flag:true', Toon::encode('flag', true));
        $this->assertEquals('name:Alice', Toon::encode('name', 'Alice'));
        $this->assertEquals('nothing:', Toon::encode('nothing', null));
    }

    public function testThrowsOnInvalidJson(): void
    {
        $this->expectException(InvalidArgumentException::class);
        Toon::encode('users', '{invalid}');
    }

    public function testEncodesEmptyArray(): void
    {
        $this->assertEquals('users[0]{}:', Toon::encode('users', []));
    }

    public function testEncodesArrayOfPrimitives(): void
    {
        $array = [1, 2, 3];
        $expected = "numbers[3]{value}:\n1\n2\n3";
        $this->assertEquals($expected, Toon::encode('numbers', $array));
    }

    public function testEncodesUniformArrayOfObjects(): void
    {
        $data = [
            ['id' => 1, 'name' => 'Alice', 'role' => 'admin'],
            ['id' => 2, 'name' => 'Bob', 'role' => 'user'],
        ];

        $expected = "users[2]{id,name,role}:\n1,Alice,admin\n2,Bob,user";
        $this->assertEquals($expected, Toon::encode('users', $data));
    }

    public function testEncodesNonUniformArrayOfObjects(): void
    {
        $data = [
            ['id' => 1, 'name' => 'Alice'],
            ['id' => 2, 'role' => 'user']
        ];

        $expected = "users[2]{id,name,role}:\n1,Alice,\n2,,user";
        $this->assertEquals($expected, Toon::encode('users', $data));
    }

    public function testEncodesNestedAssociativeObject(): void
    {
        $data = [
            'id' => 1,
            'profile' => [
                'name' => 'Alice',
                'skills' => ['PHP', 'Go']
            ]
        ];

        $output = Toon::encode('user', $data);

        $this->assertStringContainsString('user:', $output);
        $this->assertStringContainsString('id:1', $output);
        $this->assertStringContainsString('profile{', $output);
        $this->assertStringContainsString('name=Alice', $output);
        $this->assertStringContainsString('skills=[PHP|Go]', $output);
    }

    public function testEncodesObjectWithMixedValues(): void
    {
        $data = [
            'meta' => ['a' => 1, 'b' => 2],
            'tags' => ['x', 'y'],
        ];

        $output = Toon::encode('data', $data);
        $this->assertStringContainsString('meta{a=1;b=2}', $output);
        $this->assertStringContainsString('tags[2]{value}:', $output);
        $this->assertStringContainsString('x', $output);
        $this->assertStringContainsString('y', $output);
    }

    public function testHandlesEscapedDelimitersCorrectly(): void
    {
        $data = [['text' => 'Hello,World']];
        $encoded = Toon::encode('msg', $data);

        $this->assertStringContainsString('Hello\\,World', $encoded);
    }

    public function testUsesCustomDelimiter(): void
    {
        $data = [['a' => 1, 'b' => 2]];
        $expected = "pairs[1]{a;b}:\n1;2";
        $this->assertEquals($expected, Toon::encode('pairs', $data, ';'));
    }

    public function testThrowsOnUnsupportedStructure(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $stream = fopen('php://memory', 'r');
        Toon::encode('stream', $stream);
    }

    public function testEncodeValueHandlesArraysProperly(): void
    {
        $class = new ReflectionClass(Toon::class);
        $method = $class->getMethod('encodeValue');

        $list = ['A', 'B', 'C'];
        $resultList = $method->invoke(null, 'key', $list, ',');
        $this->assertEquals('[A|B|C]', $resultList);

        $assoc = ['x' => 1, 'y' => 2];
        $resultAssoc = $method->invoke(null, 'key', $assoc, ',');
        $this->assertEquals('{x=1;y=2}', $resultAssoc);

        $scalar = 'Hello';
        $resultScalar = $method->invoke(null, 'key', $scalar, ',');
        $this->assertEquals('Hello', $resultScalar);
    }
    
    public function testLooksLikeJsonHandlesEmptyString(): void
    {
        $class = new ReflectionClass(Toon::class);
        $method = $class->getMethod('looksLikeJson');

        $this->assertFalse($method->invoke(null, ''));

        $this->assertFalse($method->invoke(null, '   '));

        $this->assertTrue($method->invoke(null, '{"a":1}'));
        $this->assertTrue($method->invoke(null, '[1,2,3]'));

        $this->assertFalse($method->invoke(null, 'notjson'));
    }

    public function testEncodeValueHandlesNestedAssociativeArrays(): void
    {
        $class = new ReflectionClass(Toon::class);
        $method = $class->getMethod('encodeValue');

        $nested = ['outer' => ['inner' => ['x' => 1, 'y' => 2]]];
        $result = $method->invoke(null, 'key', $nested['outer'], ',');

        $this->assertStringContainsString('{inner={x=1;y=2}}', $result);
    }
}
