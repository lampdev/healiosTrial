<?php

namespace App\Tests\Unit;

use App\Models\User;
use App\Services\UserRetriever;
use App\Wrappers\GuzzleClientWrapper;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Psr7\Request as GuzzleRequest;
use Mockery;
use Mockery\MockInterface;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class UserRetrieverTest extends TestCase
{
    /** @var UserRetriever */
    private $userRetriever;

    /** @var int */
    private $statusCode = Response::HTTP_OK;

    /** @var string */
    private $reasonPhrase = '';

    /** @var string */
    private $body = '';

    /** @var bool */
    private $shouldFail = false;

    public function setUp()
    {
        parent::setUp();
        $this->userRetriever = new UserRetriever($this->mockGuzzleWrapper());
    }

    public function testGetUserByToken(): void
    {
        $userData = [
            'id' => 1,
            'name' => 'admin',
            'email' => 'admin@email.com',
            'isAdmin' => true
        ];
        $this->body = json_encode($userData);
        $user = $this->userRetriever->getUserByToken(new Request());
        $expectedUser = new User();
        $expectedUser->id = 1;
        $expectedUser->name = 'admin';
        $expectedUser->email = 'admin@email.com';
        $expectedUser->isAdmin = true;
        $this->assertEquals($expectedUser, $user);
    }

    public function testGetUserByTokenIfLackOfData(): void
    {
        $userData = [
            'id' => 1,
            'name' => 'admin',
            'isAdmin' => true
        ];
        $this->body = json_encode($userData);
        $user = $this->userRetriever->getUserByToken(new Request());
        $expectedUser = new User();
        $expectedUser->id = 1;
        $expectedUser->name = 'admin';
        $expectedUser->isAdmin = true;
        $this->assertEquals($expectedUser, $user);
    }

    public function testGetUserByTokenIfTokenIsInvalid(): void
    {
        $this->shouldFail = true;
        $user = $this->userRetriever->getUserByToken(new Request());
        $this->assertNull($user);
    }

    /**
     * @return GuzzleClientWrapper|MockInterface
     */
    private function mockGuzzleWrapper()
    {
        /** @var GuzzleClientWrapper|MockInterface $guzzleWrapper */
        $guzzleWrapper = Mockery::mock(GuzzleClientWrapper::class);
        $guzzleWrapper->shouldReceive('request')->andReturnUsing(function () {
            if ($this->shouldFail) {
                throw new RequestException('message', new GuzzleRequest('METHOD', 'uri'));
            }

            return $this->mockGuzzleResponse();
        });

        return $guzzleWrapper;
    }

    /**
     * @return ResponseInterface|MockInterface
     */
    private function mockGuzzleResponse()
    {
        /** @var ResponseInterface|MockInterface $response */
        $response = Mockery::mock(ResponseInterface::class);
        $response->shouldReceive('getStatusCode')->andReturnUsing(function () {
            return $this->statusCode;
        });
        $response->shouldReceive('getReasonPhrase')->andReturnUsing(function () {
            return $this->reasonPhrase;
        });
        $response->shouldReceive('getBody')->andReturnUsing(function () {
            return $this->body;
        });

        return $response;
    }
}
