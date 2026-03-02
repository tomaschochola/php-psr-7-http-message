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
use Psr\Http\Message\UriInterface;
use Uri\Rfc3986\Uri as RfcUri;

/**
 * @no-named-arguments
 */
readonly class Uri implements UriInterface
{
    protected readonly RfcUri $uri;

    public function __construct(
        RfcUri $uri,
    ) {
        $this->uri = $uri;
    }

    #[Override]
    public function __toString(): string
    {
        return $this->uri->toString();
    }

    #[Override]
    public function getAuthority(): string
    {
        $authority = '';

        $userinfo = $this->getUserInfo();

        if ($userinfo !== '') {
            $authority .= $userinfo . '@';
        }

        $authority .= $this->getHost();

        $port = $this->getPort();

        if ($port !== null) {
            $authority .= ':' . $port;
        }

        return $authority;
    }

    #[Override]
    public function getFragment(): string
    {
        return $this->uri->getFragment() ?? '';
    }

    #[Override]
    public function getHost(): string
    {
        return $this->uri->getHost() ?? '';
    }

    #[Override]
    public function getPath(): string
    {
        return $this->uri->getPath();
    }

    #[Override]
    public function getPort(): int|null
    {
        return $this->uri->getPort() ?? null;
    }

    #[Override]
    public function getQuery(): string
    {
        return $this->uri->getQuery() ?? '';
    }

    #[Override]
    public function getScheme(): string
    {
        return $this->uri->getScheme() ?? '';
    }

    #[Override]
    public function getUserInfo(): string
    {
        return $this->uri->getUserInfo() ?? '';
    }

    #[Override]
    public function withFragment(string $fragment): static
    {
        return clone ($this, [
            'uri' => $this->uri->withFragment($fragment),
        ]);
    }

    #[Override]
    public function withHost(string $host): static
    {
        return clone ($this, [
            'uri' => $this->uri->withHost($host),
        ]);
    }

    #[Override]
    public function withPath(string $path): static
    {
        return clone ($this, [
            'uri' => $this->uri->withPath($path),
        ]);
    }

    #[Override]
    public function withPort(int|null $port): static
    {
        return clone ($this, [
            'uri' => $this->uri->withPort($port),
        ]);
    }

    #[Override]
    public function withQuery(string $query): static
    {
        return clone ($this, [
            'uri' => $this->uri->withQuery($query),
        ]);
    }

    #[Override]
    public function withScheme(string $scheme): static
    {
        return clone ($this, [
            'uri' => $this->uri->withScheme($scheme),
        ]);
    }

    #[Override]
    public function withUserInfo(string $user, string|null $password = null): static
    {
        return clone ($this, [
            'uri' => $this->uri->withUserInfo($password === null ? $user : ($user . ':' . $password)),
        ]);
    }
}
