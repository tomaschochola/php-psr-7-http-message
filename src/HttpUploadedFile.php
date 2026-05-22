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
use Psr\Http\Message\StreamInterface;
use Psr\Http\Message\UploadedFileInterface;
use RuntimeException;
use UnexpectedValueException;

use function fclose;
use function fopen;
use function fwrite;
use function is_resource;
use function mb_strlen;

use const UPLOAD_ERR_OK;

/**
 * @no-named-arguments
 */
readonly class HttpUploadedFile implements UploadedFileInterface
{
    private string | null $clientFilename;

    private string | null $clientMediaType;

    private int $error;

    private int | null $size;

    /**
     * @var object{moved: bool}
     */
    private object $state;

    private StreamInterface $stream;

    public function __construct(StreamInterface $stream, int | null $size, int $error, string | null $clientFilename = null, string | null $clientMediaType = null)
    {
        $this->stream = $stream;
        $this->size = $size;
        $this->error = $error;
        $this->clientFilename = $clientFilename;
        $this->clientMediaType = $clientMediaType;
        $this->state = (object) ['moved' => false];
    }

    #[NoDiscard()]
    #[Override()]
    public function getClientFilename(): string | null
    {
        return $this->clientFilename;
    }

    #[NoDiscard()]
    #[Override()]
    public function getClientMediaType(): string | null
    {
        return $this->clientMediaType;
    }

    #[NoDiscard()]
    #[Override()]
    public function getError(): int
    {
        return $this->error;
    }

    #[NoDiscard()]
    #[Override()]
    public function getSize(): int | null
    {
        return $this->size;
    }

    #[NoDiscard()]
    #[Override()]
    public function getStream(): StreamInterface
    {
        if ($this->state->moved) {
            throw new RuntimeException('$this->state->moved');
        }

        if ($this->error !== UPLOAD_ERR_OK) {
            throw new RuntimeException('$this->error');
        }

        return $this->stream;
    }

    #[Override()]
    public function moveTo(string $targetPath): void
    {
        if ($this->state->moved) {
            throw new RuntimeException('$this->state->moved');
        }

        if ($this->error !== UPLOAD_ERR_OK) {
            throw new RuntimeException('$this->error');
        }

        if ($this->stream->isSeekable()) {
            $this->stream->rewind();
        }

        $target = fopen($targetPath, 'wb');

        if (!is_resource($target)) {
            throw new UnexpectedValueException('fopen');
        }

        while (!$this->stream->eof()) {
            $chunk = $this->stream->read(524_288);

            if (fwrite($target, $chunk) !== mb_strlen($chunk, '8bit')) {
                throw new UnexpectedValueException('fwrite');
            }
        }

        if (!fclose($target)) {
            throw new UnexpectedValueException('fclose');
        }

        $this->state->moved = true;
    }
}
