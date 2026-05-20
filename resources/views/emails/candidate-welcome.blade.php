<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Welcome to Elements HR</title>
<style>
  body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif; background: #0F172A; color: #E2E8F0; margin: 0; padding: 0; }
  .wrapper { max-width: 600px; margin: 40px auto; background: #1E293B; border-radius: 16px; overflow: hidden; border: 1px solid #334155; }
  .header { background: linear-gradient(135deg, #7C3AED 0%, #06B6D4 100%); padding: 40px 32px; text-align: center; }
  .header h1 { margin: 0; color: #fff; font-size: 24px; font-weight: 700; }
  .header p { margin: 8px 0 0; color: rgba(255,255,255,0.8); font-size: 14px; }
  .body { padding: 32px; }
  .body p { color: #CBD5E1; font-size: 15px; line-height: 1.7; margin: 0 0 16px; }
  .highlight { background: #0F172A; border-left: 3px solid #7C3AED; padding: 16px 20px; border-radius: 8px; margin: 20px 0; }
  .highlight p { margin: 0; color: #C4B5FD; font-size: 14px; }
  .btn { display: inline-block; background: linear-gradient(135deg, #7C3AED, #06B6D4); color: #fff; text-decoration: none; padding: 14px 32px; border-radius: 10px; font-weight: 600; font-size: 15px; margin: 16px 0; }
  .footer { padding: 20px 32px; border-top: 1px solid #334155; text-align: center; }
  .footer p { color: #475569; font-size: 12px; margin: 0; }
</style>
</head>
<body>
<div class="wrapper">
  <div class="header">
    <h1>🎉 Welcome to Elements HR</h1>
    <p>Your AI-powered career journey starts here</p>
  </div>
  <div class="body">
    <p>Hi <strong>{{ $candidate->full_name }}</strong>,</p>
    <p>Your candidate profile has been created on Elements HR Services. Our AI engine has analyzed your resume and is now matching you with the best opportunities.</p>

    @if($isNewAccount && $plainPassword)
    <div class="highlight">
      <p><strong>Your account credentials:</strong><br>
      Email: {{ $candidate->email }}<br>
      Temporary password: <code>{{ $plainPassword }}</code></p>
      <p style="margin-top:8px; font-size:12px; color:#94A3B8;">Please change your password after your first login.</p>
    </div>
    @endif

    @if($candidate->ai_score)
    <div class="highlight">
      <p>🤖 <strong>Your AI Resume Score: {{ $candidate->ai_score }}/100</strong><br>
      @if(!empty($candidate->skills))Skills detected: {{ implode(', ', array_slice($candidate->skills, 0, 5)) }}@endif</p>
    </div>
    @endif

    <p>Start exploring personalized job recommendations curated specifically for your profile.</p>
    <a class="btn" href="{{ url('/user/jobs/recommendations') }}">Browse AI-Matched Jobs</a>
  </div>
  <div class="footer">
    <p>© {{ date('Y') }} Elements HR Services · <a href="{{ url('/') }}" style="color:#7C3AED;">Visit Platform</a></p>
  </div>
</div>
</body>
</html>
