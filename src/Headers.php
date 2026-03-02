<?php

/**
 * @author Tomáš Chochola <tomaschochola@tomaschochola.cz>
 * @copyright © 2026 Tomáš Chochola <tomaschochola@tomaschochola.cz>
 *
 * @license CC-BY-ND-4.0
 *
 * @see {@link https://creativecommons.org/licenses/by-nd/4.0/} License
 * @see {@link https://github.com/tomaschochola} GitHub Profile
 * @see {@link https://github.com/sponsors/tomaschochola} GitHub Sponsors
 */

declare(strict_types=1);

namespace TomasChochola\Psr\Http\Message;

use function array_merge;
use function array_merge_recursive;
use function array_replace;
use function strtr;

/**
 * @no-named-arguments
 */
readonly class Headers
{
    /**
     * @var array<int|string, array<int|string, list<string>>>
     */
    protected readonly array $headers;

    /**
     * @param array<int|string, array<int|string, list<string>>> $headers
     */
    public function __construct(array $headers)
    {
        $this->headers = $headers;
    }

    /**
     * @param array<int|string, string>|string $value
     */
    public function add(string $key, array|string $value): static
    {
        // @phpstan-ignore-next-line assign.propertyType
        return clone ($this, [
            'headers' => array_merge_recursive($this->headers, [
                static::key($key) => [
                    $key => (array) $value,
                ],
            ]),
        ]);
    }

    /**
     * @return array<int|string, list<string>>
     */
    public function all(): array
    {
        $result = [];

        foreach ($this->headers as $bucket) {
            foreach ($bucket as $key => $values) {
                $result[$key] = $values;
            }
        }

        return $result;
    }

    /**
     * @return array<int|string, string>
     */
    public function get(string $key): array
    {
        $bucket = $this->headers[static::key($key)] ?? null;

        if ($bucket === null) {
            return [];
        }

        $result = [];

        foreach ($bucket as $values) {
            $result = array_merge($result, $values);
        }

        return $result;
    }

    public function has(string $key): bool
    {
        return isset($this->headers[static::key($key)]);
    }

    public function remove(string $key): static
    {
        $clone = $this->headers;

        unset($clone[static::key($key)]);

        return clone ($this, [
            'headers' => $clone,
        ]);
    }

    /**
     * @param array<int|string, string>|string $value
     */
    public function set(string $key, array|string $value): static
    {
        // @phpstan-ignore-next-line assign.propertyType
        return clone ($this, [
            'headers' => array_replace($this->headers, [
                static::key($key) => [
                    $key => (array) $value,
                ],
            ]),
        ]);
    }

    protected static function key(string $key): string
    {
        return strtr($key, '-ABCDEFGHIJKLMNOPQRSTUVWXYZ', '_abcdefghijklmnopqrstuvwxyz');
    }
}
