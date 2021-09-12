<?php


namespace Tests\Feature\LMS\Course\Http\Controllers;


use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use LMS\User\Models\User;
use LMS\Courses\Models\Course;
use Mockery as m;
use Tests\TestCase;

class CoursesControllerTest extends TestCase
{
    use DatabaseMigrations;

    public function setUp(): void
    {
        parent::setUp(); // TODO: Change the autogenerated stub
        $this->seed();
    }

    public function testUserCanCreateANewCourse()
    {
        // Prepare
        $user = User::factory()->create();
        Storage::fake();
        $payload = [
            'course_level_id' => 1,
            'title' => 'php4noobs',
            'subtitle' => 'o melhor apenas',
            'description' => 'vai caralho',
            'slug' => 'php4noobs',
            'cover' => UploadedFile::fake()->image('super-thumb.png'),
            'paid' => false
        ];
        $user->assignRole('admin');
        // Act
        $this->actingAs($user);
        $response = $this->post(route('instructor-courses-create'), $payload);

        // Assert
        $response->assertCreated()
            ->assertSee($payload['title']);


    }

    public function testUserCanDeleteACourse()
    {
        // Prepare
        $course = Course::factory()->create();

        $course->author->assignRole('admin');
        // Act
        $this->actingAs($course->author);
        $result = $this->delete(route('instructor-courses-delete', ['course' => $course->id]));

        // Assert
        $result->assertNoContent();
        $this->assertSoftDeleted('courses', [
            'id' => $course->id
        ]);

    }
}
