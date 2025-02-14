<?php

namespace Tests\Unit;

use App\Http\Controllers\UserController;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Mockery;
use Tests\TestCase;

class UserUnitTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutMiddleware(); // Disable middleware
        $this->withoutExceptionHandling(); // Disable error handling for debugging
    }

    public function test_it_can_list_all_users()
    {
        $userMock = Mockery::mock('alias:' . User::class);
        $userMock->shouldReceive('all')->once()->andReturn(collect([
            (object) ['id' => 1, 'name' => 'John Doe', 'email' => 'john@example.com'],
            (object) ['id' => 2, 'name' => 'Jane Doe', 'email' => 'jane@example.com']
        ]));

        $controller = new UserController();
        $response = $controller->index();

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertCount(2, json_decode($response->getContent()));
    }

    public function test_it_can_delete_a_user()
    {
        $userMock = Mockery::mock('alias:' . User::class);
        $userMock->shouldReceive('find')->with(1)->once()->andReturnSelf();
        $userMock->shouldReceive('delete')->once()->andReturn(true);

        $controller = new UserController();
        $response = $controller->destroy(1);

        $this->assertEquals(200, $response->getStatusCode());
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
}
