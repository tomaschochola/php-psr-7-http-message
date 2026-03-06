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
     * @var array<int|string, mixed>
     */
    protected readonly array $attributes;

    /**
     * @var array<int|string, mixed>
     */
    protected readonly array $cookieParams;

    /**
     * @var array<int|string, mixed>|object|null
     */
    protected readonly array|object|null $parsedBody;

    /**
     * @var array<int|string, mixed>
     */
    protected readonly array $queryParams;

    /**
     * @var array<int|string, mixed>
     */
    protected readonly array $serverParams;

    /**
     * @var array<int|string, mixed>
     */
    protected readonly array $uploadedFiles;

    /**
     * @param array<int|string, mixed> $serverParams
     * @param array<int|string, mixed> $cookieParams
     * @param array<int|string, mixed> $queryParams
     * @param array<int|string, mixed> $uploadedFiles
     * @param array<int|string, mixed>|object|null $parsedBody
     * @param array<int|string, mixed> $attributes
     */
    public function __construct(
        StreamInterface $stream,
        HttpHeaders $headers,
        string $protocolVersion,
        string $method,
        UriInterface $uri,
        string $requestTarget,
        array $serverParams,
        array $cookieParams,
        array $queryParams,
        array $uploadedFiles,
        array|object|null $parsedBody,
        array $attributes,
    ) {
        parent::__construct($stream, $headers, $protocolVersion, $method, $uri, $requestTarget);

        $this->serverParams = $serverParams;
        $this->cookieParams = $cookieParams;
        $this->queryParams = $queryParams;
        $this->uploadedFiles = $uploadedFiles;
        $this->parsedBody = $parsedBody;
        $this->attributes = $attributes;
    }

    #[Override]
    public function getAttribute(string $name, mixed $default = null): mixed
    {
        $value = $this->attributes[$name] ?? null;

        if ($value !== null || array_key_exists($name, $this->attributes)) {
            return $value;
        }

        return $default;
    }

    /**
     * @return array<int|string, mixed>
     */
    #[Override]
    public function getAttributes(): array
    {
        return $this->attributes;
    }

    /**
     * @return array<int|string, mixed>
     */
    #[Override]
    public function getCookieParams(): array
    {
        return $this->cookieParams;
    }

    /**
     * @return array<int|string, mixed>|object|null
     */
    #[Override]
    public function getParsedBody()
    {
        return $this->parsedBody;
    }

    /**
     * @return array<int|string, mixed>
     */
    #[Override]
    public function getQueryParams(): array
    {
        return $this->queryParams;
    }

    /**
     * @return array<int|string, mixed>
     */
    #[Override]
    public function getServerParams(): array
    {
        return $this->serverParams;
    }

    /**
     * @return array<int|string, mixed>
     */
    #[Override]
    public function getUploadedFiles(): array
    {
        return $this->uploadedFiles;
    }

    #[Override]
    public function withAttribute(string $name, mixed $value): static
    {
        $clone = $this->attributes;

        $clone[$name] = $value;

        return clone ($this, [
            'attributes' => $clone,
        ]);
    }

    /**
     * @param array<int|string, mixed> $cookies
     */
    #[Override]
    public function withCookieParams(array $cookies): static
    {
        return clone ($this, [
            'cookieParams' => $cookies,
        ]);
    }

    /**
     * @param array<int|string, mixed>|object|null $data
     */
    #[Override]
    public function withParsedBody(mixed $data): static
    {
        return clone ($this, [
            'parsedBody' => $data,
        ]);
    }

    /**
     * @param array<int|string, mixed> $query
     */
    #[Override]
    public function withQueryParams(array $query): static
    {
        return clone ($this, [
            'queryParams' => $query,
        ]);
    }

    /**
     * @param array<int|string, mixed> $uploadedFiles
     */
    #[Override]
    public function withUploadedFiles(array $uploadedFiles): static
    {
        return clone ($this, [
            'uploadedFiles' => $uploadedFiles,
        ]);
    }

    #[Override]
    public function withoutAttribute(string $name): static
    {
        $clone = $this->attributes;

        unset($clone[$name]);

        return clone ($this, [
            'attributes' => $clone,
        ]);
    }
}
