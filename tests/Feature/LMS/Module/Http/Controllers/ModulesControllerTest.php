<?php


namespace Tests\Feature\LMS\Module\Http\Controllers;


use Illuminate\Foundation\Testing\DatabaseMigrations;
use LMS\Courses\Models\Course;
use LMS\Modules\Models\Module;
use Tests\TestCase;

class ModulesControllerTest extends TestCase
{
    use DatabaseMigrations;

    public function setUp(): void
    {
        parent::setUp(); // TODO: Change the autogenerated stub
        $this->seed();
    }

    public function testUserCanCreateANewCourseModule()
    {
        // Prepare
        $course = Course::factory()->create();
        $payload = [
            'name' => 'introdução ao curso dos cara',
            'description' => 'mais qlqr coisa ai pra vc',
        ];

        // Act
        $this->actingAs($course->author);
        $response = $this->post(route('instructor-course-module-new', ['course' => $course]), $payload);

        // Assert
        $response->assertCreated();
        $this->assertDatabaseHas('course_modules', $payload);
    }

    public function testModulesOrderingAfterDelete()
    {
        // Prepare
        $course = Course::factory()
            ->has(Module::factory()->count(6))
            ->create();

        foreach ($course->modules as $key => $module) {
            $module->update(['order' => $key - 1]);
        }

        // Act
        $this->actingAs($course->author);
        $response = $this->delete(route('instructor-course-module-delete', ['course' => $course, 'module' => 4]));

        // Assert
        $expected = [0, 1, 2, 3, 4];
        $actual = $course->refresh()
            ->modules()
            ->orderBy('order')
            ->get()
            ->map(fn(Module $module) => $module->order);

        $response->assertNoContent();
        $this->assertEquals($expected, $actual->toArray());
    }


    public function testModulesOrderingAfterDeleteIndexZero()
    {
        // Prepare
        $course = Course::factory()
            ->has(Module::factory()->count(6))
            ->create();

        foreach ($course->modules as $key => $module) {
            $module->update(['order' => $key - 1]);
        }

        // Act
        $this->actingAs($course->author);
        $response = $this->delete(route('instructor-course-module-delete', ['course' => $course, 'module' => 1]));

        // Assert
        $expected = [0, 1, 2, 3, 4];
        $actual = $course->refresh()
            ->modules()
            ->orderBy('order')
            ->get()
            ->map(fn(Module $module) => $module->order);

        $response->assertNoContent();
        $this->assertEquals($expected, $actual->toArray());
    }
}