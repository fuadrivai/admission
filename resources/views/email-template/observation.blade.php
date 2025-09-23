<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>Observation Confirmation - MHIS</title>
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <style>
        body,
        table,
        td,
        a {
            -webkit-text-size-adjust: 100%;
            -ms-text-size-adjust: 100%;
        }

        table,
        td {
            mso-table-lspace: 0pt;
            mso-table-rspace: 0pt;
        }

        img {
            -ms-interpolation-mode: bicubic;
            display: block;
            border: 0;
            outline: none;
            text-decoration: none;
        }

        body {
            margin: 0;
            padding: 0;
            width: 100% !important;
            height: 100% !important;
            background-color: #fafafa;
            font-family: 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            color: #333333;
            font-size: 15px;
            line-height: 1.7;
        }

        /* Container */
        .email-wrap {
            width: 100%;
            background-color: #fafafa;
            padding: 20px 10px;
        }

        .email-body {
            max-width: 640px;
            margin: 0 auto;
            background: #ffffff;
            border-radius: 14px;
            overflow: hidden;
            box-shadow: 0 4px 14px rgba(0, 0, 0, 0.06);
        }

        /* Header */
        .email-header {
            background-color: #ffffff;
            text-align: center;
            padding: 20px 0;
        }

        .brand-logo {
            max-width: 220px;
            height: auto;
        }

        /* Content */
        .email-content {
            padding: 24px 28px;
            color: #444;
            line-height: 1.7;
            font-size: 15px;
        }

        .greeting {
            margin-bottom: 8px;
            color: #222;
            font-weight: 600;
            font-size: 16px;
        }

        .lead {
            margin: 10px 0 16px;
            color: #444;
            font-size: 15px;
        }

        .important {
            color: #9B1134;
            font-weight: 600;
        }

        /* Buttons */
        .cta {
            display: inline-block;
            background: #9B1134;
            color: #fff !important;
            text-decoration: none;
            padding: 10px 18px;
            border-radius: 6px;
            font-weight: 600;
            margin-top: 14px;
            font-size: 14px;
        }

        /* Footer */
        .email-footer {
            padding: 18px 24px;
            background: #fff;
            border-top: 1px solid #f2f2f2;
            font-size: 13px;
            color: #666;
            line-height: 1.5;
        }

        .contact {
            margin-top: 6px;
            font-weight: 500;
            color: #444;
        }

        /* Responsive */
        @media screen and (max-width:520px) {
            .email-body {
                margin: 0 10px;
            }

            .email-content {
                padding: 20px;
            }

            .brand-logo {
                max-width: 200px;
            }
        }
    </style>
</head>

<body>
    <div class="email-wrap">
        <div class="email-body" role="article" aria-roledescription="email">
            <!-- Header with logo -->
            <div class="email-header">
                <img class="brand-logo"
                    src="https://mutiaraharapan.sch.id/wp-content/uploads/2020/01/logo_mh_all_level_220_x_67_cm-01.png"
                    alt="Mutiara Harapan Islamic School">
            </div>

            <!-- Content -->
            <div class="email-content">
                <p style="margin:0 0 12px; color:#333;">Assalamualaikum Warahmatullahi Wabarakatuh,</p>

                <p class="lead" style="margin-top:0;">
                    Dear parents of <strong>{{ $data['child_name'] }}</strong>,
                </p>

                <p style="margin-bottom:14px;">
                    We are pleased to inform you that your child’s observation has been scheduled for the time you have
                    specified, on <strong>{{ $data['day'] }}</strong> at <strong>{{ $data['time'] }}</strong> at
                    Mutiara Harapan Islamic School.
                </p>

                <p style="margin-bottom:14px;">
                    Please arrive <strong>5 minutes before</strong> the chosen time. Our security will help direct
                    parents to the admissions room. If you have any questions or need further assistance, please do not
                    hesitate to contact us: <a href="tel:+6281291823247"
                        style="color:#9B1134; text-decoration:none;">+62 812-9182-3247</a>.
                </p>

                <p style="margin-bottom:18px;">
                    We look forward to welcoming <strong>{{ $data['child_name'] }}</strong> and supporting their growth
                    in our school community.
                </p>

                <p style="margin-bottom:8px;">Wassalamualaikum Warahmatullahi Wabarakatuh,</p>

                <p style="margin-top:18px; margin-bottom:2px; color:#333; font-weight:600;">Warm regards,</p>
                <p style="margin:0 0 8px; color:#444;">Mutiara Harapan Islamic School</p>
                <p class="contact">+62 812 9182 3247 (Monday - Saturday, 8 a.m. - 3 p.m.)</p>

                <!-- Optional CTA -->
                <div style="margin-top:16px;">
                    <a class="cta" href="https://mutiaraharapan.sch.id/" target="_blank" rel="noopener">Visit our
                        website</a>
                </div>
            </div>

            <!-- Footer -->
            <div class="email-footer">
                <div style="font-size:13px; color:#777;">
                    If you did not request this email or believe you have received it in error, please contact us
                    immediately at <a href="mailto:admission@mutiaraharapan.sch.id"
                        style="color:#9B1134;">admission@mutiaraharapan.sch.id</a>.
                </div>
            </div>
        </div>
    </div>
</body>

</html>
