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

use function fopen;
use function is_uploaded_file;
use function move_uploaded_file;
use function rename;

use const UPLOAD_ERR_OK;

/**
 * @no-named-arguments
 */
readonly class HttpUploadedFile implements UploadedFileInterface
{
    private readonly string|null $clientFilename;

    private readonly string|null $clientMediaType;

    private readonly int $error;

    /**
     * @var object{moved: bool}
     */
    private readonly object $state;

    private readonly int|null $size;

    private readonly string $tmpName;

    public function __construct(string $tmpName, int|null $size, int $error, string|null $clientFilename = null, string|null $clientMediaType = null)
    {
        $this->tmpName = $tmpName;
        $this->size = $size;
        $this->error = $error;
        $this->clientFilename = $clientFilename;
        $this->clientMediaType = $clientMediaType;
        $this->state = (object) ['moved' => false];
    }

    #[NoDiscard]
    #[Override]
    public function getClientFilename(): string|null
    {
        return $this->clientFilename;
    }

    #[NoDiscard]
    #[Override]
    public function getClientMediaType(): string|null
    {
        return $this->clientMediaType;
    }

    #[NoDiscard]
    #[Override]
    public function getError(): int
    {
        return $this->error;
    }

    #[NoDiscard]
    #[Override]
    public function getSize(): int|null
    {
        return $this->size;
    }

    #[NoDiscard]
    #[Override]
    public function getStream(): StreamInterface
    {
        if ($this->state->moved) {
            throw new RuntimeException('$this->state->moved');
        }

        if ($this->error !== UPLOAD_ERR_OK) {
            throw new RuntimeException('$this->error');
        }

        $resource = fopen($this->tmpName, 'rb');

        if ($resource === false) {
            throw new UnexpectedValueException('fopen');
        }

        return new HttpStream($resource);
    }

    #[Override]
    public function moveTo(string $targetPath): void
    {
        if ($this->state->moved) {
            throw new RuntimeException('$this->state->moved');
        }

        if ($this->error !== UPLOAD_ERR_OK) {
            throw new RuntimeException('$this->error');
        }

        if (is_uploaded_file($this->tmpName)) {
            if (!move_uploaded_file($this->tmpName, $targetPath)) {
                throw new UnexpectedValueException('move_uploaded_file');
            }
        } elseif (!rename($this->tmpName, $targetPath)) {
            throw new UnexpectedValueException('rename');
        }

        $this->state->moved = true;
    }
}
