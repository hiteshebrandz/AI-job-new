<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Application Update</title>
<style>
  body { font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif; background: #fcf8fa; color: #1b1b1d; margin: 0; padding: 0; }
  .wrapper { max-width: 600px; margin: 40px auto; background: #ffffff; border-radius: 16px; overflow: hidden; border: 1px solid #e4e2e4; box-shadow: 0 4px 6px -1px rgba(27,27,29,0.05); }
  .header { padding: 32px; }
  .status-badge { display: inline-block; padding: 6px 16px; border-radius: 999px; font-size: 13px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em; }
  .status-applied      { background: #eef0ff; color: #3730c4; }
  .status-shortlisted  { background: #ecfdf5; color: #047857; }
  .status-interview    { background: #e1e0ff; color: #3730c4; }
  .status-rejected     { background: #fef2f2; color: #b91c1c; }
  .status-hired        { background: #ecfdf5; color: #047857; }
  .status-under_review { background: #fffbeb; color: #b45309; }
  .body { padding: 0 32px 32px; }
  .body p { color: #45464d; font-size: 15px; line-height: 1.7; margin: 0 0 16px; }
  .job-card { background: #f6f3f5; border: 1px solid #e4e2e4; border-radius: 12px; padding: 20px; margin: 20px 0; }
  .job-card h3 { margin: 0 0 4px; color: #1b1b1d; font-size: 16px; font-family: 'Plus Jakarta Sans', sans-serif; }
  .job-card p  { margin: 0; color: #76777d; font-size: 13px; }
  .btn { display: inline-block; background: linear-gradient(135deg, #4648d4, #6063ee); color: #fff; text-decoration: none; padding: 14px 32px; border-radius: 10px; font-weight: 600; font-size: 15px; margin: 16px 0; }
  .footer { padding: 20px 32px; border-top: 1px solid #e4e2e4; text-align: center; }
  .footer p { color: #76777d; font-size: 12px; margin: 0; }
  .footer a { color: #4648d4; }
</style>
</head>
<body>
<div class="wrapper">
  <div class="header">
    <p style="color:#76777d; font-size:12px; text-transform:uppercase; letter-spacing:0.08em; margin:0 0 12px;">APPLICATION UPDATE</p>
    <h2 style="margin:0 0 12px; font-size:22px; color:#1b1b1d; font-family:'Plus Jakarta Sans',sans-serif;">Your application status has changed</h2>
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
    <p><strong>Congratulations!</strong> You've been selected for this position. The hiring team will be in touch shortly with next steps.</p>
    @elseif($application->status === \App\Models\JobApplication::STATUS_INTERVIEW)
    <p>You've been invited for an interview! Please check your dashboard for scheduling details.</p>
    @elseif($application->status === \App\Models\JobApplication::STATUS_SHORTLISTED)
    <p>Great news! Your profile stood out and you have been shortlisted. Stay tuned for the next steps.</p>
    @elseif($application->status === \App\Models\JobApplication::STATUS_REJECTED)
    <p>Thank you for applying. While your profile was impressive, we have decided to move forward with other candidates. Don't be discouraged — keep applying!</p>
    @else
    <p>Your application is currently under review. We'll notify you as soon as there's an update.</p>
    @endif

    <a class="btn" href="{{ url('/user/applied-jobs') }}">View All Applications</a>
  </div>
  <div class="footer">
    <p>&copy; {{ date('Y') }} {{ config('app.name') }} &middot; <a href="{{ url('/') }}">Visit Platform</a></p>
  </div>
</div>
</body>
</html>
