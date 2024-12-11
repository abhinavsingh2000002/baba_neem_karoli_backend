<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Password Reset OTP - BABA NEEM KAROLI TRADERS</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            line-height: 1.6;
            color: #2D3748;
            margin: 0;
            padding: 0;
            background-color: #EDF2F7;
        }
        .container {
            max-width: 600px;
            margin: 20px auto;
            background: #ffffff;
            border-radius: 16px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            overflow: hidden;
        }
        .header {
            background: linear-gradient(135deg, #4299E1 0%, #3182CE 100%);
            color: white;
            padding: 30px 20px;
            text-align: center;
        }
        .logo {
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 15px;
        }
        .logo-image {
            width: 60px;
            height: 60px;
            background: white;
            border-radius: 50%;
            padding: 10px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .content {
            padding: 40px 30px;
            text-align: center;
        }
        .otp-box {
            background: #F7FAFC;
            border: 2px dashed #4299E1;
            border-radius: 12px;
            padding: 20px;
            margin: 25px 0;
            font-size: 32px;
            letter-spacing: 8px;
            color: #2B6CB0;
            font-weight: 600;
        }
        .timer-box {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            color: #718096;
            margin: 15px 0;
        }
        .warning {
            color: #744210;
            font-size: 14px;
            margin-top: 25px;
            padding: 15px;
            background: #FEFCBF;
            border-radius: 8px;
            border-left: 4px solid #F6E05E;
        }
        .footer {
            background: #F7FAFC;
            padding: 25px;
            text-align: center;
            font-size: 13px;
            color: #718096;
        }
        .social-links {
            margin: 20px 0;
            display: flex;
            justify-content: center;
            gap: 15px;
        }
        .social-icon {
            width: 35px;
            height: 35px;
            background: #EDF2F7;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s ease;
        }
        .social-icon:hover {
            background: #E2E8F0;
            transform: translateY(-2px);
        }
        .button {
            display: inline-block;
            padding: 12px 24px;
            background: #4299E1;
            color: white;
            text-decoration: none;
            border-radius: 8px;
            font-weight: 500;
            margin-top: 20px;
            transition: all 0.3s ease;
        }
        .button:hover {
            background: #3182CE;
            transform: translateY(-2px);
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="logo">
                <img src="{{ asset('app-assets/images/logo.png') }}" alt="Logo" class="logo-image">
                <h1 style="margin: 0;">BABA NEEM KAROLI TRADERS</h1>
            </div>
        </div>

        <div class="content">
            <img src="{{ asset('app-assets/images/security.png') }}" alt="Security" style="width: 80px; margin-bottom: 20px;">
            <h2 style="color: #2B6CB0; margin-top: 0;">Password Reset OTP</h2>
            <p>Dear Valued Partner,</p>
            <p>You have requested to reset your password. Please use the following OTP to complete your password reset:</p>

            <div class="otp-box">
                <strong>{{ isset($otp) ? $otp : '123456' }}</strong>
            </div>

            <div class="timer-box">
                <svg width="20" height="20" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M10 0C4.477 0 0 4.477 0 10c0 5.522 4.477 10 10 10s10-4.478 10-10c0-5.523-4.477-10-10-10zm0 18c-4.411 0-8-3.589-8-8s3.589-8 8-8 8 3.589 8 8-3.589 8-8 8zm.5-13H9v6l5.25 3.15.75-1.23-4.5-2.67V5z"/>
                </svg>
                <span>This OTP will expire in <strong>5 minutes</strong></span>
            </div>

            <div class="warning">
                <svg width="20" height="20" fill="currentColor" viewBox="0 0 20 20" style="margin-right: 8px;">
                    <path d="M10 0C4.477 0 0 4.477 0 10c0 5.522 4.477 10 10 10s10-4.478 10-10c0-5.523-4.477-10-10-10zm1 15H9v-2h2v2zm0-4H9V5h2v6z"/>
                </svg>
                If you didn't request this password reset, please ignore this email or contact our support team.
            </div>

            <a href="#" class="button">Reset Password</a>
        </div>

        <div class="footer">
            <div class="social-links">
                <a href="#" class="social-icon">
                    <i class="fab fa-facebook-f"></i>
                </a>
                <a href="#" class="social-icon">
                    <i class="fab fa-twitter"></i>
                </a>
                <a href="#" class="social-icon">
                    <i class="fab fa-instagram"></i>
                </a>
            </div>
            <p>©  BABA NEEM KAROLI TRADERS. All rights reserved.</p>
            <p>This is an automated message, please do not reply to this email.</p>
            <p>
                <a href="#" style="color: #4299E1; text-decoration: none;">Terms of Service</a> |
                <a href="#" style="color: #4299E1; text-decoration: none;">Privacy Policy</a>
            </p>
        </div>
    </div>
</body>
</html>
