<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>MHIS Visit Confirmation</title>
    <style>
        body {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: Arial, sans-serif;
            background: linear-gradient(135deg, #f5f7fa 0%, #e8ecf1 100%);
            min-height: 100%;
            padding: 20px;
            text-align: justify;
        }

        * {
            box-sizing: border-box;
        }

        .email-container {
            max-width: 650px;
            margin: 0 auto;
            background: #ffffff;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.1);
        }

        .header {
            background: linear-gradient(135deg,
                    rgb(128, 0, 0) 0%,
                    rgb(153, 27, 27) 100%);
            padding: 40px 30px;
            display: flex;
            align-items: center;
            justify-content: flex-start;
            vertical-align: middle;
            width: 100%;
        }

        .logo {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            background: white;
            display: flex;
            height: auto;
            flex-shrink: 0;
        }

        .subject {
            color: #ffffff;
            font-size: 24px;
            font-weight: 600;
            letter-spacing: 0.5px;
            padding-left: 10px;
            text-align: center;
            vertical-align: middle;
            flex: 1;
        }

        .content {
            padding: 40px 30px;
        }

        .greeting {
            font-style: italic;
            color: #991b1b;
            margin-bottom: 20px;
            font-size: 16px;
        }

        .salutation {
            color: #1a202c;
            font-size: 16px;
            margin-bottom: 10px;
            font-weight: 500;
        }

        .intro-text {
            color: #4a5568;
            line-height: 1.8;
            margin-bottom: 30px;
            font-size: 15px;
        }

        .visit-details {
            background: #fef7f7;
            border-left: 4px solid #800000;
            padding: 25px;
            margin: 30px 0;
            border-radius: 6px;
        }

        .detail-row {
            display: flex;
            margin-bottom: 15px;
            font-size: 15px;
        }

        .detail-row:last-child {
            margin-bottom: 0;
        }

        .detail-label {
            font-weight: 600;
            color: #800000;
            min-width: 80px;
        }

        .unique-code {
            font-family: 'Courier New', monospace;
            font-weight: bold;
            font-size: 16px;
            letter-spacing: 2px;
            background: #f1f5f9;
            padding: 4px 8px;
            border-radius: 4px;
            border: 1px solid #e2e8f0;
        }

        .detail-value {
            color: #4a5568;
            flex: 1;
        }

        .rules-section {
            margin: 30px 0;
        }

        .rules-intro {
            color: #1a202c;
            font-weight: 600;
            margin-bottom: 20px;
            font-size: 15px;
        }

        .rule-item {
            margin-bottom: 20px;
            padding-left: 30px;
            position: relative;
        }

        .rule-item::before {
            content: "•";
            position: absolute;
            left: 10px;
            color: #2c5282;
            font-size: 20px;
            font-weight: bold;
        }

        .rule-english {
            color: #1a202c;
            margin-bottom: 5px;
            line-height: 1.6;
            font-size: 14px;
        }

        .rule-indonesian {
            color: #718096;
            font-style: italic;
            line-height: 1.6;
            font-size: 13px;
        }

        .closing {
            color: #4a5568;
            line-height: 1.8;
            margin: 30px 0;
            font-size: 15px;
        }

        .farewell {
            font-style: italic;
            color: #991b1b;
            margin: 25px 0;
            font-size: 15px;
        }

        .divider {
            border: 0;
            height: 2px;
            background: linear-gradient(to right,
                    transparent,
                    #cbd5e0,
                    transparent);
            margin: 30px 0;
        }

        .footer {
            background: #fef7f7;
            padding: 30px;
            text-align: center;
            border-top: 3px solid #800000;
        }

        .signature {
            color: #1a202c;
            font-weight: 600;
            font-size: 16px;
            margin-bottom: 10px;
        }

        .contact {
            color: #718096;
            font-size: 14px;
        }

        .social-media {
            margin-top: 20px;
            display: flex;
            justify-content: center;
            text-align: center;
            gap: 15px;
        }

        .social-link {
            display: inline-block;
            transition: transform 0.3s ease, opacity 0.3s ease;
        }

        .social-link:hover {
            transform: translateY(-3px);
            opacity: 0.8;
        }

        .social-icon {
            width: 32px;
            height: 32px;
            border-radius: 50%;
        }

        @media (max-width: 600px) {
            .email-container {
                border-radius: 0;
            }

            .header {
                padding: 30px 20px;
                flex-direction: column;
                text-align: center;
                gap: 20px;
            }

            .logo {
                width: 80px;
                height: 80px;
                border-radius: 50%;
                background: white;
                display: flex;
            }

            .subject {
                text-align: center;
                padding-left: 5px;
                font-size: 20px;
                vertical-align: middle;
            }

            .content {
                padding: 30px 20px;
            }

            .visit-details {
                padding: 20px;
            }

            .detail-row {
                flex-direction: column;
            }

            .detail-label {
                margin-bottom: 5px;
            }

            .footer {
                padding: 25px 20px;
            }
        }
    </style>
</head>

<body>
    <div class="email-container">
        <div class="header">
            <img src="https://admission.mhis.link/assets/images/logo.png" alt="MHIS Logo" height="100%" width="100%"
                class="logo" onerror="this.style.display='none';" />
            <h1 class="subject">MHIS School Visit Confirmation</h1>
        </div>
        <div class="content">
            <p class="greeting" id="greeting">
                Assalamualaikum Warahmatullahi Wabarakatuh
            </p>
            <p class="salutation" id="salutation">Dear Mr./Mrs. {{ $data['parent_name'] }},</p>
            <p class="intro-text" id="intro-text">
                Warm greetings from Mutiara Harapan Islamic School. We are pleased to
                confirm that we have received your request to visit our {{ $data['level'] }} MHIS
                on:
            </p>
            <div class="visit-details">
                <div class="detail-row">
                    <span class="detail-label" id="date-label">Date:</span>
                    <span class="detail-value">{{ $data['dateDate'] }}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label" id="time-label">Time:</span>
                    <span class="detail-value">{{ $data['time'] }}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label" id="code-label">Code:</span>
                    <span class="detail-value unique-code" id="unique-code"><strong>{{ $data['code'] }}</strong></span>
                </div>
            </div>
            <div class="rules-section">
                <p class="rules-intro" id="rules-intro">
                    By confirming your visit, you acknowledge and agree to the following
                    school rules:
                </p>
                <ul>
                    <li>
                        <div class="rule-english">Comply with school regulations.</div>
                        <div class="rule-indonesian">Mematuhi peraturan sekolah.</div>
                    </li>
                    <li>
                        <div class="rule-english">Comply with school regulations.</div>
                        <div class="rule-indonesian">Mematuhi peraturan sekolah.</div>
                    </li>
                    <li>
                        <div class="rule-english">
                            Refrain from taking pictures or videos of students and staff
                            within the school premises.
                        </div>
                        <div class="rule-indonesian">
                            Tidak mengambil foto atau video siswa dan staf di lingkungan
                            sekolah.
                        </div>
                    </li>
                    <li>
                        <div class="rule-english">
                            Dress modestly: long trousers for men and long trousers or
                            ankle-length skirts/dresses for women, with at least short
                            sleeves.
                        </div>
                        <div class="rule-indonesian">
                            Berpakaian sopan: bagi Ayah mengenakan celana panjang, dan bagi
                            Bunda mengenakan celana panjang atau rok/gamis sepanjang mata
                            kaki, dengan setidaknya lengan pendek.
                        </div>
                    </li>
                    <li>
                        <div class="rule-english">
                            For everyone's safety, please wear a mask if you are feeling
                            unwell during your visit.
                        </div>
                        <div class="rule-indonesian">
                            Demi kenyamanan dan keamanan bersama, mohon gunakan masker apabila
                            Ayah/Bunda merasa kurang sehat pada saat berkunjung.
                        </div>
                    </li>
                </ul>
            </div>
            <p class="closing" id="closing-text">
                We look forward to welcoming you to our warm and friendly environment,
                parents.
            </p>
            <p class="farewell" id="farewell">
                Wassalamualaikum warahmatullahi wabarakatuh
            </p>
        </div>
        <div class="footer">
            <p class="signature" id="signature">
                Warm regards,<br />
                Mutiara Harapan Islamic School
            </p>
            <p class="contact">
                +62 812 9182 3247 (Monday - Saturday, 8 a.m. - 3 p.m.)
            </p>
            <table role="presentation" style="margin: 0 auto; margin-top: 20px; text-align: center;">
                <tr>
                    <td style="padding: 0 8px;">
                        <a href="https://www.instagram.com/mutiara_harapan_islamic_school/?hl=en" target="_blank">
                            <img src="https://s7768615.sendpul.se/img/constructor/social/round/instagram.png"
                                width="24" height="24" alt="Instagram" />
                        </a>
                    </td>
                    <td style="padding: 0 8px;">
                        <a href="https://www.facebook.com/mutiaraharapanislamicschool/" target="_blank">
                            <img src="https://s7768615.sendpul.se/img/constructor/social/round/facebook.png"
                                width="24" height="24" alt="Facebook" />
                        </a>
                    </td>
                    <td style="padding: 0 8px;">
                        <a href="https://www.youtube.com/channel/UCQF7LfijHASD7dHwHK_88gw" target="_blank">
                            <img src="https://s7768615.sendpul.se/img/constructor/social/round/youtube.png"
                                width="24" height="24" alt="YouTube" />
                        </a>
                    </td>
                </tr>
            </table>
        </div>
    </div>
</body>

</html>
