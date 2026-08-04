<?php

namespace Tests\Feature;

use App\Enums\ApplicationStatus;
use App\Filament\Resources\JobApplications\Pages\ListJobApplications;
use App\Models\JobApplication;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class JobApplicationStatusInlineEditTest extends TestCase
{
    use RefreshDatabase;

    public function test_an_admin_can_update_an_application_status_from_the_list_table(): void
    {
        $admin = User::factory()->create([
            'role' => 'ADMIN',
            'is_active' => true,
        ]);
        $application = JobApplication::create([
            'applicantName' => 'Sok Chan',
            'email' => 'sok.chan@example.com',
            'phone' => '012 345 678',
            'resumeUrl' => 'resumes/sok-chan.pdf',
            'status' => ApplicationStatus::PENDING,
            'submittedAt' => now(),
        ]);

        Livewire::actingAs($admin)
            ->test(ListJobApplications::class)
            ->assertTableSelectColumnHasOptions(
                'status',
                collect(ApplicationStatus::cases())
                    ->mapWithKeys(fn (ApplicationStatus $status): array => [$status->value => $status->getLabel()])
                    ->all(),
                $application,
            )
            ->call('updateTableColumnState', 'status', $application->getKey(), ApplicationStatus::INTERVIEW->value)
            ->assertHasNoErrors();

        $this->assertDatabaseHas('job_applications', [
            'id' => $application->getKey(),
            'status' => ApplicationStatus::INTERVIEW->value,
        ]);
    }
}
