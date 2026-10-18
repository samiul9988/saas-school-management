<?php

namespace Tests\Feature\InstituteSettings;

use App\Models\School;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Tests\TestCase;

class SchoolProfileTest extends TestCase
{
    use RefreshDatabase;

    private function makeSchoolAdmin(): User
    {
        $school = School::create([
            'name' => 'Profile Test School',
            'slug' => Str::slug('Profile Test School').'-'.Str::random(5),
            'email' => fake()->unique()->safeEmail(),
            'status' => 'active',
        ]);

        return User::create([
            'name' => 'School Admin',
            'email' => fake()->unique()->safeEmail(),
            'password' => bcrypt('password'),
            'role' => User::ROLE_SCHOOL_ADMIN,
            'school_id' => $school->id,
        ]);
    }

    public function test_school_admin_can_update_institute_profile(): void
    {
        $admin = $this->makeSchoolAdmin();

        $response = $this->actingAs($admin)->put(route('school.profile.update'), [
            'name' => 'Updated Institute Name',
            'admin_name' => 'Jane Doe',
            'contact_number' => '01711111111',
            'address' => 'New Address',
        ]);

        $response->assertRedirect(route('school.profile.edit'));

        $admin->school->refresh();
        $this->assertSame('Updated Institute Name', $admin->school->name);
        $this->assertSame('Jane Doe', $admin->school->admin_name);
        $this->assertSame('01711111111', $admin->school->contact_number);
        $this->assertSame('New Address', $admin->school->address);
    }

    public function test_school_admin_can_update_logo(): void
    {
        Storage::fake('public');
        $admin = $this->makeSchoolAdmin();

        $this->actingAs($admin)->put(route('school.profile.update'), [
            'name' => $admin->school->name,
            'logo' => UploadedFile::fake()->image('logo.png'),
        ]);

        $admin->school->refresh();
        $this->assertNotNull($admin->school->logo);
        Storage::disk('public')->assertExists($admin->school->logo);
    }

    public function test_profile_page_renders(): void
    {
        $admin = $this->makeSchoolAdmin();

        $this->actingAs($admin)->get(route('school.profile.edit'))->assertOk();
    }
}
