<?php

namespace App\Service\Security;

use Lexik\Bundle\JWTAuthenticationBundle\Encoder\JWTEncoderInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Exception\JWTDecodeFailureException;
use Lexik\Bundle\JWTAuthenticationBundle\Exception\JWTEncodeFailureException;
use Lexik\Bundle\JWTAuthenticationBundle\TokenExtractor\AuthorizationHeaderTokenExtractor;
use Symfony\Component\HttpFoundation\Request;

class TokenService
{
    private JWTEncoderInterface $jwtEncoder;

    public function __construct(JWTEncoderInterface $jwtEncoder)
    {
        $this->jwtEncoder = $jwtEncoder;
    }

    /**
     * @return bool|false|string|null
     */
    public function extractToken(Request $request)
    {
        $extractor = new AuthorizationHeaderTokenExtractor(
            'Bearer',
            'Authorization'
        );

        return $extractor->extract($request);
    }

    /**
     * @throws JWTEncodeFailureException
     */
    public function refreshToken(string $token): ?string
    {
        $error = false;
        try {
            $this->jwtEncoder->decode($token);
        } catch (JWTDecodeFailureException $e) {
            $error = $e->getMessage();
        }
        if (strcmp($error, 'Expired JWT Token') == 0 || !$error) {
            $pieces = explode('.', $token);
            $payload = json_decode(base64_decode($pieces[1]), true);

            $token = $this->jwtEncoder->encode($payload);

            return $token;
        }

        return null;
    }
}
