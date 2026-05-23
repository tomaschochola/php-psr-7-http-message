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
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\StreamInterface;
use Psr\Http\Message\UriInterface;

use function array_key_exists;

/**
 * @no-named-arguments
 */
readonly class HttpServerRequest extends HttpRequest implements ServerRequestInterface
{
    /**
     * @var array<mixed, mixed>
     */
    private array $attributes;

    /**
     * @var array<mixed, mixed>
     */
    private array $cookieParams;

    /**
     * @var array<mixed, mixed>|object|null
     */
    private array | object | null $parsedBody;

    /**
     * @var array<mixed, mixed>
     */
    private array $queryParams;

    /**
     * @var array<mixed, mixed>
     */
    private array $serverParams;

    /**
     * @var array<mixed, mixed>
     */
    private array $uploadedFiles;

    /**
     * @param array<mixed, mixed> $serverParams
     * @param array<mixed, mixed> $cookieParams
     * @param array<mixed, mixed> $queryParams
     * @param array<mixed, mixed> $uploadedFiles
     * @param array<mixed, mixed>|object|null $parsedBody
     * @param array<mixed, mixed> $attributes
     */
    public function __construct(StreamInterface $stream, HttpHeaders $headers, string $protocolVersion, string $method, UriInterface $uri, string $requestTarget, array $serverParams, array $cookieParams, array $queryParams, array $uploadedFiles, array | object | null $parsedBody, array $attributes)
    {
        parent::__construct($stream, $headers, $protocolVersion, $method, $uri, $requestTarget);

        $this->serverParams = $serverParams;
        $this->cookieParams = $cookieParams;
        $this->queryParams = $queryParams;
        $this->uploadedFiles = $uploadedFiles;
        $this->parsedBody = $parsedBody;
        $this->attributes = $attributes;
    }

    #[NoDiscard()]
    #[Override()]
    public function getAttribute(string $name, mixed $default = null): mixed
    {
        $value = $this->attributes[$name] ?? null;

        if ($value !== null || array_key_exists($name, $this->attributes)) {
            return $value;
        }

        return $default;
    }

    /**
     * @return array<mixed, mixed>
     */
    #[NoDiscard()]
    #[Override()]
    public function getAttributes(): array
    {
        return $this->attributes;
    }

    /**
     * @return array<mixed, mixed>
     */
    #[NoDiscard()]
    #[Override()]
    public function getCookieParams(): array
    {
        return $this->cookieParams;
    }

    /**
     * @return array<mixed, mixed>|object|null
     */
    #[NoDiscard()]
    #[Override()]
    public function getParsedBody()
    {
        return $this->parsedBody;
    }

    /**
     * @return array<mixed, mixed>
     */
    #[NoDiscard()]
    #[Override()]
    public function getQueryParams(): array
    {
        return $this->queryParams;
    }

    /**
     * @return array<mixed, mixed>
     */
    #[NoDiscard()]
    #[Override()]
    public function getServerParams(): array
    {
        return $this->serverParams;
    }

    /**
     * @return array<mixed, mixed>
     */
    #[NoDiscard()]
    #[Override()]
    public function getUploadedFiles(): array
    {
        return $this->uploadedFiles;
    }

    #[NoDiscard()]
    #[Override()]
    public function withAttribute(string $name, mixed $value): static
    {
        $clone = $this->attributes;
        $clone[$name] = $value;

        return clone ($this, [
            'attributes' => $clone,
        ]);
    }

    /**
     * @param array<mixed, mixed> $cookies
     */
    #[NoDiscard()]
    #[Override()]
    public function withCookieParams(array $cookies): static
    {
        return clone ($this, [
            'cookieParams' => $cookies,
        ]);
    }

    /**
     * @param array<mixed, mixed>|object|null $data
     */
    #[NoDiscard()]
    #[Override()]
    public function withParsedBody(mixed $data): static
    {
        return clone ($this, [
            'parsedBody' => $data,
        ]);
    }

    /**
     * @param array<mixed, mixed> $query
     */
    #[NoDiscard()]
    #[Override()]
    public function withQueryParams(array $query): static
    {
        return clone ($this, [
            'queryParams' => $query,
        ]);
    }

    /**
     * @param array<mixed, mixed> $uploadedFiles
     */
    #[NoDiscard()]
    #[Override()]
    public function withUploadedFiles(array $uploadedFiles): static
    {
        return clone ($this, [
            'uploadedFiles' => $uploadedFiles,
        ]);
    }

    #[NoDiscard()]
    #[Override()]
    public function withoutAttribute(string $name): static
    {
        $clone = $this->attributes;
        unset($clone[$name]);

        return clone ($this, [
            'attributes' => $clone,
        ]);
    }
}
