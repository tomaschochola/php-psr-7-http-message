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

use NoDiscard;

use function array_merge;
use function array_merge_recursive;
use function array_replace;
use function strtr;

/**
 * @internal
 *
 * @no-named-arguments
 */
readonly class HttpHeaders
{
    /**
     * @var array<mixed, array<mixed, list<string>>>
     */
    private readonly array $headers;

    /**
     * @param array<mixed, array<mixed, list<string>>> $headers
     */
    public function __construct(array $headers)
    {
        $this->headers = $headers;
    }

    /**
     * @param array<mixed, string>|string $value
     */
    #[NoDiscard]
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
     * @return array<mixed, list<string>>
     */
    #[NoDiscard]
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
     * @return array<mixed, string>
     */
    #[NoDiscard]
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

    #[NoDiscard]
    public function has(string $key): bool
    {
        return isset($this->headers[static::key($key)]);
    }

    #[NoDiscard]
    public function remove(string $key): static
    {
        $clone = $this->headers;

        unset($clone[static::key($key)]);

        return clone ($this, [
            'headers' => $clone,
        ]);
    }

    /**
     * @param array<mixed, string>|string $value
     */
    #[NoDiscard]
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

    #[NoDiscard]
    private static function key(string $key): string
    {
        return strtr($key, 'ABCDEFGHIJKLMNOPQRSTUVWXYZ', 'abcdefghijklmnopqrstuvwxyz');
    }
}
