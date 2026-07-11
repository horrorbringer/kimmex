<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome to Kimmex Newsletter</title>
    <style>
        body { margin: 0; padding: 0; background: #f8f7f4; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif; }
        .wrapper { max-width: 600px; margin: 0 auto; padding: 40px 20px; }
        .card { background: #fff; border-radius: 8px; border: 1px solid #e5e5e5; overflow: hidden; }
        .header { background: #0B2B5C; padding: 28px 32px; text-align: center; }
        .header p { color: rgba(255,255,255,0.5); font-size: 10px; margin: 0 0 6px; text-transform: uppercase; letter-spacing: 2px; }
        .header h1 { color: #fff; font-size: 18px; font-weight: 800; margin: 0; }
        .body { padding: 32px; }
        .greeting { font-size: 15px; color: #0B2B5C; font-weight: 700; margin: 0 0 16px; }
        .text { font-size: 14px; color: #555; line-height: 1.8; margin: 0 0 16px; }
        .highlight-box { background: #f8f7f4; border-radius: 8px; padding: 20px 24px; margin: 24px 0; }
        .highlight-box h3 { font-size: 12px; color: #0B2B5C; text-transform: uppercase; letter-spacing: 1px; margin: 0 0 12px; }
        .highlight-box ul { margin: 0; padding: 0; list-style: none; }
        .highlight-box li { font-size: 13px; color: #555; padding: 6px 0; border-bottom: 1px solid #eee; }
        .highlight-box li:last-child { border-bottom: none; }
        .highlight-box li a { color: #E31E24; text-decoration: none; font-weight: 600; }
        .btn { display: inline-block; background: #E31E24; color: #fff; padding: 12px 28px; border-radius: 6px; text-decoration: none; font-size: 13px; font-weight: 700; text-transform: uppercase; letter-spacing: 1px; margin-top: 8px; }
        .footer { padding: 24px 32px; border-top: 1px solid #f0f0f0; text-align: center; }
        .footer p { font-size: 11px; color: #999; margin: 0 0 4px; }
        .footer a { color: #999; text-decoration: underline; }
    </style>
</head>
<body>
    <div class="wrapper">
        <div class="card">
            <div class="header">
                <p>Kimmex Construction & Investment</p>
                <h1>Welcome to Our Newsletter!</h1>
            </div>
            <div class="body">
                <p class="greeting">{{ $subscriberName ? "Hi {$subscriberName}," : 'Hi there,' }}</p>

                <p class="text">
                    Thank you for subscribing to the Kimmex newsletter. You'll now receive updates on our latest projects, company news, and industry insights directly in your inbox.
                </p>

                <div class="highlight-box">
                    <h3>While you're here, explore:</h3>
                    <ul>
                        <li><a href="{{ $projectsUrl }}">Our Projects</a> — See what we've built across Cambodia</li>
                        <li><a href="{{ $newsUrl }}">News & Updates</a> — Latest announcements and milestones</li>
                        <li><a href="{{ $websiteUrl }}/about">About Us</a> — 25+ years of construction excellence</li>
                    </ul>
                </div>

                <p class="text">
                    We respect your inbox — you'll only hear from us when we have something valuable to share.
                </p>

                <a href="{{ $websiteUrl }}" class="btn">Visit Our Website</a>
            </div>
            <div class="footer">
                <p>&copy; {{ date('Y') }} Kimmex Construction & Investment Co., Ltd.</p>
                <p>You're receiving this because you subscribed with {{ $subscriberEmail }}.</p>
                <p><a href="{{ $unsubscribeUrl }}">Unsubscribe</a></p>
            </div>
        </div>
    </div>
</body>
</html>
