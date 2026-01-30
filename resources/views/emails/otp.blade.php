<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Email Verification OTP</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }
        .email-container {
            max-width: 600px;
            margin: 30px auto;
            background-color: #ffffff;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }
        .email-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px;
            text-align: center;
        }
        .email-body {
            padding: 40px 30px;
        }
        .otp-box {
            background-color: #f8f9fa;
            border: 2px dashed #667eea;
            border-radius: 10px;
            padding: 20px;
            text-align: center;
            margin: 30px 0;
        }
        .otp-code {
            font-size: 36px;
            font-weight: bold;
            color: #667eea;
            letter-spacing: 8px;
            margin: 10px 0;
        }
        .email-footer {
            background-color: #f8f9fa;
            padding: 20px;
            text-align: center;
            color: #6c757d;
            font-size: 14px;
        }
        .btn {
            display: inline-block;
            padding: 12px 30px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            text-decoration: none;
            border-radius: 5px;
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <div class="email-container">
        <div class="email-header">
            <h1 style="margin: 0;">🚚 Dispatch Scheduling System</h1>
            <p style="margin: 10px 0 0 0;">Email Verification</p>
        </div>
        
        <div class="email-body">
            <h2 style="color: #333;">Hello {{ $name }}!</h2>
            
            <p style="color: #666; line-height: 1.6;">
                Thank you for registering with Dispatch Scheduling System. To complete your registration 
                and verify your email address, please use the following One-Time Password (OTP):
            </p>
            
            <div class="otp-box">
                <p style="margin: 0; color: #666;">Your OTP Code:</p>
                <div class="otp-code">{{ $otp }}</div>
                <p style="margin: 10px 0 0 0; color: #999; font-size: 14px;">
                    Valid for 10 minutes
                </p>
            </div>
            
            <p style="color: #666; line-height: 1.6;">
                <strong>Important:</strong> Do not share this OTP with anyone. Our team will never ask 
                you for this code via email, phone, or any other medium.
            </p>
            
            <p style="color: #666; line-height: 1.6;">
                If you did not request this verification, please ignore this email.
            </p>
        </div>
        
        <div class="email-footer">
            <p style="margin: 5px 0;">© 2024 Dispatch Scheduling System. All rights reserved.</p>
            <p style="margin: 5px 0;">This is an automated email. Please do not reply.</p>
        </div>
    </div>
</body>
</html>