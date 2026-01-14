<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Welcome to {{ config('app.name') }}</title>
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
            font-size: 28px;
            font-weight: bold;
            text-decoration: none;
        }
        .content {
            background: white;
            padding: 40px;
            border-radius: 0 0 10px 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        .welcome-title {
            color: #2d3748;
            font-size: 28px;
            margin-bottom: 20px;
        }
        .button {
            display: inline-block;
            padding: 12px 30px;
            background: #4299e1;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            font-weight: bold;
            margin: 20px 0;
        }
        .features {
            margin: 30px 0;
        }
        .feature-item {
            display: flex;
            align-items: center;
            margin-bottom: 15px;
        }
        .feature-icon {
            width: 24px;
            height: 24px;
            background: #4299e1;
            color: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 10px;
            font-size: 12px;
        }
        .footer {
            text-align: center;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #e2e8f0;
            color: #718096;
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
            <h1 class="welcome-title">Welcome, {{ $user->name }}! 👋</h1>
            
            <p>Thank you for joining {{ config('app.name') }}! We're excited to have you on board.</p>
            
            <p>
                Your account has been successfully created and you can now access all the features of our platform.
            </p>
            
            <div class="features">
                <div class="feature-item">
                    <div class="feature-icon">✓</div>
                    <span><strong>Multi-tenant SaaS platform</strong> - Your own isolated workspace</span>
                </div>
                <div class="feature-item">
                    <div class="feature-icon">✓</div>
                    <span><strong>Team collaboration</strong> - Invite team members and manage roles</span>
                </div>
                <div class="feature-item">
                    <div class="feature-icon">✓</div>
                    <span><strong>File storage</strong> - Upload and manage files securely</span>
                </div>
                <div class="feature-item">
                    <div class="feature-icon">✓</div>
                    <span><strong>Support system</strong> - Get help when you need it</span>
                </div>
                <div class="feature-item">
                    <div class="feature-icon">✓</div>
                    <span><strong>Subscription management</strong> - Choose plans that fit your needs</span>
                </div>
            </div>
            
            <div style="text-align: center;">
                <a href="{{ $loginUrl }}" class="button">
                    Access Your Dashboard
                </a>
            </div>
            
            <p>If you have any questions or need assistance, our support team is here to help.</p>
            
            <p>Best regards,<br>The {{ config('app.name') }} Team</p>
        </div>
        
        <div class="footer">
            <p>
                © {{ date('Y') }} {{ config('app.name') }}. All rights reserved.<br>
                <a href="{{ config('app.url') }}" style="color: #718096;">{{ config('app.url') }}</a>
            </p>
        </div>
    </div>
</body>
</html>