<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Course;
use App\Models\Certificate;
use App\Models\CertificateTemplate;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class CertificateControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seedRolesAndPermissions();
        Storage::fake('public');
    }

    public function test_instructor_can_generate_certificate_for_completed_student(): void
    {
        $instructor = $this->createInstructorUser();
        $student = $this->createParticipantUser();
        $course = Course::factory()->create();
        $template = CertificateTemplate::factory()->create();

        $course->instructors()->attach($instructor->id);
        $course->enrolledUsers()->attach($student->id, ['completed_at' => now()]);

        $response = $this->actingAs($instructor)->post(
            route('courses.certificates.generate', [$course, $student]),
            ['template_id' => $template->id]
        );

        $response->assertRedirect();
        $this->assertDatabaseHas('certificates', [
            'user_id' => $student->id,
            'course_id' => $course->id,
            'template_id' => $template->id,
        ]);
    }

    public function test_certificate_verification_works(): void
    {
        $certificate = Certificate::factory()->create([
            'verification_code' => 'TEST123456',
            'issued_at' => now(),
        ]);

        $response = $this->get(route('certificates.verify', 'TEST123456'));

        $response->assertStatus(200);
        $response->assertViewIs('certificates.verify');
        $response->assertViewHas('certificate', $certificate);
        $response->assertSee($certificate->user->name);
        $response->assertSee($certificate->course->title);
    }

    public function test_certificate_verification_fails_with_invalid_code(): void
    {
        $response = $this->get(route('certificates.verify', 'INVALID123'));

        $response->assertStatus(404);
    }

    public function test_student_can_download_own_certificate(): void
    {
        $student = $this->createParticipantUser();
        $certificate = Certificate::factory()->create([
            'user_id' => $student->id,
            'file_path' => 'certificates/test.pdf',
        ]);

        // Create fake certificate file
        Storage::disk('public')->put('certificates/test.pdf', 'fake pdf content');

        $response = $this->actingAs($student)->get(
            route('certificates.download', $certificate)
        );

        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'application/pdf');
    }

    public function test_student_cannot_download_others_certificate(): void
    {
        $student1 = $this->createParticipantUser();
        $student2 = $this->createParticipantUser();
        
        $certificate = Certificate::factory()->create(['user_id' => $student2->id]);

        $response = $this->actingAs($student1)->get(
            route('certificates.download', $certificate)
        );

        $response->assertStatus(403);
    }

    public function test_bulk_certificate_generation(): void
    {
        $instructor = $this->createInstructorUser();
        $course = Course::factory()->create();
        $template = CertificateTemplate::factory()->create();
        
        $students = User::factory()->count(3)->create();
        
        foreach ($students as $student) {
            $student->assignRole('participant');
            $course->enrolledUsers()->attach($student->id, ['completed_at' => now()]);
        }

        $course->instructors()->attach($instructor->id);

        $response = $this->actingAs($instructor)->post(
            route('courses.certificates.bulk-generate', $course),
            [
                'template_id' => $template->id,
                'user_ids' => $students->pluck('id')->toArray(),
            ]
        );

        $response->assertRedirect();
        
        foreach ($students as $student) {
            $this->assertDatabaseHas('certificates', [
                'user_id' => $student->id,
                'course_id' => $course->id,
            ]);
        }
    }
}