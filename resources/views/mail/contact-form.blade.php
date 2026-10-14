<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>New Portfolio Contact</title>
</head>
<body style="font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; margin: 0; padding: 20px; background-color: #f5f5f5;">
    <div style="max-width: 600px; margin: 0 auto; background-color: #ffffff; border-radius: 8px; padding: 32px; border: 1px solid #e5e5e5;">
        <h2 style="margin: 0 0 24px 0; font-size: 20px; color: #1a1a1a;">New message from your portfolio</h2>

        <table style="width: 100%; border-collapse: collapse;">
            <tr>
                <td style="padding: 8px 0; font-size: 14px; color: #666; width: 100px; vertical-align: top;">Name</td>
                <td style="padding: 8px 0; font-size: 14px; color: #1a1a1a;">{{ $senderName }}</td>
            </tr>
            <tr>
                <td style="padding: 8px 0; font-size: 14px; color: #666; width: 100px; vertical-align: top;">Email</td>
                <td style="padding: 8px 0; font-size: 14px; color: #1a1a1a;">
                    <a href="mailto:{{ $senderEmail }}">{{ $senderEmail }}</a>
                </td>
            </tr>
            <tr>
                <td style="padding: 8px 0; font-size: 14px; color: #666; width: 100px; vertical-align: top;">Subject</td>
                <td style="padding: 8px 0; font-size: 14px; color: #1a1a1a;">{{ $topic }}</td>
            </tr>
        </table>

        <hr style="border: none; border-top: 1px solid #e5e5e5; margin: 16px 0;">

        <div style="font-size: 14px; color: #1a1a1a; line-height: 1.6; white-space: pre-wrap;">{{ $body ?: '(no message)' }}</div>

        <hr style="border: none; border-top: 1px solid #e5e5e5; margin: 16px 0;">

        @if ($dashboardUrl)
            <p style="margin: 0 0 16px 0;">
                <a href="{{ $dashboardUrl }}" style="display: inline-block; background: #0d8a7a; color: #ffffff; text-decoration: none; padding: 10px 18px; border-radius: 999px; font-size: 14px;">Open in dashboard &amp; reply</a>
            </p>
        @endif

        <p style="font-size: 12px; color: #999; margin: 0;">
            Sent via your portfolio contact form on {{ now()->format('F j, Y \a\t g:i A') }}. You can also just hit Reply — it goes straight to {{ $senderName }}.
        </p>
    </div>
</body>
</html>
