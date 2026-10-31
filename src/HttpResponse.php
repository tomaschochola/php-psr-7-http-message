<?php

declare(strict_types=1);

namespace TomasChochola\Psr\Http\Message;

use NoDiscard;
use Override;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;

/**
 * @no-named-arguments
 */
readonly class HttpResponse extends HttpMessage implements ResponseInterface
{
    private int $code;

    private string $reasonPhrase;

    public function __construct(StreamInterface $stream, HttpHeaders $headers, string $protocolVersion, int $code, string $reasonPhrase)
    {
        parent::__construct($stream, $headers, $protocolVersion);

        $this->code = $code;
        $this->reasonPhrase = $reasonPhrase;
    }

    #[NoDiscard()]
    #[Override()]
    public function getReasonPhrase(): string
    {
        return $this->reasonPhrase;
    }

    #[NoDiscard()]
    #[Override()]
    public function getStatusCode(): int
    {
        return $this->code;
    }

    #[NoDiscard()]
    #[Override()]
    public function withStatus(int $code, string $reasonPhrase = ''): static
    {
        return clone ($this, [
            'code' => $code,
            'reasonPhrase' => $reasonPhrase,
        ]);
    }
}
