<?php
namespace Tests\Unit\Policies;

use App\Policies\PackageGroupPolicy;
use App\Services\PackageGroupService;
use PHPUnit\Framework\TestCase;

class PackageGroupPolicyTest extends TestCase
{
    /**
     * @group integration
     * Note: isAdmin() type-hints App\Model\User which requires lib-reporangler.
     * This test requires the full app bootstrap to load the User class.
     */
    public function testIsAdminReturnsTrueForAdmin()
    {
        if (!class_exists('RepoRangler\\Entity\\User', false) && !class_exists('App\\Model\\User', false)) {
            $this->markTestSkipped('App\Model\User requires lib-reporangler to be properly autoloaded');
        }

        $user = $this->createMock(\App\Model\User::class);
        $user->is_admin_user = true;

        $service = $this->createMock(PackageGroupService::class);
        $policy = new PackageGroupPolicy($service);

        $this->assertTrue($policy->isAdmin($user));
    }

    public function testLeaveReturnsTrue()
    {
        $service = $this->createMock(PackageGroupService::class);
        $policy = new PackageGroupPolicy($service);

        $this->assertTrue($policy->leave(null));
    }
}
