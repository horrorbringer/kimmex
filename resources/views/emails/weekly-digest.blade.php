<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kimmex Weekly Digest</title>
    <style>
        body { margin: 0; padding: 0; background: #f8f7f4; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif; }
        .wrapper { max-width: 600px; margin: 0 auto; padding: 40px 20px; }
        .card { background: #fff; border-radius: 8px; border: 1px solid #e5e5e5; overflow: hidden; }
        .header { background: #0B2B5C; padding: 24px 32px; text-align: center; }
        .header p { color: rgba(255,255,255,0.5); font-size: 10px; margin: 0 0 4px; text-transform: uppercase; letter-spacing: 2px; }
        .header h1 { color: #fff; font-size: 14px; font-weight: 800; margin: 0; text-transform: uppercase; letter-spacing: 0.5px; }
        .body { padding: 32px; }
        .greeting { font-size: 14px; color: #0B2B5C; font-weight: 600; margin: 0 0 16px; }
        .intro { font-size: 13px; color: #666; line-height: 1.7; margin: 0 0 24px; }
        .section-title { font-size: 16px; font-weight: 700; color: #0B2B5C; margin: 0 0 16px; padding-bottom: 8px; border-bottom: 2px solid #E31E24; }
        .item { margin-bottom: 20px; padding-bottom: 20px; border-bottom: 1px solid #f0f0f0; }
        .item:last-child { border-bottom: none; margin-bottom: 0; padding-bottom: 0; }
        .item-title { font-size: 15px; font-weight: 700; color: #0B2B5C; margin: 0 0 6px; }
        .item-title a { color: #0B2B5C; text-decoration: none; }
        .item-title a:hover { color: #E31E24; }
        .item-meta { font-size: 11px; color: #999; margin: 0 0 6px; text-transform: uppercase; letter-spacing: 0.5px; }
        .item-excerpt { font-size: 13px; color: #555; line-height: 1.6; margin: 0 0 10px; }
        .item-link { font-size: 12px; font-weight: 600; color: #E31E24; text-decoration: none; }
        .item-link:hover { text-decoration: underline; }
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
                <h1>Weekly Digest</h1>
            </div>

            <div class="body">
                @if($subscriberName)
                    <p class="greeting">Hi {{ $subscriberName }},</p>
                @else
                    <p class="greeting">Hi there,</p>
                @endif

                <p class="intro">Here's a summary of what's new at Kimmex this week:</p>

                @if($articles->isNotEmpty())
                    <h2 class="section-title">📰 New Articles</h2>
                    @foreach($articles as $article)
                        <div class="item">
                            <h3 class="item-title">
                                <a href="{{ url('/news/' . $article->slug) }}">{{ $article->getTranslation('title', 'en') }}</a>
                            </h3>
                            @if($article->getTranslation('excerpt', 'en'))
                                <p class="item-excerpt">{{ Str::limit($article->getTranslation('excerpt', 'en'), 150) }}</p>
                            @endif
                            <a href="{{ url('/news/' . $article->slug) }}" class="item-link">Read More →</a>
                        </div>
                    @endforeach
                @endif

                @if($projects->isNotEmpty())
                    <h2 class="section-title" style="margin-top: 32px;">🏗️ New Projects</h2>
                    @foreach($projects as $project)
                        <div class="item">
                            <h3 class="item-title">
                                <a href="{{ url('/projects/' . $project->slug) }}">{{ $project->getTranslation('title', 'en') }}</a>
                            </h3>
                            @if($project->projectCategory)
                                <p class="item-meta">{{ $project->projectCategory->localizedName('en') }}</p>
                            @endif
                            <a href="{{ url('/projects/' . $project->slug) }}" class="item-link">View Project →</a>
                        </div>
                    @endforeach
                @endif
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
