<?php

namespace Fontebasso\JsonToToon;

use InvalidArgumentException;

class Toon
{
    /**
     * Converts a JSON string, PHP array or primitive value into TOON format.
     *
     * @param string $label      Logical dataset name (e.g. "users")
     * @param mixed  $json       JSON string or PHP data
     * @param string $delimiter  Delimiter between values (default: ',')
     * @return string            The TOON representation
     *
     * @throws InvalidArgumentException
     */
    public static function encode(string $label, mixed $json, string $delimiter = ','): string
    {
        $data = $json;

        if (is_string($json) && self::looksLikeJson($json)) {
            $data = json_decode($json, true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new InvalidArgumentException('Invalid JSON: ' . json_last_error_msg());
            }
        }

        if (is_scalar($data) || $data === null) {
            if (is_bool($data)) {
                $data = $data ? 'true' : 'false';
            }
            return sprintf('%s:%s', $label, self::escapeValue((string)$data, $delimiter));
        }

        if (empty($data)) {
            return sprintf('%s[0]{}:', $label);
        }

        if (is_array($data) && array_is_list($data)) {
            if (!is_array($data[0])) {
                $count = count($data);
                $header = sprintf('%s[%d]{value}:', $label, $count);
                $lines = array_map(fn($v) => self::escapeValue((string)$v, $delimiter), $data);
                return $header . PHP_EOL . implode(PHP_EOL, $lines);
            }

            $columns = [];
            foreach ($data as $row) {
                if (is_array($row)) {
                    $columns = array_unique(array_merge($columns, array_keys($row)));
                }
            }

            $count = count($data);
            $header = sprintf('%s[%d]{%s}:', $label, $count, implode($delimiter, $columns));

            $lines = [];
            foreach ($data as $row) {
                $values = [];
                foreach ($columns as $col) {
                    $value = $row[$col] ?? '';
                    $values[] = self::encodeValue($col, $value, $delimiter);
                }
                $lines[] = implode($delimiter, $values);
            }

            return $header . PHP_EOL . implode(PHP_EOL, $lines);
        }

        if (is_array($data) && !array_is_list($data)) {
            $lines = [];
            foreach ($data as $key => $value) {
                if (is_array($value)) {
                    if (array_is_list($value)) {
                        $count = count($value);
                        $lines[] = sprintf(
                            '%s[%d]{value}:' . PHP_EOL . implode(PHP_EOL, array_map(fn($v) => self::escapeValue((string)$v, $delimiter), $value)),
                            $key,
                            $count
                        );
                    } else {
                        $pairs = implode(';', array_map(
                            fn($k, $v) => sprintf(
                                '%s=%s',
                                $k,
                                is_array($v)
                                    ? self::encodeValue($k, $v, $delimiter)
                                    : self::escapeValue((string)$v, $delimiter)
                            ),
                            array_keys($value),
                            $value
                        ));
                        $lines[] = sprintf('%s{%s}', $key, $pairs);
                    }
                } else {
                    $escaped = self::escapeValue((string)$value, $delimiter);
                    $lines[] = sprintf('%s:%s', $key, $escaped);
                }
            }
            return $label . ':' . PHP_EOL . implode(PHP_EOL, $lines);
        }

        throw new InvalidArgumentException('Unsupported data structure for TOON encoding.');
    }

    private static function escapeValue(string $value, string $delimiter): string
    {
        return str_replace([$delimiter, "\n", "\r"], ['\\' . $delimiter, ' ', ' '], $value);
    }

    private static function encodeValue(string $key, mixed $value, string $delimiter): string
    {
        if (is_array($value)) {
            if (array_is_list($value)) {
                return '[' . implode('|', array_map(
                    fn($v) => self::escapeValue((string)$v, $delimiter),
                    $value
                )) . ']';
            }

            $pairs = [];
            foreach ($value as $k => $v) {
                if (is_array($v)) {
                    $pairs[] = sprintf(
                        '%s=%s',
                        $k,
                        self::encodeValue($k, $v, $delimiter)
                    );
                } else {
                    $pairs[] = sprintf(
                        '%s=%s',
                        $k,
                        self::escapeValue((string)$v, $delimiter)
                    );
                }
            }
            return '{' . implode(';', $pairs) . '}';
        }

        return self::escapeValue((string)$value, $delimiter);
    }

    private static function looksLikeJson(string $string): bool
    {
        $string = trim($string);
        if ($string === '') {
            return false;
        }
        return (
            ($string[0] === '{' && str_ends_with($string, '}')) ||
            ($string[0] === '[' && str_ends_with($string, ']'))
        );
    }
}
