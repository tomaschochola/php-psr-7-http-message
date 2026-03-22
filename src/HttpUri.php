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
use Psr\Http\Message\UriInterface;
use Stringable;
use Uri\Rfc3986\Uri as RfcUri;

/**
 * @no-named-arguments
 */
readonly class HttpUri implements Stringable, UriInterface
{
    private readonly RfcUri $uri;

    public function __construct(RfcUri $uri)
    {
        $this->uri = $uri;
    }

    #[NoDiscard]
    #[Override]
    public function __toString(): string
    {
        return $this->uri->toString();
    }

    #[NoDiscard]
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

    #[NoDiscard]
    #[Override]
    public function getFragment(): string
    {
        return $this->uri->getFragment() ?? '';
    }

    #[NoDiscard]
    #[Override]
    public function getHost(): string
    {
        return $this->uri->getHost() ?? '';
    }

    #[NoDiscard]
    #[Override]
    public function getPath(): string
    {
        return $this->uri->getPath();
    }

    #[NoDiscard]
    #[Override]
    public function getPort(): int|null
    {
        return $this->uri->getPort() ?? null;
    }

    #[NoDiscard]
    #[Override]
    public function getQuery(): string
    {
        return $this->uri->getQuery() ?? '';
    }

    #[NoDiscard]
    #[Override]
    public function getScheme(): string
    {
        return $this->uri->getScheme() ?? '';
    }

    #[NoDiscard]
    #[Override]
    public function getUserInfo(): string
    {
        return $this->uri->getUserInfo() ?? '';
    }

    #[NoDiscard]
    #[Override]
    public function withFragment(string $fragment): static
    {
        return clone ($this, [
            'uri' => $this->uri->withFragment($fragment),
        ]);
    }

    #[NoDiscard]
    #[Override]
    public function withHost(string $host): static
    {
        return clone ($this, [
            'uri' => $this->uri->withHost($host),
        ]);
    }

    #[NoDiscard]
    #[Override]
    public function withPath(string $path): static
    {
        return clone ($this, [
            'uri' => $this->uri->withPath($path),
        ]);
    }

    #[NoDiscard]
    #[Override]
    public function withPort(int|null $port): static
    {
        return clone ($this, [
            'uri' => $this->uri->withPort($port),
        ]);
    }

    #[NoDiscard]
    #[Override]
    public function withQuery(string $query): static
    {
        return clone ($this, [
            'uri' => $this->uri->withQuery($query),
        ]);
    }

    #[NoDiscard]
    #[Override]
    public function withScheme(string $scheme): static
    {
        return clone ($this, [
            'uri' => $this->uri->withScheme($scheme),
        ]);
    }

    #[NoDiscard]
    #[Override]
    public function withUserInfo(string $user, string|null $password = null): static
    {
        return clone ($this, [
            'uri' => $this->uri->withUserInfo($password === null ? $user : ($user . ':' . $password)),
        ]);
    }
}
