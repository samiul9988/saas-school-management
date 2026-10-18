<?php

namespace Tests\Feature\InstituteSettings;

use App\Models\School;
use App\Models\Signature;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Tests\TestCase;

class SignatureTest extends TestCase
{
    use RefreshDatabase;

    private function makeSchoolAdmin(string $schoolName = 'Signature Test School'): User
    {
        $school = School::create([
            'name' => $schoolName,
            'slug' => Str::slug($schoolName).'-'.Str::random(5),
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

    public function test_school_admin_can_create_signature_with_image(): void
    {
        Storage::fake('public');
        $admin = $this->makeSchoolAdmin();

        $response = $this->actingAs($admin)->post(route('school.signatures.store'), [
            'title' => 'Head Teacher',
            'position' => 'Head Teacher',
            'status' => 'active',
            'image' => UploadedFile::fake()->image('sign.png'),
        ]);

        $response->assertRedirect();

        $signature = Signature::first();
        $this->assertSame('Head Teacher', $signature->title);
        $this->assertNotNull($signature->image_path);
        Storage::disk('public')->assertExists($signature->image_path);
    }

    public function test_image_can_be_replaced_and_old_file_removed(): void
    {
        Storage::fake('public');
        $admin = $this->makeSchoolAdmin();

        $signature = Signature::create([
            'school_id' => $admin->school_id,
            'title' => 'Demo',
            'position' => 'Demo',
            'image_path' => 'signatures/old.png',
            'status' => 'active',
        ]);
        Storage::disk('public')->put('signatures/old.png', 'fake');

        $this->actingAs($admin)->post(route('school.signatures.upload-image', $signature), [
            'image' => UploadedFile::fake()->image('new.png'),
        ]);

        $signature->refresh();
        Storage::disk('public')->assertMissing('signatures/old.png');
        Storage::disk('public')->assertExists($signature->image_path);
    }

    public function test_signature_deletion_removes_file(): void
    {
        Storage::fake('public');
        $admin = $this->makeSchoolAdmin();

        $signature = Signature::create([
            'school_id' => $admin->school_id,
            'title' => 'Demo',
            'position' => 'Demo',
            'image_path' => 'signatures/demo.png',
            'status' => 'active',
        ]);
        Storage::disk('public')->put('signatures/demo.png', 'fake');

        $this->actingAs($admin)->delete(route('school.signatures.destroy', $signature));

        $this->assertDatabaseMissing('signatures', ['id' => $signature->id]);
        Storage::disk('public')->assertMissing('signatures/demo.png');
    }

    public function test_signatures_are_tenant_isolated(): void
    {
        $adminA = $this->makeSchoolAdmin('School A');
        $adminB = $this->makeSchoolAdmin('School B');

        Signature::create(['school_id' => $adminA->school_id, 'title' => 'A Sig', 'position' => 'Admin', 'status' => 'active']);
        Signature::create(['school_id' => $adminB->school_id, 'title' => 'B Sig', 'position' => 'Admin', 'status' => 'active']);

        $this->actingAs($adminA);
        $visible = Signature::all();

        $this->assertCount(1, $visible);
        $this->assertSame('A Sig', $visible->first()->title);
    }
}
