<?php

namespace App\Tests\Unit\Service\Security;

use App\Service\Security\TokenService;
use Lexik\Bundle\JWTAuthenticationBundle\Encoder\JWTEncoderInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Exception\JWTDecodeFailureException;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\HeaderBag;
use Symfony\Component\HttpFoundation\Request;

/**
 * @runTestsInSeparateProcesses
 */
class TokenServiceTest extends TestCase
{
    public function testExtractToken(): void
    {
        $token = 'Bearer xyz';
        $jwtEncoder = $this->createMock(JWTEncoderInterface::class);
        $tokenService = new TokenService($jwtEncoder);

        //good request
        $request = $this->createMock(Request::class);
        $headerBag = $this->createMock(HeaderBag::class);
        $headerBag->method('has')->willReturn(true);
        $headerBag->method('get')->willReturn($token);
        $request->headers = $headerBag;

        $authorizationHeaderTokenExtractor  = \Mockery::mock('overload:AuthorizationHeaderTokenExtractor');
        $authorizationHeaderTokenExtractor->shouldReceive('extract')
            ->once()
            ->with($request)
            ->andReturn();

        $this->assertEquals('xyz', $tokenService->extractToken($request));

        //bad request
        $request = $this->createMock(Request::class);
        $headerBag = $this->createMock(HeaderBag::class);
        $headerBag->method('has')->willReturn(false);
        $request->headers = $headerBag;

        $authorizationHeaderTokenExtractor  = \Mockery::mock('overload:AuthorizationHeaderTokenExtractor');
        $authorizationHeaderTokenExtractor->shouldReceive('extract')
            ->once()
            ->with($request)
            ->andReturn();

        $this->assertFalse($tokenService->extractToken($request));
    }

    public function testRefreshToken(): void
    {
        $token = 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJ1c2VybmFtZSI6ImVtYWlsQHRlc3QucGwifQ.kl-fH-B3fKTuOc3u3-Pr14uct1ct6grjlqK5OCfSxz4';
        //no error
        $jwtEncoder = $this->createMock(JWTEncoderInterface::class);
        $jwtEncoder->method('decode')->willReturn([]);
        $jwtEncoder->method('encode')->willReturn('newToken');
        $tokenService = new TokenService($jwtEncoder);

        $this->assertEquals('newToken', $tokenService->refreshToken($token));

        //expired token error
        $jwtEncoder = $this->createMock(JWTEncoderInterface::class);
        $exception = new JWTDecodeFailureException('', 'Expired JWT Token');
        $jwtEncoder->method('decode')->willThrowException($exception);
        $jwtEncoder->method('encode')->willReturn('newToken');
        $tokenService = new TokenService($jwtEncoder);

        $this->assertEquals('newToken', $tokenService->refreshToken($token));

        //wrong token
        $jwtEncoder = $this->createMock(JWTEncoderInterface::class);
        $exception = new JWTDecodeFailureException('', 'oopsie');
        $jwtEncoder->method('decode')->willThrowException($exception);
        $tokenService = new TokenService($jwtEncoder);

        $this->assertNull($tokenService->refreshToken($token));
    }
}