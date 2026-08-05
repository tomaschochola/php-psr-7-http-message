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

namespace Tests;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Small;
use PHPUnit\Framework\Attributes\Test;
use TomasChochola\Psr\Http\Message\HttpHeaders;
use TomasChochola\Psr\Http\Message\HttpMessage;
use TomasChochola\Psr\Http\Message\HttpRequest;
use TomasChochola\Psr\Http\Message\HttpResponse;
use TomasChochola\Psr\Http\Message\HttpServerRequest;
use TomasChochola\Psr\Http\Message\HttpStream;
use TomasChochola\Psr\Http\Message\HttpUploadedFile;
use TomasChochola\Psr\Http\Message\HttpUri;
use Uri\Rfc3986\Uri;

use function file_get_contents;
use function fopen;
use function sys_get_temp_dir;
use function tempnam;
use function unlink;

use const UPLOAD_ERR_OK;

/**
 * @internal
 *
 * @no-named-arguments
 */
#[CoversClass(HttpHeaders::class)]
#[CoversClass(HttpMessage::class)]
#[CoversClass(HttpRequest::class)]
#[CoversClass(HttpResponse::class)]
#[CoversClass(HttpServerRequest::class)]
#[CoversClass(HttpStream::class)]
#[CoversClass(HttpUploadedFile::class)]
#[CoversClass(HttpUri::class)]
#[Small()]
final class HttpMessageTest extends TestCase
{
    #[Test()]
    public function messageHeaderChangesAreImmutableAndCaseInsensitive(): void
    {
        $resource = fopen('php://temp', 'w+b');
        self::assertIsResource($resource);
        $message = new HttpMessage(new HttpStream($resource), new HttpHeaders([]), '1.1');
        $changed = $message->withHeader('X-Request-Id', 'first')->withAddedHeader('x-request-id', 'second');

        self::assertFalse($message->hasHeader('X-Request-Id'));
        self::assertTrue($changed->hasHeader('X-REQUEST-ID'));
        self::assertSame(['first', 'second'], $changed->getHeader('x-request-id'));
        self::assertSame('first,second', $changed->getHeaderLine('X-Request-Id'));
        self::assertSame('1.1', $changed->getProtocolVersion());
    }

    #[Test()]
    public function requestAndUriExposePsrSevenComponents(): void
    {
        $resource = fopen('php://temp', 'w+b');
        self::assertIsResource($resource);
        $uri = new HttpUri(new Uri('https://user:pass@example.com:8443/path?query=value#fragment'));
        $request = new HttpRequest(new HttpStream($resource), new HttpHeaders([]), '2', 'GET', $uri, '');

        self::assertSame('https', $uri->getScheme());
        self::assertSame('user:pass@example.com:8443', $uri->getAuthority());
        self::assertSame('/path?query=value', $request->getRequestTarget());
        self::assertSame('example.com:8443', $request->getHeaderLine('Host'));
        self::assertSame('GET', $request->getMethod());
        self::assertSame('https://user:pass@example.com:8443/path?query=value#fragment', (string) $uri);

        $changed = $uri->withHost('example.org')->withQuery('changed=yes');

        self::assertSame('example.com', $uri->getHost());
        self::assertSame('example.org', $changed->getHost());
        self::assertSame('changed=yes', $changed->getQuery());
    }

    #[Test()]
    public function responseAndServerRequestChangesAreImmutable(): void
    {
        $responseResource = fopen('php://temp', 'w+b');
        $requestResource = fopen('php://temp', 'w+b');
        self::assertIsResource($responseResource);
        self::assertIsResource($requestResource);
        $response = new HttpResponse(new HttpStream($responseResource), new HttpHeaders([]), '1.1', 200, 'OK');

        $request = new HttpServerRequest(
            new HttpStream($requestResource),
            new HttpHeaders([]),
            '1.1',
            'POST',
            new HttpUri(new Uri('https://example.com/')),
            '',
            ['REMOTE_ADDR' => '127.0.0.1'],
            ['session' => 'value'],
            ['page' => '1'],
            [],
            ['name' => 'value'],
            ['request-id' => null],
        );

        self::assertSame(200, $response->getStatusCode());
        self::assertSame(201, $response->withStatus(201, 'Created')->getStatusCode());
        self::assertSame('OK', $response->getReasonPhrase());
        self::assertNull($request->getAttribute('request-id', 'fallback'));
        self::assertSame('fallback', $request->getAttribute('missing', 'fallback'));
        self::assertSame('value', $request->withAttribute('request-id', 'value')->getAttribute('request-id'));
        self::assertNull($request->getAttribute('request-id'));
        self::assertSame(['page' => '1'], $request->getQueryParams());
    }

    #[Test()]
    public function streamAndUploadedFilePreservePayload(): void
    {
        $resource = fopen('php://temp', 'w+b');
        self::assertIsResource($resource);
        $stream = new HttpStream($resource);

        self::assertSame(7, $stream->write('payload'));
        $stream->rewind();
        self::assertSame('payload', $stream->getContents());
        self::assertSame(7, $stream->getSize());

        $stream->rewind();
        $upload = new HttpUploadedFile($stream, 7, UPLOAD_ERR_OK, 'payload.txt', 'text/plain');
        $target = tempnam(sys_get_temp_dir(), 'http-upload-');
        self::assertIsString($target);

        $upload->moveTo($target);

        self::assertSame('payload', file_get_contents($target));
        self::assertSame('payload.txt', $upload->getClientFilename());
        self::assertSame('text/plain', $upload->getClientMediaType());
        self::assertSame(7, $upload->getSize());
        self::assertSame(UPLOAD_ERR_OK, $upload->getError());
        self::assertTrue(unlink($target));
    }
}
