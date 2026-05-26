<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Welcome to Elements HR</title>
<style>
  body { font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif; background: #fcf8fa; color: #1b1b1d; margin: 0; padding: 0; }
  .wrapper { max-width: 600px; margin: 40px auto; background: #ffffff; border-radius: 16px; overflow: hidden; border: 1px solid #e4e2e4; box-shadow: 0 4px 6px -1px rgba(27,27,29,0.05); }
  .header { background: linear-gradient(135deg, #4648d4 0%, #6063ee 100%); padding: 40px 32px; text-align: center; }
  .header h1 { margin: 0; color: #fff; font-size: 24px; font-weight: 700; font-family: 'Plus Jakarta Sans', sans-serif; }
  .header p { margin: 8px 0 0; color: rgba(255,255,255,0.85); font-size: 14px; }
  .body { padding: 32px; }
  .body p { color: #45464d; font-size: 15px; line-height: 1.7; margin: 0 0 16px; }
  .highlight { background: #f6f3f5; border-left: 3px solid #4648d4; padding: 16px 20px; border-radius: 8px; margin: 20px 0; }
  .highlight p { margin: 0; color: #3730c4; font-size: 14px; }
  .btn { display: inline-block; background: linear-gradient(135deg, #4648d4, #6063ee); color: #fff; text-decoration: none; padding: 14px 32px; border-radius: 10px; font-weight: 600; font-size: 15px; margin: 16px 0; }
  .footer { padding: 20px 32px; border-top: 1px solid #e4e2e4; text-align: center; }
  .footer p { color: #76777d; font-size: 12px; margin: 0; }
  .footer a { color: #4648d4; }
</style>
</head>
<body>
<div class="wrapper">
  <div class="header">
    <h1>Welcome to Elements HR</h1>
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
      <p style="margin-top:8px; font-size:12px; color:#76777d;">Please change your password after your first login.</p>
    </div>
    @endif

    @if($candidate->ai_score)
    <div class="highlight">
      <p><strong>Your AI Resume Score: {{ $candidate->ai_score }}/100</strong><br>
      @if(!empty($candidate->skills))Skills detected: {{ implode(', ', array_slice($candidate->skills, 0, 5)) }}@endif</p>
    </div>
    @endif

    <p>Start exploring personalized job recommendations curated specifically for your profile.</p>
    <a class="btn" href="{{ url('/user/jobs/recommendations') }}">Browse AI-Matched Jobs</a>
  </div>
  <div class="footer">
    <p>&copy; {{ date('Y') }} Elements HR Services &middot; <a href="{{ url('/') }}">Visit Platform</a></p>
  </div>
</div>
</body>
</html>
