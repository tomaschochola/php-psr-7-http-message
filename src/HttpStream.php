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
use UnexpectedValueException;

use function fclose;
use function feof;
use function fread;
use function fseek;
use function fstat;
use function ftell;
use function fwrite;
use function in_array;
use function is_int;
use function is_resource;
use function is_string;
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
     * @var object{current?: resource}
     */
    private readonly object $resource;

    /**
     * @param resource $resource
     */
    public function __construct(mixed $resource)
    {
        if (!is_resource($resource)) {
            throw new InvalidArgumentException('$resource');
        }

        $this->resource = new class($resource) {
            /**
             * @var resource
             */
            public mixed $current;

            /**
             * @param resource $current
             */
            public function __construct(mixed $current)
            {
                $this->current = $current;
            }
        };
    }

    public function __destruct()
    {
        try {
            if (!isset($this->resource->current)) {
                return;
            }

            $resource = $this->resource->current;

            unset($this->resource->current);

            if (is_resource($resource)) {
                fclose($resource);
            }
        } catch (Throwable) {
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
        if (!isset($this->resource->current) || !is_resource($this->resource->current)) {
            throw new UnexpectedValueException('$this->resource->current');
        }

        $resource = $this->resource->current;

        unset($this->resource->current);

        if (!fclose($resource)) {
            throw new UnexpectedValueException('fclose');
        }
    }

    /**
     * @return resource
     */
    #[NoDiscard]
    #[Override]
    public function detach(): mixed
    {
        if (!isset($this->resource->current) || !is_resource($this->resource->current)) {
            throw new UnexpectedValueException('$this->resource->current');
        }

        $resource = $this->resource->current;

        unset($this->resource->current);

        return $resource;
    }

    #[NoDiscard]
    #[Override]
    public function eof(): bool
    {
        if (!isset($this->resource->current) || !is_resource($this->resource->current)) {
            throw new UnexpectedValueException('$this->resource->current');
        }

        return feof($this->resource->current);
    }

    #[NoDiscard]
    #[Override]
    public function getContents(): string
    {
        if (!isset($this->resource->current) || !is_resource($this->resource->current)) {
            throw new UnexpectedValueException('$this->resource->current');
        }

        $content = stream_get_contents($this->resource->current);

        if (!is_string($content)) {
            throw new UnexpectedValueException('stream_get_contents');
        }

        return $content;
    }

    #[NoDiscard]
    #[Override]
    public function getMetadata(string|null $key = null): mixed
    {
        if (!isset($this->resource->current) || !is_resource($this->resource->current)) {
            throw new UnexpectedValueException('$this->resource->current');
        }

        $meta = stream_get_meta_data($this->resource->current);

        if ($key === null) {
            return $meta;
        }

        return $meta[$key] ?? null;
    }

    #[NoDiscard]
    #[Override]
    public function getSize(): int|null
    {
        if (!isset($this->resource->current) || !is_resource($this->resource->current)) {
            throw new UnexpectedValueException('$this->resource->current');
        }

        $stat = fstat($this->resource->current);

        if ($stat === false) {
            throw new UnexpectedValueException('fstat');
        }

        return $stat['size'];
    }

    #[NoDiscard]
    #[Override]
    public function isReadable(): bool
    {
        if (!isset($this->resource->current) || !is_resource($this->resource->current)) {
            throw new UnexpectedValueException('$this->resource->current');
        }

        return in_array(stream_get_meta_data($this->resource->current)['mode'], [
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
        if (!isset($this->resource->current) || !is_resource($this->resource->current)) {
            throw new UnexpectedValueException('$this->resource->current');
        }

        return stream_get_meta_data($this->resource->current)['seekable'];
    }

    #[NoDiscard]
    #[Override]
    public function isWritable(): bool
    {
        if (!isset($this->resource->current) || !is_resource($this->resource->current)) {
            throw new UnexpectedValueException('$this->resource->current');
        }

        return in_array(stream_get_meta_data($this->resource->current)['mode'], [
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
        if (!isset($this->resource->current) || !is_resource($this->resource->current)) {
            throw new UnexpectedValueException('$this->resource->current');
        }

        if ($length < 1) {
            throw new InvalidArgumentException('$length');
        }

        $value = fread($this->resource->current, $length);

        if (!is_string($value)) {
            throw new UnexpectedValueException('fread');
        }

        return $value;
    }

    #[Override]
    public function rewind(): void
    {
        if (!isset($this->resource->current) || !is_resource($this->resource->current)) {
            throw new UnexpectedValueException('$this->resource->current');
        }

        if (!rewind($this->resource->current)) {
            throw new UnexpectedValueException('rewind');
        }
    }

    #[Override]
    public function seek(int $offset, int $whence = SEEK_SET): void
    {
        if (!isset($this->resource->current) || !is_resource($this->resource->current)) {
            throw new UnexpectedValueException('$this->resource->current');
        }

        if (fseek($this->resource->current, $offset, $whence) !== 0) {
            throw new UnexpectedValueException('fseek');
        }
    }

    #[NoDiscard]
    #[Override]
    public function tell(): int
    {
        if (!isset($this->resource->current) || !is_resource($this->resource->current)) {
            throw new UnexpectedValueException('$this->resource->current');
        }

        $value = ftell($this->resource->current);

        if (!is_int($value)) {
            throw new UnexpectedValueException('ftell');
        }

        return $value;
    }

    #[NoDiscard]
    #[Override]
    public function write(string $string): int
    {
        if (!isset($this->resource->current) || !is_resource($this->resource->current)) {
            throw new UnexpectedValueException('$this->resource->current');
        }

        $value = fwrite($this->resource->current, $string);

        if (!is_int($value)) {
            throw new UnexpectedValueException('fwrite');
        }

        return $value;
    }
}
