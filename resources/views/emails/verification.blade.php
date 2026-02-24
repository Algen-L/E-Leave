<!DOCTYPE html>
<html>

<head>
    <style>
        .email-container {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            max-width: 600px;
            margin: 0 auto;
            background-color: #f4f7f9;
            padding: 20px;
        }

        .card {
            background-color: #ffffff;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.08);
        }

        .header-gradient {
            background: linear-gradient(to left, #9ecdf8 0%, #005a8a 100%);
            padding: 40px 10px;
            text-align: center;
        }

        .header-table {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
        }

        .header-cell {
            vertical-align: middle;
            text-align: center;
            padding: 0;
        }

        .logo-img {
            height: 75px;
            width: auto;
            display: block;
            margin: 0 auto;
        }

        .header-title-cell {
            width: 70%;
        }

        .logo-side-cell {
            width: 15%;
        }

        .header-title h2 {
            margin: 0;
            color: #ffffff;
            font-family: 'Segoe UI', Arial, sans-serif;
            font-size: 20px;
            font-weight: 900;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            line-height: 1.25;
        }

        .content {
            padding: 40px 20px;
            text-align: center;
            color: #334155;
        }

        .code-label {
            font-size: 16px;
            color: #64748b;
            margin-bottom: 10px;
        }

        .code-box {
            display: inline-block;
            padding: 15px 30px;
            background-color: #f1f5f9;
            border: 2px dashed #1b6ca8;
            border-radius: 12px;
            font-size: 36px;
            font-weight: 800;
            letter-spacing: 6px;
            color: #003366;
            margin: 25px 0;
        }

        .footer {
            padding: 25px;
            background-color: #f8fafc;
            text-align: center;
            font-size: 12px;
            color: #94a3b8;
            border-top: 1px solid #e2e8f0;
        }
    </style>
</head>

<body>
    <div class='email-container'>
        <div class='card'>
            <div class='header-gradient'>
                <table class='header-table'>
                    <tr>
                        <td class='header-cell logo-side-cell'>
                            <img src="{{ $message->embed(public_path('images/logo.png')) }}" class='logo-img'
                                alt='DepEd Logo'>
                        </td>
                        <td class='header-cell header-title-cell'>
                            <div class='header-title'>
                                <h2>SCHOOLS DIVISION OFFICE<br>LEAVE APPLICATION SYSTEM</h2>
                            </div>
                        </td>
                        <td class='header-cell logo-side-cell'></td>
                    </tr>
                </table>
            </div>
            <div class='content'>
                <p style='font-size: 18px; font-weight: 500;'>Verification Code Request</p>
                <p class='code-label'>Please use the following 6-digit code to {{ $actionText }}:</p>
                <div class='code-box'>{{ $code }}</div>
                <p style='margin-top: 20px;'>If you didn't request this code, you can safely ignore this email.</p>
            </div>
            <div class='footer'>
                &copy; {{ date('Y') }} San Pedro Division Office - Learning & Development Unit<br>
                This is an automated message, please do not reply.
            </div>
        </div>
    </div>
</body>

</html>