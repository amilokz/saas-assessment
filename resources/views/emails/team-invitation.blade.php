<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Team Invitation</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            padding: 40px 0;
            text-align: center;
            border-radius: 10px 10px 0 0;
        }
        .logo {
            color: white;
            font-size: 24px;
            font-weight: bold;
            text-decoration: none;
        }
        .content {
            background: white;
            padding: 40px;
            border-radius: 0 0 10px 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        .title {
            color: #2d3748;
            font-size: 24px;
            margin-bottom: 20px;
        }
        .info-box {
            background: #f7fafc;
            border-left: 4px solid #4299e1;
            padding: 15px;
            margin: 20px 0;
            border-radius: 4px;
        }
        .button {
            display: inline-block;
            padding: 12px 30px;
            background: #4299e1;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            font-weight: bold;
            margin: 10px 5px;
        }
        .button-secondary {
            background: #718096;
        }
        .footer {
            text-align: center;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #e2e8f0;
            color: #718096;
            font-size: 14px;
        }
        .expiry-notice {
            background: #fffaf0;
            border: 1px solid #fed7d7;
            padding: 10px;
            border-radius: 4px;
            margin: 20px 0;
            text-align: center;
            color: #c53030;
            font-size: 14px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <a href="{{ config('app.url') }}" class="logo">{{ config('app.name') }}</a>
        </div>
        
        <div class="content">
            <h1 class="title">You're Invited! 🎉</h1>
            
            <p>Hello,</p>
            
            <p>
                <strong>{{ $invitation->inviter->name }}</strong> has invited you to join 
                <strong>{{ $invitation->company->name }}</strong> on {{ config('app.name') }}.
            </p>
            
            <div class="info-box">
                <p><strong>Company:</strong> {{ $invitation->company->name }}</p>
                <p><strong>Invited By:</strong> {{ $invitation->inviter->name }}</p>
                <p><strong>Your Role:</strong> {{ $invitation->role->display_name ?? ucfirst(str_replace('_', ' ', $invitation->role->name)) }}</p>
                <p><strong>Invited Email:</strong> {{ $invitation->email }}</p>
            </div>
            
            <div class="expiry-notice">
                ⏰ This invitation expires on {{ $invitation->expires_at->format('F j, Y \a\t g:i A') }}
            </div>
            
            <div style="text-align: center; margin: 30px 0;">
                <a href="{{ $acceptUrl }}" class="button">
                    Accept Invitation
                </a>
                
                <a href="{{ $declineUrl }}" class="button button-secondary">
                    Decline Invitation
                </a>
            </div>
            
            <p>
                If you're having trouble clicking the "Accept Invitation" button, 
                copy and paste the URL below into your web browser:
            </p>
            
            <p style="word-break: break-all; color: #4a5568; background: #f7fafc; padding: 10px; border-radius: 4px;">
                {{ $acceptUrl }}
            </p>
            
            <p>
                If you didn't expect this invitation or believe it was sent in error, 
                please ignore this email or contact us.
            </p>
            
            <p>Welcome aboard!</p>
            <p>The {{ config('app.name') }} Team</p>
        </div>
        
        <div class="footer">
            <p>
                © {{ date('Y') }} {{ config('app.name') }}. All rights reserved.<br>
                <a href="{{ config('app.url') }}" style="color: #718096;">{{ config('app.url') }}</a>
            </p>
            <p style="font-size: 12px; margin-top: 10px;">
                This is an automated message, please do not reply to this email.
            </p>
        </div>
    </div>
</body>
</html>