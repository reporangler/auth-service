<?php
namespace Tests\Unit\Model;

use App\Model\LoginToken;
use PHPUnit\Framework\TestCase;

class LoginTokenTest extends TestCase
{
    public function testTokenIsHexString()
    {
        $token = new LoginToken(['user_id' => 1, 'expire_at' => '2025-01-01']);
        $this->assertRegExp('/^[a-f0-9]{64}$/', $token->token);
    }

    public function testTokenIsUnique()
    {
        $token1 = new LoginToken(['user_id' => 1, 'expire_at' => '2025-01-01']);
        $token2 = new LoginToken(['user_id' => 1, 'expire_at' => '2025-01-01']);
        $this->assertNotEquals($token1->token, $token2->token);
    }

    public function testTokenLength()
    {
        $token = new LoginToken(['user_id' => 1, 'expire_at' => '2025-01-01']);
        $this->assertEquals(64, strlen($token->token));
    }
}
