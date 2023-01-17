<?php

declare(strict_types=1);

namespace MwopTest\Mastodon;

use Fig\Http\Message\RequestMethodInterface;
use Fig\Http\Message\StatusCodeInterface;
use Generator;
use Laminas\Diactoros\RequestFactory;
use Laminas\Diactoros\Response;
use Laminas\Diactoros\StreamFactory;
use Mwop\Mastodon\ApiPath;
use Mwop\Mastodon\Credentials;
use Mwop\Mastodon\Media;
use Mwop\Mastodon\PsrApiClient;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestInterface;

class PsrApiClientTest extends TestCase
{
    private PsrApiClient $apiClient;

    private Credentials $credentials;

    /** @var ClientInterface&MockObject */
    private $httpClient;

    /** @var null|string */
    private $tempfile = null;

    protected function setUp(): void
    {
        $this->tempfile = null;
        $this->httpClient = $this->createMock(ClientInterface::class);
        $this->apiClient  = new PsrApiClient(
            $this->httpClient,
            new RequestFactory(),
            new StreamFactory(),
            'mastodon.local',
        );
        $this->credentials = new Credentials('x-some-token-here');
    }

    protected function tearDown(): void
    {
        if (is_string($this->tempfile) && is_file($this->tempfile)) {
            unlink($this->tempfile);
        }
    }

    /** @psalm-var Generator<string, array{0: int}> */
    public function invalidMediaStatusCodes(): Generator
    {
        yield 'no-content'     => [StatusCodeInterface::STATUS_NO_CONTENT];
        yield 'redirect'       => [StatusCodeInterface::STATUS_TEMPORARY_REDIRECT];
        yield 'client-error'   => [StatusCodeInterface::STATUS_BAD_REQUEST];
        yield 'authorization'  => [StatusCodeInterface::STATUS_UNAUTHORIZED];
        yield 'authentication' => [StatusCodeInterface::STATUS_FORBIDDEN];
        yield 'unprocessable'  => [StatusCodeInterface::STATUS_UNPROCESSABLE_ENTITY];
        yield 'server-error'   => [StatusCodeInterface::STATUS_INTERNAL_SERVER_ERROR];
    }

    /** @dataProvider invalidMediaStatusCodes */
    public function testUploadMediaReturnsFailureResultWhenStatusCodeIsInvalid(int $statusCode): void
    {
        $this->tempfile = tempnam(sys_get_temp_dir(), 'mwo');
        $media = new Media(fopen($this->tempfile, 'r+'), basename($this->tempfile), 'text/plain');

        $response = new Response(status: $statusCode);

        $this->httpClient
            ->expects($this->once())
            ->method('sendRequest')
            ->with($this->callback(function (RequestInterface $request): bool {
                if ($request->getHeaderLine('Authorization') !== 'Bearer x-some-token-here') {
                    return false;
                }

                if ($request->getHeaderLine('Accept') !== 'application/json') {
                    return false;
                }

                if (! preg_match('#^multipart/form-data; boundary\=\S+$#', $request->getHeaderLine('Content-Type'))) {
                    return false;
                }

                if ($request->getMethod() !== RequestMethodInterface::METHOD_POST) {
                    return false;
                }

                if ($request->getUri()->getPath() !== ApiPath::MEDIA->value) {
                    return false;
                }

                return true;
            }))
            ->willReturn($response);

        $result = $this->apiClient->uploadMedia($this->credentials, $media);
        $this->assertFalse($result->isSuccessful());
    }

    /** @psalm-var Generator<string, array{0: int}> */
    public function validMediaStatusCodes(): Generator
    {
        yield 'accepted' => [StatusCodeInterface::STATUS_ACCEPTED];
        yield 'ok'       => [StatusCodeInterface::STATUS_OK];
    }

    /** @dataProvider validMediaStatusCodes */
    public function testUploadMediaReturnsSuccessResultWhenStatusCodeIsValid(int $statusCode): void
    {
        $this->tempfile = tempnam(sys_get_temp_dir(), 'mwo');
        $media = new Media(fopen($this->tempfile, 'r+'), basename($this->tempfile), 'text/plain');

        $response = new Response(status: $statusCode);

        $this->httpClient
            ->expects($this->once())
            ->method('sendRequest')
            ->with($this->callback(function (RequestInterface $request): bool {
                if ($request->getHeaderLine('Authorization') !== 'Bearer x-some-token-here') {
                    return false;
                }

                if ($request->getHeaderLine('Accept') !== 'application/json') {
                    return false;
                }

                if (! preg_match('#^multipart/form-data; boundary\=\S+$#', $request->getHeaderLine('Content-Type'))) {
                    return false;
                }

                if ($request->getMethod() !== RequestMethodInterface::METHOD_POST) {
                    return false;
                }

                if ($request->getUri()->getPath() !== ApiPath::MEDIA->value) {
                    return false;
                }

                return true;
            }))
            ->willReturn($response);

        $result = $this->apiClient->uploadMedia($this->credentials, $media);
        $this->assertTrue($result->isSuccessful());
    }
}
