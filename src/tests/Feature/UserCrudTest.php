<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\Attributes\Group;

final class UserCrudTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    #[Group('database')]
    #[TestDox('UserモデルのCRUD操作が正常に動作する')]
    public function it_can_create_update_and_delete_user(): void
    {
        // CREATE
        $user = User::factory()->create([
            'name' => 'Taro',
            'email' => 'taro@example.com',
        ]);
        $this->assertDatabaseHas('users', ['email' => 'taro@example.com']);

        // UPDATE
        $user->update(['name' => 'Hanako']);
        $this->assertDatabaseHas('users', [
            'email' => 'taro@example.com',
            'name'  => 'Hanako',
        ]);

        // DELETE
        $user->delete();
        $this->assertDatabaseMissing('users', ['email' => 'taro@example.com']);
    }
}