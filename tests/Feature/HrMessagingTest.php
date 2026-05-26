<?php

namespace Tests\Feature;

use App\Models\Conversation;
use App\Models\Message;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HrMessagingTest extends TestCase
{
    use RefreshDatabase;

    private User $hr;

    private User $candidate;

    protected function setUp(): void
    {
        parent::setUp();

        $this->hr = User::factory()->create(['role' => User::ROLE_HR]);
        $this->candidate = User::factory()->create(['role' => User::ROLE_USER]);
    }

    public function test_hr_can_send_message_and_candidate_can_reply(): void
    {
        $conversation = Conversation::create([
            'hr_id' => $this->hr->id,
            'candidate_id' => $this->candidate->id,
            'job_description_id' => null,
        ]);

        $this->actingAs($this->hr)
            ->postJson(route('hr.messages.store', $conversation), [
                'body' => 'Hi, we think you are a strong fit.',
            ])
            ->assertCreated()
            ->assertJsonPath('message.body', 'Hi, we think you are a strong fit.');

        $this->assertDatabaseHas('messages', [
            'conversation_id' => $conversation->id,
            'sender_id' => $this->hr->id,
            'body' => 'Hi, we think you are a strong fit.',
        ]);

        $this->actingAs($this->candidate)
            ->postJson(route('user.messages.store', $conversation), [
                'body' => 'Thank you, I am interested.',
            ])
            ->assertCreated();

        $this->actingAs($this->candidate)
            ->get(route('user.messages.show', $conversation))
            ->assertOk()
            ->assertSee('Hi, we think you are a strong fit.')
            ->assertSee('Thank you, I am interested');
    }

    public function test_mark_read_clears_unread_for_recipient(): void
    {
        $conversation = Conversation::create([
            'hr_id' => $this->hr->id,
            'candidate_id' => $this->candidate->id,
        ]);

        Message::create([
            'conversation_id' => $conversation->id,
            'sender_id' => $this->hr->id,
            'body' => 'Hello',
        ]);

        $this->actingAs($this->candidate)
            ->post(route('user.messages.read', $conversation))
            ->assertOk();

        $this->assertDatabaseMissing('messages', [
            'conversation_id' => $conversation->id,
            'sender_id' => $this->hr->id,
            'read_at' => null,
        ]);
    }

    public function test_candidate_cannot_access_other_conversations(): void
    {
        $other = User::factory()->create(['role' => User::ROLE_USER]);
        $conversation = Conversation::create([
            'hr_id' => $this->hr->id,
            'candidate_id' => $other->id,
        ]);

        $this->actingAs($this->candidate)
            ->get(route('user.messages.show', $conversation))
            ->assertForbidden();
    }
}
