<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $status->emailSubject() }}</title>
    <style>
        body { margin: 0; padding: 0; background: #f8f7f4; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif; }
        .wrapper { max-width: 600px; margin: 0 auto; padding: 40px 20px; }
        .card { background: #fff; border-radius: 8px; border: 1px solid #e5e5e5; overflow: hidden; }
        .header { background: #0B2B5C; padding: 28px 32px; }
        .header h1 { color: #fff; font-size: 18px; font-weight: 800; margin: 0; text-transform: uppercase; letter-spacing: 0.5px; }
        .header p { color: rgba(255,255,255,0.5); font-size: 11px; margin: 6px 0 0; text-transform: uppercase; letter-spacing: 1px; }
        .body { padding: 32px; }
        .greeting { font-size: 15px; color: #0B2B5C; font-weight: 700; margin: 0 0 16px; }
        .text { font-size: 14px; color: #555; line-height: 1.7; margin: 0 0 16px; }
        .status-badge { display: inline-block; padding: 6px 16px; border-radius: 20px; font-size: 12px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; }
        .status-PENDING { background: #f3f4f6; color: #6b7280; }
        .status-REVIEWING { background: #dbeafe; color: #1d4ed8; }
        .status-SHORTLISTED { background: #e0e7ff; color: #4338ca; }
        .status-INTERVIEW { background: #fef3c7; color: #d97706; }
        .status-ACCEPTED { background: #dcfce7; color: #16a34a; }
        .status-REJECTED { background: #fee2e2; color: #dc2626; }
        .details { background: #f8f7f4; border-radius: 6px; padding: 16px 20px; margin: 20px 0; }
        .details-row { display: flex; justify-content: space-between; padding: 6px 0; font-size: 13px; }
        .details-label { color: #999; font-weight: 600; }
        .details-value { color: #0B2B5C; font-weight: 700; }
        .custom-msg { background: #f0f9ff; border-left: 3px solid #0B2B5C; padding: 14px 18px; margin: 20px 0; border-radius: 0 6px 6px 0; }
        .custom-msg p { font-size: 13px; color: #333; line-height: 1.6; margin: 0; }
        .footer { padding: 24px 32px; border-top: 1px solid #f0f0f0; text-align: center; }
        .footer p { font-size: 11px; color: #999; margin: 0; }
    </style>
</head>
<body>
    <div class="wrapper">
        <div class="card">
            <div class="header">
                <p>Kimmex Construction & Investment</p>
                <h1>{{ $status->emailSubject() }}</h1>
            </div>
            <div class="body">
                <p class="greeting">Dear {{ $applicantName }},</p>

                @switch($status)
                    @case(App\Enums\ApplicationStatus::PENDING)
                        <p class="text">Thank you for submitting your application to Kimmex. We have received your documents and our team will review them shortly.</p>
                        @break
                    @case(App\Enums\ApplicationStatus::REVIEWING)
                        <p class="text">Your application is now being reviewed by our recruitment team. We will be in touch soon with an update.</p>
                        @break
                    @case(App\Enums\ApplicationStatus::SHORTLISTED)
                        <p class="text">Great news! You have been shortlisted for this position. Our team was impressed with your qualifications and will be in touch shortly regarding next steps.</p>
                        @break
                    @case(App\Enums\ApplicationStatus::INTERVIEW)
                        <p class="text">We are pleased to inform you that you have been selected for an interview. Our HR team will contact you shortly with scheduling details.</p>
                        @break
                    @case(App\Enums\ApplicationStatus::ACCEPTED)
                        <p class="text">Congratulations! We are delighted to inform you that your application has been accepted. Welcome to the Kimmex team! Our HR department will contact you with onboarding details.</p>
                        @break
                    @case(App\Enums\ApplicationStatus::REJECTED)
                        <p class="text">Thank you for your interest in joining Kimmex. After careful consideration, we have decided to proceed with other candidates for this position. We encourage you to apply for future openings.</p>
                        @break
                @endswitch

                <div style="margin: 20px 0;">
                    <span class="status-badge status-{{ $status->value }}">{{ $status->getLabel() }}</span>
                </div>

                <div class="details">
                    <div class="details-row">
                        <span class="details-label">Position</span>
                        <span class="details-value">{{ $jobTitle }}</span>
                    </div>
                    <div class="details-row">
                        <span class="details-label">Applicant</span>
                        <span class="details-value">{{ $applicantName }}</span>
                    </div>
                    <div class="details-row">
                        <span class="details-label">Status</span>
                        <span class="details-value">{{ $status->getLabel() }}</span>
                    </div>
                </div>

                @if($customMessage)
                    <div class="custom-msg">
                        <p><strong>Message from our team:</strong><br>{{ $customMessage }}</p>
                    </div>
                @endif

                <p class="text">If you have any questions, please don't hesitate to contact us at <a href="mailto:recruitment@kimmex.com.kh" style="color: #E31E24;">recruitment@kimmex.com.kh</a>.</p>
            </div>
            <div class="footer">
                <p>© {{ date('Y') }} Kimmex Construction & Investment Co., Ltd. All rights reserved.</p>
            </div>
        </div>
    </div>
</body>
</html>
