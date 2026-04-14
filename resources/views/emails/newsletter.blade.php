<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0"/>
<title>{{ $subject }}</title>
<style>
  body  { margin:0; padding:0; background:#f4f4f4; font-family:Arial,sans-serif; }
  .wrap { max-width:620px; margin:30px auto; background:#fff; border-radius:8px; overflow:hidden; box-shadow:0 2px 8px rgba(0,0,0,.08); }
  .hdr  { background: linear-gradient(135deg, #2ECC71 0%, #F1C40F 100%); padding:24px 32px; text-align:center; }
  .hdr img { height:40px; }
  .body { padding:32px; color:#374151; font-size:15px; line-height:1.7; }
  .body h1 { font-size:22px; color:#1a202c; margin-top:0; }
  .body a { color:#2ECC71; text-decoration:none; }
  .body a:hover { color:#27AE60; text-decoration:underline; }
  .ftr  { background:#f9fafb; padding:20px 32px; text-align:center; font-size:12px; color:#9ca3af; border-top:1px solid #e5e7eb; }
  .btn { display:inline-block; background:#2ECC71; color:#fff!important; padding:12px 28px; text-decoration:none; border-radius:6px; font-weight:bold; margin:16px 0; }
  .btn:hover { background:#27AE60; }
</style> 
</head>
<body>
<div class="wrap">
    <div class="hdr">
        <img src="{{ asset('dashboard/assets/images/favicon.png') }}" alt="Orderer" />
    </div>
    <div class="body">
        <p>Hi{{ $recipientName ? ' '.$recipientName : '' }},</p>
        {!! $body !!}
        <p style="margin-top:32px;color:#6b7280;font-size:13px;">
            You are receiving this email because you have an account with us.
        </p>
    </div>
    <div class="ftr">
        &copy; {{ date('Y') }} Orderer. All rights reserved.
    </div>
</div>
</body>
</html>