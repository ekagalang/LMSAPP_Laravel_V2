<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password - BASS Training Center</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f8f9fa;
        }
        .email-container {
            background: white;
            padding: 40px;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 3px solid #DA1E1E;
        }
        .logo {
            font-size: 28px;
            font-weight: bold;
            color: #DA1E1E;
            margin-bottom: 10px;
        }
        .subtitle {
            color: #6c757d;
            font-size: 16px;
        }
        .content {
            margin-bottom: 30px;
        }
        .greeting {
            font-size: 18px;
            margin-bottom: 20px;
            color: #495057;
        }
        .message {
            font-size: 16px;
            margin-bottom: 20px;
            line-height: 1.7;
            color: #6c757d;
        }
        .button-container {
            text-align: center;
            margin: 30px 0;
        }
        .reset-button {
            display: inline-block;
            padding: 15px 30px;
            background: linear-gradient(135deg, #DA1E1E, #cfcbca);
            color: white;
            text-decoration: none;
            border-radius: 8px;
            font-weight: bold;
            font-size: 16px;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(0, 123, 255, 0.3);
        }
        .reset-button:hover {
            background: linear-gradient(135deg, #ffd2c7, #DA1E1E);
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(0, 123, 255, 0.4);
        }
        .warning {
            background: #fff3cd;
            border: 1px solid #ffeaa7;
            border-radius: 8px;
            padding: 15px;
            margin: 20px 0;
            font-size: 14px;
            color: #856404;
        }
        .footer {
            text-align: center;
            margin-top: 40px;
            padding-top: 20px;
            border-top: 1px solid #e9ecef;
            color: #6c757d;
            font-size: 14px;
        }
        .footer a {
            color: #007bff;
            text-decoration: none;
        }
        .support-info {
            background: #f2c5c2;
            border-left: 4px solid #DA1E1E;
            padding: 15px;
            margin: 20px 0;
            border-radius: 4px;
        }
        
        @media (max-width: 600px) {
            body {
                padding: 10px;
            }
            .email-container {
                padding: 20px;
            }
            .logo {
                font-size: 24px;
            }
            .reset-button {
                padding: 12px 25px;
                font-size: 14px;
            }
        }
    </style>
</head>
<body>
    <div class="email-container">
        <div class="header">
            <div class="logo">BASS Training Center</div>
            <div class="subtitle">Learning Management System</div>
        </div>
        
        <div class="content">
            <div class="greeting">Halo!</div>
            
            <div class="message">
                Anda menerima email ini karena kami menerima permintaan untuk mereset password akun Anda di <strong>BASS Training Center</strong>.
            </div>
            
            <div class="button-container">
                <a href="{{ $url }}" class="reset-button">Reset Password Sekarang</a>
            </div>
            
            <div class="warning">
                ⏱️ <strong>Penting:</strong> Link reset password ini hanya berlaku selama <strong>60 menit</strong>. Setelah itu, Anda perlu meminta link reset yang baru.
            </div>
            
            <div class="message">
                Jika Anda tidak meminta reset password, tidak ada tindakan lebih lanjut yang diperlukan. Akun Anda tetap aman.
            </div>
            
            <div class="support-info">
                <strong>Mengalami kendala?</strong><br>
                Jika tombol di atas tidak berfungsi, copy dan paste URL berikut di browser Anda:<br>
                <a href="{{ $url }}" style="word-break: break-all; color: #DA1E1E;">{{ $url }}</a>
            </div>
        </div>
        
        <div class="footer">
            <p>Email ini dikirim otomatis, mohon jangan membalas.</p>
            <p>
                Butuh bantuan? Hubungi kami di 
                <a href="mailto:help@basstrainingacademy.com">help@basstrainingacademy.com</a>
            </p>
            <p style="margin-top: 20px;">
                <strong>BASS Training Academy</strong><br>
                © {{ date('Y') }} All rights reserved.
            </p>
        </div>
    </div>
</body>
</html>