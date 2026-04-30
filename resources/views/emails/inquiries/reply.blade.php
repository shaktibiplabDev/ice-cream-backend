<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $replyMessage->subject }}</title>
</head>
<body style="margin:0;background:#f4f9fe;font-family:Arial,sans-serif;color:#1a3a52;">
    <div style="max-width:620px;margin:0 auto;padding:28px 16px;">
        <div style="background:#ffffff;border:1px solid #d6eeff;border-radius:12px;overflow:hidden;">
            <div style="background:#c90000;padding:22px 26px;color:#ffffff;">
                <h1 style="margin:0;font-size:24px;">Celesty Ice Cream</h1>
                <p style="margin:6px 0 0;font-size:14px;">Reply for inquiry {{ $inquiry->displayNumber() }}</p>
            </div>

            <div style="padding:26px;">
                <p style="font-size:16px;line-height:1.6;margin:0 0 16px;">Hi {{ $inquiry->name }},</p>
                <div style="font-size:15px;line-height:1.7;color:#1a3a52;">
                    {!! nl2br(e($replyMessage->body)) !!}
                </div>

                <div style="background:#f4f9fe;border:1px solid #d6eeff;border-radius:10px;padding:14px;margin:24px 0 0;">
                    <div style="font-size:12px;text-transform:uppercase;color:#6ea0bf;font-weight:700;">Reference</div>
                    <div style="font-size:18px;font-weight:800;color:#1d7ab5;margin-top:4px;">{{ $inquiry->displayNumber() }}</div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
