<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>New Celesty inquiry</title>
</head>
<body style="margin:0;background:#f4f9fe;font-family:Arial,sans-serif;color:#1a3a52;">
    <div style="max-width:620px;margin:0 auto;padding:28px 16px;">
        <div style="background:#ffffff;border:1px solid #d6eeff;border-radius:12px;overflow:hidden;">
            <div style="background:#1d7ab5;padding:22px 26px;color:#ffffff;">
                <h1 style="margin:0;font-size:24px;">New inquiry</h1>
                <p style="margin:6px 0 0;font-size:14px;">{{ $inquiry->displayNumber() }}</p>
            </div>

            <div style="padding:26px;">
                <p style="font-size:15px;line-height:1.7;margin:0 0 12px;"><strong>Name:</strong> {{ $inquiry->name }}</p>
                <p style="font-size:15px;line-height:1.7;margin:0 0 12px;"><strong>Business:</strong> {{ $inquiry->business_name ?: 'Not provided' }}</p>
                <p style="font-size:15px;line-height:1.7;margin:0 0 18px;"><strong>Email:</strong> {{ $inquiry->email }}</p>

                <div style="background:#f4f9fe;border:1px solid #d6eeff;border-radius:10px;padding:16px;">
                    {!! nl2br(e($inquiry->requirement)) !!}
                </div>
            </div>
        </div>
    </div>
</body>
</html>
