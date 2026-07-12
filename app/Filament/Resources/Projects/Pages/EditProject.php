<?php

namespace App\Filament\Resources\Projects\Pages;

use App\Filament\Resources\Projects\ProjectResource;
use App\Mail\TestimonialRequestMail;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Mail;

class EditProject extends EditRecord
{
    use \LaraZeus\SpatieTranslatable\Resources\Pages\EditRecord\Concerns\Translatable;

    protected static string $resource = ProjectResource::class;

    protected function getHeaderActions(): array
    {
        return [
            \App\Filament\Support\AIHelper::getAutoFillAction('project'),
            Action::make('requestTestimonial')
                ->label('Request Testimonial')
                ->icon('heroicon-o-star')
                ->color('success')
                ->form([
                    TextInput::make('client_name')
                        ->label('Client Name')
                        ->required()
                        ->maxLength(255)
                        ->default(fn () => $this->record->client),
                    TextInput::make('client_email')
                        ->label('Client Email')
                        ->email()
                        ->required()
                        ->maxLength(255),
                ])
                ->action(function (array $data) {
                    Mail::to($data['client_email'])->send(
                        new TestimonialRequestMail(
                            project: $this->record,
                            clientName: $data['client_name'],
                            clientEmail: $data['client_email'],
                        )
                    );

                    Notification::make()
                        ->title('Testimonial request sent!')
                        ->body("An email has been sent to {$data['client_email']}.")
                        ->success()
                        ->send();
                })
                ->modalHeading('Request Client Testimonial')
                ->modalDescription('Send an email to your client asking for a testimonial about this project.')
                ->modalSubmitActionLabel('Send Request'),
            DeleteAction::make()->visible(fn () => auth()->user()?->isAdmin()),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
