<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Notification')</title>
    <style>
        /* Base Reset */
        body, table, td, p, a, h1, h2, h3, h4, h5, h6 {
            margin: 0;
            padding: 0;
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
            -webkit-text-size-adjust: 100%;
            -ms-text-size-adjust: 100%;
        }
        
        table, td {
            mso-table-lspace: 0pt;
            mso-table-rspace: 0pt;
            border-collapse: collapse !important;
        }

        img {
            -ms-interpolation-mode: bicubic;
            border: 0;
            height: auto;
            line-height: 100%;
            outline: none;
            text-decoration: none;
            display: block;
        }

        body {
            background-color: #0b0e11; /* Deep dark background */
            color: #d1d5db; /* Light gray text */
            width: 100% !important;
            height: 100% !important;
        }

        /* Container specific */
        .wrapper {
            width: 100%;
            table-layout: fixed;
            background-color: #0b0e11;
            padding-bottom: 60px;
        }

        .main {
            background-color: #131722; /* Card background */
            margin: 0 auto;
            width: 100%;
            max-width: 600px;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.5);
            /* overflow: hidden; */
            margin-top: 40px;
        }

        /* Header */
        .header {
            padding: 40px 40px 20px 40px;
            text-align: center;
        }

        .header-logo {
            margin: 0 auto 20px auto;
            max-height: 45px;
        }

        .header-icon {
            width: 64px;
            height: 64px;
            margin: 0 auto 20px auto;
        }

        .title {
            color: #ffffff;
            font-size: 28px;
            font-weight: 600;
            line-height: 1.3;
            margin: 0;
            text-align: center;
        }

        /* Content */
        .content {
            padding: 20px 40px;
        }

        .text {
            color: #a3a8b3;
            font-size: 16px;
            line-height: 1.6;
            margin-bottom: 24px;
        }

        /* Data Tables (for receipts, etc.) */
        .data-table {
            width: 100%;
            margin-bottom: 30px;
            border-radius: 8px;
            background: #1e222d;
            padding: 20px;
        }

        .data-row {
            padding: 12px 0;
            border-bottom: 1px solid #2a2e39;
        }

        .data-row:last-child {
            border-bottom: none;
        }

        .data-label {
            color: #8c93a1;
            font-size: 14px;
            width: 50%;
        }

        .data-value {
            color: #ffffff;
            font-size: 14px;
            font-weight: 600;
            text-align: right;
            width: 50%;
        }

        /* Call to Action Button */
        .button-wrapper {
            text-align: center;
            margin: 40px 0;
        }

        .button {
            display: inline-block;
            background-color: #00d166; /* Fintech Green */
            color: #000000 !important;
            font-size: 16px;
            font-weight: 600;
            text-decoration: none;
            padding: 16px 36px;
            border-radius: 30px;
            transition: background-color 0.3s ease;
        }

        .button:hover {
            background-color: #00e570;
        }

        /* Footer */
        .footer {
            padding: 30px 40px 40px 40px;
            text-align: center;
            border-top: 1px solid #2a2e39;
        }

        .footer-text {
            color: #6b7280;
            font-size: 12px;
            line-height: 1.5;
            margin-bottom: 15px;
        }

        .disclaimer {
            color: #4b5563;
            font-size: 11px;
            line-height: 1.4;
            text-align: justify;
        }

        /* Utility */
        .text-highlight {
            color: #ffffff;
            font-weight: 600;
        }
        .text-green {
            color: #00d166;
        }

        @media screen and (max-width: 600px) {
            .main {
                width: 100% !important;
                border-radius: 0 !important;
                margin-top: 0 !important;
            }
            .header, .content, .footer {
                padding-left: 20px !important;
                padding-right: 20px !important;
            }
            .button {
                width: 100% !important;
                box-sizing: border-box;
            }
        }
    </style>
</head>
<body>
    <center class="wrapper">
        <table class="main" width="100%">
            <!-- Header Section -->
            <tr>
                <td class="header">
                    <!-- Logo Injection -->
                    <center>
                        @if(isset($message))
                            <img src="{{ $message->embed(public_path('assets/images/logo_dark.svg')) }}" alt="Logo" class="header-logo">
                        @else
                            <img src="{{ asset('assets/images/logo_dark.svg') }}" alt="Logo" class="header-logo">
                        @endif
                    </center>
                    
                    @hasSection('header-icon')
                        <center>
                            @yield('header-icon')
                        </center>
                    @endif

                    <h1 class="title">@yield('title')</h1>
                </td>
            </tr>

            <!-- Content Section -->
            <tr>
                <td class="content">
                    @if(isset($content))
                        {!! $content !!}
                    @else
                        @yield('content')
                    @endif
                </td>
            </tr>

            <!-- Footer Section -->
            <tr>
                <td class="footer">
                    <p class="footer-text">
                        If you have questions, please contact us at <a href="mailto:{{ site()->email ?? 'support@example.com' }}" style="color:#00d166;text-decoration:none;">{{ site()->email ?? 'support@example.com' }}</a>.
                    </p>
                    <p class="disclaimer">
                        Trading involves complex risks. There is a high risk of losing money rapidly due to leverage. You should consider whether you understand how CFDs work and whether you can afford to take the high risk of losing your money. All investments carry risks, and investors may lose more than their original investment. Information on this email is for reference only and not a recommendation.
                    </p>
                </td>
            </tr>
        </table>
    </center>
</body>
</html>
