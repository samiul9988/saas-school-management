<?php

namespace Tests\Feature\Sms;

use App\Models\School;
use App\Models\SmsTemplate;
use App\Models\User;
use App\Services\Sms\SmsTemplateRenderer;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class SmsTemplateTest extends TestCase
{
    use RefreshDatabase;

    private function makeSchoolAdmin(): User
    {
        $school = School::create([
            'name' => 'Template Test School',
            'slug' => Str::slug('Template Test School').'-'.Str::random(5),
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

    public function test_renderer_fills_known_tags_and_blanks_unknown_tags(): void
    {
        $renderer = new SmsTemplateRenderer;

        $result = $renderer->render('Hello {{student_name}}, due: {{due_amount}}, unknown: {{nope}}', [
            'student_name' => 'Karim',
            'due_amount' => '500',
        ]);

        $this->assertSame('Hello Karim, due: 500, unknown: ', $result);
    }

    public function test_school_admin_can_create_a_default_template_and_it_unsets_previous_default(): void
    {
        $admin = $this->makeSchoolAdmin();

        $this->actingAs($admin)->post(route('school.sms-templates.store'), [
            'category' => 'guardian',
            'title' => 'First',
            'content' => 'Hello {{student_name}}',
            'is_default' => '1',
        ]);

        $this->actingAs($admin)->post(route('school.sms-templates.store'), [
            'category' => 'guardian',
            'title' => 'Second',
            'content' => 'Hi {{student_name}}',
            'is_default' => '1',
        ]);

        $this->assertSame(1, SmsTemplate::where('category', 'guardian')->where('is_default', true)->count());
        $this->assertSame('Hi {{student_name}}', SmsTemplate::defaultContentFor('guardian'));
    }

    public function test_templates_are_tenant_isolated(): void
    {
        $adminA = $this->makeSchoolAdmin();
        $adminB = $this->makeSchoolAdmin();

        SmsTemplate::create(['school_id' => $adminA->school_id, 'category' => 'hr', 'title' => 'A', 'content' => 'x', 'is_default' => true]);
        SmsTemplate::create(['school_id' => $adminB->school_id, 'category' => 'hr', 'title' => 'B', 'content' => 'y', 'is_default' => true]);

        $this->actingAs($adminA);

        $this->assertCount(1, SmsTemplate::all());
        $this->assertSame('x', SmsTemplate::defaultContentFor('hr'));
    }

    public function test_template_settings_page_renders(): void
    {
        $admin = $this->makeSchoolAdmin();

        $this->actingAs($admin)->get(route('school.sms-templates.index'))->assertOk();
    }
}
