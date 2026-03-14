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

use InvalidArgumentException;
use NoDiscard;
use Override;
use Psr\Http\Message\StreamInterface;
use Stringable;
use Throwable;

use function fclose;
use function feof;
use function fread;
use function fseek;
use function fstat;
use function ftell;
use function fwrite;
use function in_array;
use function is_resource;
use function rewind;
use function stream_get_contents;
use function stream_get_meta_data;

use const SEEK_SET;

/**
 * @no-named-arguments
 */
readonly class HttpStream implements StreamInterface, Stringable
{
    /**
     * @var resource
     */
    private readonly mixed $resource;

    /**
     * @param resource $resource
     */
    public function __construct(mixed $resource)
    {
        $this->resource = $resource;
    }

    public function __destruct()
    {
        if (is_resource($this->resource)) {
            fclose($this->resource);
        }
    }

    #[NoDiscard]
    #[Override]
    public function __toString(): string
    {
        try {
            if ($this->isSeekable()) {
                $this->rewind();
            }

            return $this->getContents();
        } catch (Throwable) {
            return '';
        }
    }

    #[Override]
    public function close(): void
    {
        fclose($this->resource);
    }

    /**
     * @return resource
     */
    #[NoDiscard]
    #[Override]
    public function detach(): mixed
    {
        return $this->resource;
    }

    #[NoDiscard]
    #[Override]
    public function eof(): bool
    {
        return feof($this->resource);
    }

    #[NoDiscard]
    #[Override]
    public function getContents(): string
    {
        $content = stream_get_contents($this->resource);

        return $content === false ? '' : $content;
    }

    #[NoDiscard]
    #[Override]
    public function getMetadata(string|null $key = null): mixed
    {
        $meta = stream_get_meta_data($this->resource);

        if ($key === null) {
            return $meta;
        }

        return $meta[$key] ?? null;
    }

    #[NoDiscard]
    #[Override]
    public function getSize(): int|null
    {
        $stat = fstat($this->resource);

        if ($stat === false) {
            return null;
        }

        return $stat['size'];
    }

    #[NoDiscard]
    #[Override]
    public function isReadable(): bool
    {
        return in_array(stream_get_meta_data($this->resource)['mode'], [
            'r', 'rb',
            'r+', 'rb+', 'r+b',
            'w+', 'wb+', 'w+b',
            'a+', 'ab+', 'a+b',
            'x+', 'xb+', 'x+b',
            'c+', 'cb+', 'c+b',
        ], true);
    }

    #[NoDiscard]
    #[Override]
    public function isSeekable(): bool
    {
        return stream_get_meta_data($this->resource)['seekable'];
    }

    #[NoDiscard]
    #[Override]
    public function isWritable(): bool
    {
        return in_array(stream_get_meta_data($this->resource)['mode'], [
            'w', 'wb',
            'w+', 'wb+', 'w+b',
            'a', 'ab',
            'a+', 'ab+', 'a+b',
            'x', 'xb',
            'x+', 'xb+', 'x+b',
            'c', 'cb',
            'c+', 'cb+', 'c+b',
            'r+', 'rb+', 'r+b',
        ], true);
    }

    #[NoDiscard]
    #[Override]
    public function read(int $length): string
    {
        if ($length < 1) {
            throw new InvalidArgumentException('$length');
        }

        $value = fread($this->resource, $length);

        return $value === false ? '' : $value;
    }

    #[Override]
    public function rewind(): void
    {
        rewind($this->resource);
    }

    #[Override]
    public function seek(int $offset, int $whence = SEEK_SET): void
    {
        fseek($this->resource, $offset, $whence);
    }

    #[NoDiscard]
    #[Override]
    public function tell(): int
    {
        $value = ftell($this->resource);

        return $value === false ? 0 : $value;
    }

    #[NoDiscard]
    #[Override]
    public function write(string $string): int
    {
        $value = fwrite($this->resource, $string);

        return $value === false ? 0 : $value;
    }
}
