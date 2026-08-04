<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $status->emailSubject() }}</title>
    <style>
        @media only screen and (max-width: 640px) {
            .email-shell { width: 100% !important; }
            .email-padding { padding-left: 24px !important; padding-right: 24px !important; }
            .email-title { font-size: 26px !important; line-height: 32px !important; }
            .detail-label, .detail-value { display: block !important; width: 100% !important; text-align: left !important; }
            .detail-value { padding-top: 5px !important; }
        }
    </style>
</head>
<body style="margin: 0; padding: 0; background-color: #f3f5f8; color: #26364d; font-family: Arial, Helvetica, sans-serif;">
    @php
        $statusStyles = match ($status) {
            App\Enums\ApplicationStatus::PENDING => ['background' => '#fff4d6', 'color' => '#9a5b00', 'accent' => '#e31e24'],
            App\Enums\ApplicationStatus::REVIEWING => ['background' => '#e7f1ff', 'color' => '#185ba8', 'accent' => '#2374c6'],
            App\Enums\ApplicationStatus::SHORTLISTED => ['background' => '#eeeafe', 'color' => '#5641b4', 'accent' => '#6e56cf'],
            App\Enums\ApplicationStatus::INTERVIEW => ['background' => '#fff0d9', 'color' => '#a6500a', 'accent' => '#df7b18'],
            App\Enums\ApplicationStatus::ACCEPTED => ['background' => '#e2f6e9', 'color' => '#157344', 'accent' => '#1a9c5c'],
            App\Enums\ApplicationStatus::REJECTED => ['background' => '#fde9e8', 'color' => '#b42318', 'accent' => '#d92d20'],
        };

        $statusMessage = match ($status) {
            App\Enums\ApplicationStatus::PENDING => 'We have received your application and our team will review it shortly.',
            App\Enums\ApplicationStatus::REVIEWING => 'Your application is now with our recruitment team for review. We will be in touch when there is an update.',
            App\Enums\ApplicationStatus::SHORTLISTED => 'Your experience stood out to our team. We are pleased to move you forward to the next stage.',
            App\Enums\ApplicationStatus::INTERVIEW => 'We are pleased to invite you to an interview. Our HR team will contact you with the scheduling details.',
            App\Enums\ApplicationStatus::ACCEPTED => 'Congratulations — your application has been accepted. Our HR team will contact you with your onboarding details.',
            App\Enums\ApplicationStatus::REJECTED => 'Thank you for the time and care you put into your application. We have decided to move forward with other candidates for this role.',
        };
    @endphp

    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="width: 100%; background-color: #f3f5f8;">
        <tr>
            <td align="center" style="padding: 36px 16px;">
                <table role="presentation" class="email-shell" width="600" cellpadding="0" cellspacing="0" border="0" style="width: 600px; max-width: 600px; background-color: #ffffff; border: 1px solid #dfe4ea; border-radius: 16px; overflow: hidden;">
                    <tr>
                        <td style="height: 5px; background-color: {{ $statusStyles['accent'] }}; font-size: 0; line-height: 0;">&nbsp;</td>
                    </tr>
                    <tr>
                        <td class="email-padding" style="padding: 30px 40px 26px; background-color: #0b2b5c;">
                            <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0">
                                <tr>
                                    <td style="vertical-align: middle;">
                                        <img src="{{ asset('logo.png') }}" alt="Kimmex" width="116" style="display: block; width: 116px; max-width: 116px; height: auto; border: 0;">
                                    </td>
                                    <td align="right" style="vertical-align: middle; color: #b9c7dd; font-size: 10px; font-weight: 700; letter-spacing: 1.6px; text-transform: uppercase;">
                                        Career update
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    <tr>
                        <td class="email-padding" style="padding: 42px 40px 36px;">
                            <p style="margin: 0 0 18px; color: #6e7b8f; font-size: 12px; font-weight: 700; letter-spacing: 1.4px; text-transform: uppercase;">Application update</p>
                            <h1 class="email-title" style="margin: 0 0 18px; color: #102f60; font-size: 30px; font-weight: 700; letter-spacing: -0.5px; line-height: 36px;">{{ $status->emailSubject() }}</h1>
                            <p style="margin: 0 0 24px; color: #43536b; font-size: 16px; line-height: 26px;">Hello {{ $applicantName }},</p>
                            <p style="margin: 0 0 28px; color: #43536b; font-size: 16px; line-height: 26px;">{{ $statusMessage }}</p>

                            <span style="display: inline-block; padding: 8px 13px; border-radius: 999px; background-color: {{ $statusStyles['background'] }}; color: {{ $statusStyles['color'] }}; font-size: 11px; font-weight: 700; letter-spacing: 0.9px; text-transform: uppercase;">{{ $status->getLabel() }}</span>

                            <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="margin-top: 30px; border: 1px solid #e2e8f0; border-radius: 10px; background-color: #f8fafc;">
                                <tr>
                                    <td class="detail-label" style="width: 34%; padding: 17px 20px; border-bottom: 1px solid #e2e8f0; color: #718096; font-size: 12px; font-weight: 700; letter-spacing: 0.5px; text-transform: uppercase;">Position</td>
                                    <td class="detail-value" align="right" style="padding: 17px 20px; border-bottom: 1px solid #e2e8f0; color: #173968; font-size: 14px; font-weight: 700;">{{ $jobTitle }}</td>
                                </tr>
                                <tr>
                                    <td class="detail-label" style="width: 34%; padding: 17px 20px; color: #718096; font-size: 12px; font-weight: 700; letter-spacing: 0.5px; text-transform: uppercase;">Application status</td>
                                    <td class="detail-value" align="right" style="padding: 17px 20px; color: #173968; font-size: 14px; font-weight: 700;">{{ $status->getLabel() }}</td>
                                </tr>
                            </table>

                            @if (filled($customMessage))
                                <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="margin-top: 24px; border-left: 4px solid {{ $statusStyles['accent'] }}; background-color: #f8fafc;">
                                    <tr>
                                        <td style="padding: 18px 20px;">
                                            <p style="margin: 0 0 8px; color: #173968; font-size: 12px; font-weight: 700; letter-spacing: 0.5px; text-transform: uppercase;">A note from our team</p>
                                            <p style="margin: 0; color: #43536b; font-size: 14px; line-height: 23px;">{!! nl2br(e($customMessage)) !!}</p>
                                        </td>
                                    </tr>
                                </table>
                            @endif

                            <p style="margin: 30px 0 0; color: #43536b; font-size: 14px; line-height: 23px;">For any questions, contact our recruitment team at <a href="mailto:recruitment@kimmex.com.kh" style="color: #d71920; font-weight: 700; text-decoration: none;">recruitment@kimmex.com.kh</a>.</p>
                        </td>
                    </tr>
                    <tr>
                        <td class="email-padding" style="padding: 24px 40px 30px; border-top: 1px solid #e2e8f0; background-color: #fbfcfe;">
                            <p style="margin: 0 0 6px; color: #173968; font-size: 12px; font-weight: 700;">Kimmex Construction &amp; Investment Co., Ltd.</p>
                            <p style="margin: 0; color: #7c899d; font-size: 11px; line-height: 18px;">This is an automated application update. Please do not reply directly to this email.</p>
                        </td>
                    </tr>
                </table>
                <p style="margin: 18px 0 0; color: #8a96a8; font-size: 11px; line-height: 18px;">&copy; {{ now()->year }} Kimmex Construction &amp; Investment Co., Ltd. All rights reserved.</p>
            </td>
        </tr>
    </table>
</body>
</html>
