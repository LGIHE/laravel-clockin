<?php

namespace Tests\Unit\Models;

use App\Models\Notice;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class NoticeTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test notice soft delete.
     */
    public function test_notice_soft_delete(): void
    {
        $notice = Notice::factory()->create();
        $noticeId = $notice->id;

        $notice->delete();

        $this->assertSoftDeleted('notices', ['id' => $noticeId]);
        $this->assertNotNull($notice->fresh()->deleted_at);
    }

    /**
     * Test notice can be restored after soft delete.
     */
    public function test_notice_can_be_restored(): void
    {
        $notice = Notice::factory()->create();
        $notice->delete();

        $notice->restore();

        $this->assertNull($notice->fresh()->deleted_at);
        $this->assertDatabaseHas('notices', [
            'id' => $notice->id,
            'deleted_at' => null,
        ]);
    }
}
