<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title }}</title>
    <style>
        body { margin: 0; padding: 0; background: #f8f7f4; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif; }
        .wrapper { max-width: 600px; margin: 0 auto; padding: 40px 20px; }
        .card { background: #fff; border-radius: 8px; border: 1px solid #e5e5e5; overflow: hidden; }
        .header { background: #0B2B5C; padding: 24px 32px; text-align: center; }
        .header p { color: rgba(255,255,255,0.5); font-size: 10px; margin: 0 0 4px; text-transform: uppercase; letter-spacing: 2px; }
        .header h1 { color: #fff; font-size: 14px; font-weight: 800; margin: 0; text-transform: uppercase; letter-spacing: 0.5px; }
        .cover { width: 100%; height: 240px; object-fit: cover; display: block; }
        .body { padding: 32px; }
        .greeting { font-size: 14px; color: #0B2B5C; font-weight: 600; margin: 0 0 16px; }
        .intro { font-size: 13px; color: #666; line-height: 1.7; margin: 0 0 20px; }
        .article-title { font-size: 20px; font-weight: 800; color: #0B2B5C; margin: 0 0 12px; line-height: 1.3; }
        .article-excerpt { font-size: 14px; color: #555; line-height: 1.7; margin: 0 0 24px; }
        .btn { display: inline-block; background: #E31E24; color: #fff; padding: 12px 28px; border-radius: 6px; text-decoration: none; font-size: 12px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; }
        .footer { padding: 24px 32px; border-top: 1px solid #f0f0f0; text-align: center; }
        .footer p { font-size: 11px; color: #999; margin: 0 0 8px; }
        .footer a { color: #999; text-decoration: underline; }
    </style>
</head>
<body>
    <div class="wrapper">
        <div class="card">
            <div class="header">
                <p>Kimmex Construction & Investment</p>
                <h1>Latest News</h1>
            </div>

            @if($coverImage)
                <img src="{{ $coverImage }}" alt="{{ $title }}" class="cover" />
            @endif

            <div class="body">
                @if($subscriberName)
                    <p class="greeting">Hi {{ $subscriberName }},</p>
                @else
                    <p class="greeting">Hi there,</p>
                @endif

                @if($customIntro)
                    <p class="intro">{{ $customIntro }}</p>
                @else
                    <p class="intro">We've just published a new article we think you'll find interesting:</p>
                @endif

                <h2 class="article-title">{{ $title }}</h2>

                @if($excerpt)
                    <p class="article-excerpt">{{ $excerpt }}</p>
                @endif

                <a href="{{ $articleUrl }}" class="btn">Read Full Article →</a>
            </div>

            <div class="footer">
                <p>© {{ date('Y') }} Kimmex Construction & Investment Co., Ltd.</p>
                <p>You're receiving this because you subscribed to Kimmex updates.</p>
                <p><a href="{{ $unsubscribeUrl }}">Unsubscribe</a></p>
            </div>
        </div>
    </div>
</body>
</html>
