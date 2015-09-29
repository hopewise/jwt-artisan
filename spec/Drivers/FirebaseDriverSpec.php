<?php

namespace spec\GenTux\Drivers;

use Exception;
use Firebase\JWT\JWT;
use Prophecy\Argument;
use PhpSpec\ObjectBehavior;
use GenTux\Drivers\FirebaseDriver;
use GenTux\Drivers\JwtDriverInterface;

class FirebaseDriverSpec extends ObjectBehavior
{
    public function it_implements_the_driver_interface()
    {
        $this->shouldHaveType(JwtDriverInterface::class);
    }

    public function it_creates_new_tokens()
    {
        $payload = ['foo' => 'bar'];
        $secret = 'secret123';

        $driver = new FirebaseDriver();
        $result = $driver->createToken($payload, $secret);

        $expect = JWT::encode($payload, $secret);
        if($result !== $expect) {
            throw new Exception('Expected '.$expect.' to match '.$result);
        }
    }

    public function it_validates_tokens()
    {
        $token = JWT::encode([
            'exp' => time() + 30,
            'iat' => time(),
            'nbf' => time(),
        ], $secret = 'secret_123');

        $driver = new FirebaseDriver();
        $result = $driver->validateToken($token, $secret);

        if(! $result) throw new Exception('Unable to validate token '.$token);
    }

    public function it_decodes_tokens()
    {
        $token = JWT::encode(
            $payload = [
            'exp' => time() + 30,
            'iat' => time(),
            'nbf' => time(),
            'foo' => 'bar',
        ], $secret = 'secret_123');

        $driver = new FirebaseDriver();
        $result = $driver->decodeToken($token, $secret);

        if(
            $result['exp'] !== $payload['exp']
            || $result['iat'] !== $payload['iat']
            || $result['nbf'] !== $payload['nbf']
            || $result['foo'] !== $payload['foo']
        ) {
            throw new \Exception('Decoded payload did not match the encoded tokens payload. '.$token);
        }
    }
}
