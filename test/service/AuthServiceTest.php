<?php

use PHPUnit\Framework\TestCase;

require_once 'app/service/authService.php';

class AuthServiceTest extends TestCase
{
    public function testGetAccessTokenNotExpiredForVodafone()
    {
        $authDbMock = ($this->getMockBuilder(AuthDao::class)->onlyMethods(['getToken'])->getMock());
        $authDbMock->method('getToken')->will($this->returnValue(['token' => 'e5d8a0eab1a0b6970920c798b56ffa29', 'expiryDate' => date_add(new DateTime(), new DateInterval('PT360S'))->format('Y-m-d H:i:s'), 'username' => 'abc', 'password' => '123', 'id' => 1]));
        $authDbMock->expects($this->once())->method('getToken');
        $this->assertEquals('e5d8a0eab1a0b6970920c798b56ffa29', (new VodafoneAuthService($authDbMock))->getAccessToken(1));
    }
    public function testGetAccessTokenExpiredForVodafone()
    {
        $authDbMock = ($this->getMockBuilder(AuthDao::class)->onlyMethods(['getToken'])->getMock());
        $authDbMock->method('getToken')->will($this->returnValue(['token' => 'e5d8a0eab1a0b6970920c798b56ffa29', 'expiryDate' => date_sub(new DateTime(), new DateInterval('PT360S'))->format('Y-m-d H:i:s'), 'username' => 'abc', 'password' => '123', 'id' => 1]));

        $authClientMock = $this->getMockBuilder(AuthClient::class)->onlyMethods(['getAccessToken'])->getMock();
        $authClientMock->method('getAccessToken')->will($this->returnValue(new AccessToken('e5d8a0eab1a0b6970920c798b56ffa29', new DateTime())));

        $authDbMock->expects($this->once())->method('getToken');
        $authClientMock->expects($this->once())->method('getAccessToken');
        $this->assertEquals('e5d8a0eab1a0b6970920c798b56ffa29', (new VodafoneAuthService($authDbMock, $authClientMock))->getAccessToken(1));
    }
}
