<?php
namespace Tests\Unit\Policies;

use App\Policies\RepositoryPolicy;
use PHPUnit\Framework\TestCase;

class RepositoryPolicyTest extends TestCase
{
    public function testProtectReturnsTrue()
    {
        $policy = new RepositoryPolicy();
        $this->assertTrue($policy->protect(null));
    }

    public function testUnprotectReturnsTrue()
    {
        $policy = new RepositoryPolicy();
        $this->assertTrue($policy->unprotect(null));
    }
}
