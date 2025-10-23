<?php

namespace Tests\Feature;

use Illuminate\Filesystem\Filesystem;
use Tests\TestCase;

class ViewsNoRoleDirectivesTest extends TestCase
{
    public function test_no_direct_role_checks_in_views(): void
    {
        $fs = new Filesystem();
        $files = $fs->allFiles(resource_path('views'));

        $forbidden = [
            'hasRole(',
            'hasAnyRole(',
            '@role(',
            '@hasrole(',
            'role:'
        ];

        foreach ($files as $file) {
            $contents = $fs->get($file->getRealPath());
            foreach ($forbidden as $needle) {
                $this->assertStringNotContainsString($needle, $contents, 'Found forbidden role directive in view: '.$file->getRelativePathname());
            }
        }
    }
}

