<?php

namespace Tests\Feature;

use App\Models\Resume;
use App\Models\ResumeCertificate;
use App\Models\ResumeVersion;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ResumeCertificateTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_user_can_create_certificate(): void
    {
        $user = User::factory()->create();

        $resume = Resume::factory()->create([
            'user_id' => $user->id,
        ]);

        $version = ResumeVersion::factory()->create([
            'resume_id' => $resume->id,
            'version_number' => 1,
        ]);

        $response = $this->actingAs($user)
            ->postJson("/api/resumes/{$resume->id}/certificates", [
                'resume_version_id' => $version->id,
                'name' => 'Laravel Developer Certificate',
                'issuer' => 'Laravel',
                'credential_id' => 'CERT-2026-001',
                'credential_url' => 'https://example.com/certificate',
                'issued_at' => '2026-01-15',
                'expires_at' => '2029-01-15',
                'sort_order' => 1,
            ]);

        $response
            ->assertStatus(201)
            ->assertJsonPath(
                'data.name',
                'Laravel Developer Certificate'
            )
            ->assertJsonPath(
                'data.issuer',
                'Laravel'
            )
            ->assertJsonPath(
                'data.credential_id',
                'CERT-2026-001'
            )
            ->assertJsonPath(
                'data.credential_url',
                'https://example.com/certificate'
            );

        $this->assertDatabaseHas('resume_certificates', [
            'resume_version_id' => $version->id,
            'name' => 'Laravel Developer Certificate',
            'issuer' => 'Laravel',
        ]);
    }

    public function test_authenticated_user_can_list_certificates(): void
    {
        $user = User::factory()->create();

        $resume = Resume::factory()->create([
            'user_id' => $user->id,
        ]);

        $version = ResumeVersion::factory()->create([
            'resume_id' => $resume->id,
            'version_number' => 1,
        ]);

        ResumeCertificate::create([
            'resume_version_id' => $version->id,
            'name' => 'Laravel Developer Certificate',
            'issuer' => 'Laravel',
            'credential_id' => 'CERT-001',
            'credential_url' => 'https://example.com/certificate',
            'issued_at' => '2026-01-15',
            'expires_at' => '2029-01-15',
            'sort_order' => 1,
        ]);

        $response = $this->actingAs($user)
            ->getJson("/api/resumes/{$resume->id}/certificates");

        $response
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath(
                'data.0.name',
                'Laravel Developer Certificate'
            );
    }

    public function test_authenticated_user_can_view_certificate(): void
    {
        $user = User::factory()->create();

        $resume = Resume::factory()->create([
            'user_id' => $user->id,
        ]);

        $version = ResumeVersion::factory()->create([
            'resume_id' => $resume->id,
            'version_number' => 1,
        ]);

        $certificate = ResumeCertificate::create([
            'resume_version_id' => $version->id,
            'name' => 'Laravel Developer Certificate',
            'issuer' => 'Laravel',
            'credential_id' => 'CERT-001',
            'credential_url' => 'https://example.com/certificate',
            'issued_at' => '2026-01-15',
            'expires_at' => '2029-01-15',
        ]);

        $response = $this->actingAs($user)
            ->getJson(
                "/api/resumes/{$resume->id}/certificates/{$certificate->id}"
            );

        $response
            ->assertOk()
            ->assertJsonPath('data.id', $certificate->id)
            ->assertJsonPath(
                'data.name',
                'Laravel Developer Certificate'
            );
    }

    public function test_authenticated_user_can_update_certificate(): void
    {
        $user = User::factory()->create();

        $resume = Resume::factory()->create([
            'user_id' => $user->id,
        ]);

        $version = ResumeVersion::factory()->create([
            'resume_id' => $resume->id,
            'version_number' => 1,
        ]);

        $certificate = ResumeCertificate::create([
            'resume_version_id' => $version->id,
            'name' => 'Old Certificate',
            'issuer' => 'Old Issuer',
            'credential_id' => 'OLD-001',
            'credential_url' => 'https://example.com/old',
            'issued_at' => '2025-01-15',
            'expires_at' => '2027-01-15',
        ]);

        $response = $this->actingAs($user)
            ->putJson(
                "/api/resumes/{$resume->id}/certificates/{$certificate->id}",
                [
                    'name' => 'Updated Certificate',
                    'issuer' => 'Updated Issuer',
                    'credential_id' => 'NEW-001',
                    'credential_url' => 'https://example.com/new',
                    'issued_at' => '2026-01-15',
                    'expires_at' => '2029-01-15',
                ]
            );

        $response
            ->assertOk()
            ->assertJsonPath(
                'data.name',
                'Updated Certificate'
            )
            ->assertJsonPath(
                'data.issuer',
                'Updated Issuer'
            )
            ->assertJsonPath(
                'data.credential_id',
                'NEW-001'
            )
            ->assertJsonPath(
                'data.credential_url',
                'https://example.com/new'
            );
    }

    public function test_authenticated_user_can_delete_certificate(): void
    {
        $user = User::factory()->create();

        $resume = Resume::factory()->create([
            'user_id' => $user->id,
        ]);

        $version = ResumeVersion::factory()->create([
            'resume_id' => $resume->id,
            'version_number' => 1,
        ]);

        $certificate = ResumeCertificate::create([
            'resume_version_id' => $version->id,
            'name' => 'Laravel Certificate',
            'issuer' => 'Laravel',
            'credential_id' => 'CERT-001',
            'issued_at' => '2026-01-15',
        ]);

        $response = $this->actingAs($user)
            ->deleteJson(
                "/api/resumes/{$resume->id}/certificates/{$certificate->id}"
            );

        $response->assertOk();

        $this->assertDatabaseMissing('resume_certificates', [
            'id' => $certificate->id,
        ]);
    }

    public function test_user_cannot_access_another_users_certificate(): void
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();

        $otherResume = Resume::factory()->create([
            'user_id' => $otherUser->id,
        ]);

        $version = ResumeVersion::factory()->create([
            'resume_id' => $otherResume->id,
            'version_number' => 1,
        ]);

        $certificate = ResumeCertificate::create([
            'resume_version_id' => $version->id,
            'name' => 'Private Certificate',
            'issuer' => 'Private Issuer',
            'credential_id' => 'PRIVATE-001',
            'issued_at' => '2026-01-15',
        ]);

        $response = $this->actingAs($user)
            ->getJson(
                "/api/resumes/{$otherResume->id}/certificates/{$certificate->id}"
            );

        $response->assertForbidden();
    }
}