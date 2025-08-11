<?php

namespace Tests\Feature;

use App\Models\Content;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ContentTest extends TestCase
{
    use RefreshDatabase;

    public function test_content_can_be_created(): void
    {
        $content = Content::factory()->create();

        $this->assertDatabaseHas('contents', [
            'id' => $content->id,
        ]);
    }

    public function test_content_can_be_updated(): void
    {
        $content = Content::factory()->create();

        $content->update(['title' => 'Updated Title']);

        $this->assertDatabaseHas('contents', [
            'id' => $content->id,
            'title' => 'Updated Title',
        ]);
    }

    public function test_content_can_be_deleted(): void
    {
        $content = Content::factory()->create();

        $content->delete();

        $this->assertDatabaseMissing('contents', [
            'id' => $content->id,
        ]);
    }
}

