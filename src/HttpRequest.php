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
use Override;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\StreamInterface;
use Psr\Http\Message\UriInterface;

/**
 * @no-named-arguments
 */
readonly class HttpRequest extends HttpMessage implements RequestInterface
{
    private readonly string $method;

    private readonly string $requestTarget;

    private readonly UriInterface $uri;

    public function __construct(StreamInterface $stream, HttpHeaders $headers, string $protocolVersion, string $method, UriInterface $uri, string $requestTarget)
    {
        parent::__construct($stream, $headers, $protocolVersion);

        $this->method = $method;
        $this->uri = $uri;
        $this->requestTarget = $requestTarget;
    }

    #[NoDiscard]
    #[Override]
    public function getMethod(): string
    {
        return $this->method;
    }

    #[NoDiscard]
    #[Override]
    public function getRequestTarget(): string
    {
        if ($this->requestTarget !== '') {
            return $this->requestTarget;
        }

        $target = $this->uri->getPath();

        if ($target === '') {
            $target = '/';
        }

        $query = $this->uri->getQuery();

        if ($query !== '') {
            $target .= '?' . $query;
        }

        return $target;
    }

    #[NoDiscard]
    #[Override]
    public function getUri(): UriInterface
    {
        return $this->uri;
    }

    #[NoDiscard]
    #[Override]
    public function withMethod(string $method): static
    {
        return clone ($this, [
            'method' => $method,
        ]);
    }

    #[NoDiscard]
    #[Override]
    public function withRequestTarget(string $requestTarget): static
    {
        return clone ($this, [
            'requestTarget' => $requestTarget,
        ]);
    }

    #[NoDiscard]
    #[Override]
    public function withUri(UriInterface $uri, bool $preserveHost = false): static
    {
        if ($preserveHost) {
            $uri = $uri->withHost($this->uri->getHost());
        }

        return clone ($this, [
            'uri' => $uri,
        ]);
    }
}
