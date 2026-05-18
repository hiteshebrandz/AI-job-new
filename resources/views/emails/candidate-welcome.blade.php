<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Welcome to Elements HR</title>
</head>
<body style="font-family: Inter, Arial, sans-serif; line-height: 1.6; color: #1a1c2e;">
    <h1 style="color: #4648d4;">Welcome to Elements HR Services</h1>
    <p>Hello {{ $candidate->full_name }},</p>
    @if ($isNewAccount && $plainPassword)
        <p>Your candidate profile has been created. Use the credentials below to sign in:</p>
        <ul>
            <li><strong>Email:</strong> {{ $candidate->email }}</li>
            <li><strong>Temporary password:</strong> {{ $plainPassword }}</li>
            <li><strong>Candidate ID:</strong> {{ $candidate->candidate_code }}</li>
        </ul>
        <p>Please change your password after your first login.</p>
    @else
        <p>Your resume has been parsed and your candidate profile is now active.</p>
        <p><strong>Candidate ID:</strong> {{ $candidate->candidate_code }}</p>
    @endif
    <p>
        <a href="{{ route('login') }}" style="display:inline-block;padding:12px 24px;background:#4648d4;color:#fff;text-decoration:none;border-radius:8px;">Sign in to your dashboard</a>
    </p>
    <p style="color:#666;font-size:14px;">© {{ date('Y') }} Elements HR Services</p>
</body>
</html>
