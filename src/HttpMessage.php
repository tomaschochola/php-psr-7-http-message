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

use Override;
use Psr\Http\Message\MessageInterface;
use Psr\Http\Message\StreamInterface;

use function implode;

/**
 * @no-named-arguments
 */
readonly class HttpMessage implements MessageInterface
{
    protected readonly HttpHeaders $headers;

    protected readonly string $protocolVersion;

    protected readonly StreamInterface $stream;

    public function __construct(StreamInterface $stream, HttpHeaders $headers, string $protocolVersion)
    {
        $this->stream = $stream;
        $this->headers = $headers;
        $this->protocolVersion = $protocolVersion;
    }

    #[Override]
    public function getBody(): StreamInterface
    {
        return $this->stream;
    }

    #[Override]
    public function getHeader(string $name): array
    {
        return $this->headers->get($name);
    }

    #[Override]
    public function getHeaderLine(string $name): string
    {
        return implode(',', $this->headers->get($name));
    }

    #[Override]
    public function getHeaders(): array
    {
        return $this->headers->all();
    }

    #[Override]
    public function getProtocolVersion(): string
    {
        return $this->protocolVersion;
    }

    #[Override]
    public function hasHeader(string $name): bool
    {
        return $this->headers->has($name);
    }

    #[Override]
    public function withAddedHeader(string $name, mixed $value): static
    {
        return clone ($this, [
            'headers' => $this->headers->add($name, $value),
        ]);
    }

    #[Override]
    public function withBody(StreamInterface $body): static
    {
        return clone ($this, [
            'stream' => $body,
        ]);
    }

    #[Override]
    public function withHeader(string $name, mixed $value): static
    {
        return clone ($this, [
            'headers' => $this->headers->set($name, $value),
        ]);
    }

    #[Override]
    public function withProtocolVersion(string $version): static
    {
        return clone ($this, [
            'protocolVersion' => $version,
        ]);
    }

    #[Override]
    public function withoutHeader(string $name): static
    {
        return clone ($this, [
            'headers' => $this->headers->remove($name),
        ]);
    }
}
