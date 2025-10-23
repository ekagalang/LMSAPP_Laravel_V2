<?php

namespace Tests\Feature;

use Illuminate\Filesystem\Filesystem;
use Tests\TestCase;

class NoRoleChecksInAppTest extends TestCase
{
    public function test_no_direct_role_checks_in_app_except_auth_service_provider(): void
    {
        $fs = new Filesystem();
        $files = $fs->allFiles(app_path());

        $forbidden = ['hasRole(', 'hasAnyRole(', '::role('];
        $allowed = [
            realpath(app_path('Providers/AuthServiceProvider.php')),
        ];

        foreach ($files as $file) {
            $path = $file->getRealPath();
            $contents = $fs->get($path);
            foreach ($forbidden as $needle) {
                if (str_contains($contents, $needle)) {
                    $this->assertTrue(in_array($path, $allowed), 'Found forbidden role usage in: '.$file->getRelativePathname());
                }
            }
        }
    }
}

