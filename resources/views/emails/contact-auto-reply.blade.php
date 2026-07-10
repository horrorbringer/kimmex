<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>We received your message</title>
    <style>
        body { margin: 0; padding: 0; background: #f8f7f4; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif; }
        .wrapper { max-width: 600px; margin: 0 auto; padding: 40px 20px; }
        .card { background: #fff; border-radius: 8px; border: 1px solid #e5e5e5; overflow: hidden; }
        .header { background: #0B2B5C; padding: 24px 32px; }
        .header p { color: rgba(255,255,255,0.5); font-size: 10px; margin: 0 0 4px; text-transform: uppercase; letter-spacing: 2px; }
        .header h1 { color: #fff; font-size: 16px; font-weight: 800; margin: 0; }
        .body { padding: 32px; }
        .greeting { font-size: 15px; color: #0B2B5C; font-weight: 700; margin: 0 0 16px; }
        .text { font-size: 14px; color: #555; line-height: 1.7; margin: 0 0 16px; }
        .quote-box { background: #f8f7f4; border-left: 3px solid #E31E24; padding: 16px 20px; border-radius: 0 6px 6px 0; margin: 20px 0; }
        .quote-label { font-size: 10px; color: #999; text-transform: uppercase; letter-spacing: 1px; font-weight: 700; margin: 0 0 6px; }
        .quote-text { font-size: 13px; color: #333; line-height: 1.6; margin: 0; font-style: italic; }
        .footer { padding: 24px 32px; border-top: 1px solid #f0f0f0; text-align: center; }
        .footer p { font-size: 11px; color: #999; margin: 0 0 4px; }
    </style>
</head>
<body>
    <div class="wrapper">
        <div class="card">
            <div class="header">
                <p>Kimmex Construction & Investment</p>
                <h1>Thank you for contacting us</h1>
            </div>
            <div class="body">
                <p class="greeting">Dear {{ $name }},</p>

                <p class="text">Thank you for reaching out to Kimmex. We have received your message and our team will review it within 24 business hours.</p>

                <p class="text">Here's a copy of what you sent us:</p>

                @if($subject)
                <div class="quote-box">
                    <p class="quote-label">Subject</p>
                    <p class="quote-text">{{ $subject }}</p>
                </div>
                @endif

                <div class="quote-box">
                    <p class="quote-label">Your Message</p>
                    <p class="quote-text">{{ Str::limit($message, 500) }}</p>
                </div>

                <p class="text">If your matter is urgent, please call us directly at <strong>+855 23 999 999</strong>.</p>

                <p class="text">Best regards,<br><strong>Kimmex Team</strong></p>
            </div>
            <div class="footer">
                <p>© {{ date('Y') }} Kimmex Construction & Investment Co., Ltd.</p>
                <p>This is an automated confirmation. Please do not reply to this email.</p>
            </div>
        </div>
    </div>
</body>
</html>
