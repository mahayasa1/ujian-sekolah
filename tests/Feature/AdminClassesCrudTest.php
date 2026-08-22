<?php

namespace Tests\Feature;

use App\Models\{User, ClassRoom};
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class AdminClassesCrudTest extends TestCase
{
    use RefreshDatabase;

    protected function admin(): User
    {
        return User::factory()->create(['role' => 'admin']);
    }

    public function test_admin_can_view_classes_page(): void
    {
        $this->actingAs($this->admin())
            ->get(route('admin.classes'))
            ->assertOk()
            ->assertSeeLivewire('admin.classes');
    }

    public function test_admin_can_create_class(): void
    {
        Livewire::actingAs($this->admin())
            ->test(\App\Livewire\Admin\Classes::class)
            ->set('name', 'VII A')
            ->set('grade', '7')
            ->call('save')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('class_rooms', ['name' => 'VII A', 'grade' => '7']);
    }

    public function test_validation_fails_for_empty_fields(): void
    {
        Livewire::actingAs($this->admin())
            ->test(\App\Livewire\Admin\Classes::class)
            ->set('name', '')
            ->set('grade', '')
            ->call('save')
            ->assertHasErrors(['name', 'grade']);
    }

    public function test_admin_can_edit_class(): void
    {
        $class = ClassRoom::create(['name' => 'VII A', 'grade' => '7']);

        Livewire::actingAs($this->admin())
            ->test(\App\Livewire\Admin\Classes::class)
            ->call('edit', $class->id)
            ->assertSet('name', 'VII A')
            ->assertSet('showForm', true)
            ->set('name', 'VII B')
            ->call('save');

        $this->assertDatabaseHas('class_rooms', ['id' => $class->id, 'name' => 'VII B']);
    }

    public function test_admin_can_delete_class(): void
    {
        $class = ClassRoom::create(['name' => 'VII A', 'grade' => '7']);

        Livewire::actingAs($this->admin())
            ->test(\App\Livewire\Admin\Classes::class)
            ->call('delete', $class->id);

        $this->assertDatabaseMissing('class_rooms', ['id' => $class->id]);
    }

    public function test_student_cannot_access_classes_page(): void
    {
        $student = User::factory()->create(['role' => 'siswa']);
        $this->actingAs($student)->get(route('admin.classes'))->assertForbidden();
    }

    public function test_teacher_cannot_access_classes_page(): void
    {
        $teacher = User::factory()->create(['role' => 'guru']);
        $this->actingAs($teacher)->get(route('admin.classes'))->assertForbidden();
    }
}
