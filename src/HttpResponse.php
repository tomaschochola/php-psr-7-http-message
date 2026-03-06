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
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;

/**
 * @no-named-arguments
 */
readonly class HttpResponse extends HttpMessage implements ResponseInterface
{
    protected readonly int $code;

    protected readonly string $reasonPhrase;

    public function __construct(StreamInterface $stream, HttpHeaders $headers, string $protocolVersion, int $code, string $reasonPhrase)
    {
        parent::__construct($stream, $headers, $protocolVersion);

        $this->code = $code;
        $this->reasonPhrase = $reasonPhrase;
    }

    #[Override]
    public function getReasonPhrase(): string
    {
        return $this->reasonPhrase;
    }

    #[Override]
    public function getStatusCode(): int
    {
        return $this->code;
    }

    #[Override]
    public function withStatus(int $code, string $reasonPhrase = ''): static
    {
        return clone ($this, [
            'code' => $code,
            'reasonPhrase' => $reasonPhrase,
        ]);
    }
}
