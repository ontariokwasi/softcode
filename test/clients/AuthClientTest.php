<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;

require_once 'app/client/AuthClient.php';
require_once 'app/model/auth/Token.php';

final class AuthClientTest extends TestCase
{
    // 6b4ea190cba4ed195cc85da8bc3f4802a57b5bf8<=>12039e7fa0b2e0e693f2701670d32d6dc6d7933f
    // a85900768e7bc2db917f187f6b9fdf62c64959a1 = a87a2bb9dff3d896d2e4848141dbde76d3c4858c

    public function testVodafoneAuth(): void
    {
        $token = (new AuthClient)->getAccessToken(ClientType::VODAFONE, new AuthRequest('6b4ea190cba4ed195cc85da8bc3f4802a57b5bf8', '12039e7fa0b2e0e693f2701670d32d6dc6d7933f'));
        $this->assertInstanceOf(AccessToken::class, $token);
        $this->assertNotEmpty($token->token);
        $this->assertInstanceOf(DateTime::class, $token->expiry);
        $this->assertEquals("{\"token\":\"$token->token\",\"valid_until\":\"" . $token->expiry->format('Y-m-d H:i:s') . "\"}",  $token->__toString());
    }
}
