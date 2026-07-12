<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Share Your Experience</title>
    <style>
        body { margin: 0; padding: 0; background: #f8f7f4; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif; }
        .wrapper { max-width: 600px; margin: 0 auto; padding: 40px 20px; }
        .card { background: #fff; border-radius: 8px; border: 1px solid #e5e5e5; overflow: hidden; }
        .header { background: #0B2B5C; padding: 24px 32px; text-align: center; }
        .header p { color: rgba(255,255,255,0.5); font-size: 10px; margin: 0 0 4px; text-transform: uppercase; letter-spacing: 2px; }
        .header h1 { color: #fff; font-size: 14px; font-weight: 800; margin: 0; text-transform: uppercase; letter-spacing: 0.5px; }
        .body { padding: 32px; }
        .greeting { font-size: 14px; color: #0B2B5C; font-weight: 600; margin: 0 0 16px; }
        .intro { font-size: 13px; color: #666; line-height: 1.7; margin: 0 0 20px; }
        .project-name { font-size: 18px; font-weight: 800; color: #0B2B5C; margin: 0 0 12px; line-height: 1.3; }
        .message { font-size: 14px; color: #555; line-height: 1.7; margin: 0 0 24px; }
        .btn { display: inline-block; background: #E31E24; color: #fff; padding: 12px 28px; border-radius: 6px; text-decoration: none; font-size: 12px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; }
        .footer { padding: 24px 32px; border-top: 1px solid #f0f0f0; text-align: center; }
        .footer p { font-size: 11px; color: #999; margin: 0 0 8px; }
    </style>
</head>
<body>
    <div class="wrapper">
        <div class="card">
            <div class="header">
                <p>Kimmex Construction & Investment</p>
                <h1>Testimonial Request</h1>
            </div>

            <div class="body">
                <p class="greeting">Hi {{ $clientName }},</p>

                <p class="intro">Thank you for choosing Kimmex for your project. We are thrilled to have worked with you and hope the experience exceeded your expectations.</p>

                <p class="project-name">Project: {{ $projectTitle }}</p>

                <p class="message">We would be incredibly grateful if you could take a moment to share your experience working with us. Your feedback helps us improve and allows future clients to make informed decisions.</p>

                <a href="{{ $submitUrl }}" class="btn">Submit Your Testimonial →</a>

                <p style="font-size: 12px; color: #999; margin-top: 24px;">This link is unique to you and will expire in 30 days.</p>
            </div>

            <div class="footer">
                <p>© {{ date('Y') }} Kimmex Construction & Investment Co., Ltd.</p>
                <p>You received this email because your project with Kimmex was recently completed.</p>
            </div>
        </div>
    </div>
</body>
</html>
