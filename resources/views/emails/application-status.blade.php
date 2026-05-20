<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Application Update</title>
<style>
  body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif; background: #0F172A; color: #E2E8F0; margin: 0; padding: 0; }
  .wrapper { max-width: 600px; margin: 40px auto; background: #1E293B; border-radius: 16px; overflow: hidden; border: 1px solid #334155; }
  .header { padding: 32px; }
  .status-badge { display: inline-block; padding: 6px 16px; border-radius: 999px; font-size: 13px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em; }
  .status-applied      { background: #1E3A5F; color: #60A5FA; }
  .status-shortlisted  { background: #1B4332; color: #34D399; }
  .status-interview    { background: #312E81; color: #C4B5FD; }
  .status-rejected     { background: #450A0A; color: #F87171; }
  .status-hired        { background: #14532D; color: #86EFAC; }
  .status-under_review { background: #1C1917; color: #D4A574; }
  .body { padding: 0 32px 32px; }
  .body p { color: #CBD5E1; font-size: 15px; line-height: 1.7; margin: 0 0 16px; }
  .job-card { background: #0F172A; border: 1px solid #334155; border-radius: 12px; padding: 20px; margin: 20px 0; }
  .job-card h3 { margin: 0 0 4px; color: #E2E8F0; font-size: 16px; }
  .job-card p  { margin: 0; color: #64748B; font-size: 13px; }
  .btn { display: inline-block; background: linear-gradient(135deg, #7C3AED, #06B6D4); color: #fff; text-decoration: none; padding: 14px 32px; border-radius: 10px; font-weight: 600; font-size: 15px; margin: 16px 0; }
  .footer { padding: 20px 32px; border-top: 1px solid #334155; text-align: center; }
  .footer p { color: #475569; font-size: 12px; margin: 0; }
</style>
</head>
<body>
<div class="wrapper">
  <div class="header">
    <p style="color:#64748B; font-size:12px; text-transform:uppercase; letter-spacing:0.08em; margin:0 0 12px;">APPLICATION UPDATE</p>
    <h2 style="margin:0 0 12px; font-size:22px; color:#E2E8F0;">Your application status has changed</h2>
    <span class="status-badge status-{{ $application->status }}">
      {{ \App\Models\JobApplication::statusLabel($application->status) }}
    </span>
  </div>
  <div class="body">
    <p>Hi <strong>{{ $application->user->name }}</strong>,</p>
    <p>We have an update on your application for the following position:</p>

    <div class="job-card">
      <h3>{{ $application->job->title }}</h3>
      <p>{{ $application->job->company_name }}</p>
    </div>

    @if($application->status === \App\Models\JobApplication::STATUS_HIRED)
    <p>🎉 <strong>Congratulations!</strong> You've been selected for this position. The hiring team will be in touch shortly with next steps.</p>
    @elseif($application->status === \App\Models\JobApplication::STATUS_INTERVIEW)
    <p>📅 You've been invited for an interview! Please check your dashboard for scheduling details.</p>
    @elseif($application->status === \App\Models\JobApplication::STATUS_SHORTLISTED)
    <p>✅ Great news! Your profile stood out and you have been shortlisted. Stay tuned for the next steps.</p>
    @elseif($application->status === \App\Models\JobApplication::STATUS_REJECTED)
    <p>Thank you for applying. While your profile was impressive, we have decided to move forward with other candidates. Don't be discouraged — keep applying!</p>
    @else
    <p>Your application is currently under review. We'll notify you as soon as there's an update.</p>
    @endif

    <a class="btn" href="{{ url('/user/applied-jobs') }}">View All Applications</a>
  </div>
  <div class="footer">
    <p>© {{ date('Y') }} Elements HR Services · <a href="{{ url('/') }}" style="color:#7C3AED;">Visit Platform</a></p>
  </div>
</div>
</body>
</html>
