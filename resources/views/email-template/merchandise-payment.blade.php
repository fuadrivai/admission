@php($pageTitle = $data['title'] ?? 'Merchandise Payment Confirmation')
<!doctype html>
<html lang="en" xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="x-apple-disable-message-reformatting" />
    <title>{{ $pageTitle }}</title>
</head>
<body style="margin:0;padding:0;background-color:#f9f7f4;font-family:Arial,'Helvetica Neue',Helvetica,sans-serif;-webkit-text-size-adjust:100%;-ms-text-size-adjust:100%">
    <center style="width:100%;background-color:#f9f7f4">
        <table role="presentation" align="center" border="0" cellpadding="0" cellspacing="0" width="100%" style="max-width:600px">
            <tr>
                <td height="4" style="background:#800000;background-image:linear-gradient(90deg,#800000 0%,#d4af37 50%,#800000 100%)"></td>
            </tr>
        </table>

        <table role="presentation" align="center" border="0" cellpadding="0" cellspacing="0" width="100%" style="width:100%;max-width:600px;margin:0 auto">
            <tr>
                <td align="center" style="padding:40px 24px 25px;background-color:#fff;border-left:1px solid #f0f0f0;border-right:1px solid #f0f0f0">
                    <img src="https://bangka.mutiaraharapan.sch.id/wp-content/uploads/2020/03/LOGO-5-1536x864-1-1024x576.png"
                        alt="Mutiara Harapan Islamic School" width="200" border="0"
                        style="display:block;width:100%;max-width:200px;height:auto;margin:0 auto 20px">
                    <h1 style="margin:0 0 15px;font-size:24px;color:#800000;font-weight:bold;letter-spacing:.5px">{{ $pageTitle }}</h1>
                    <p style="margin:0 0 20px;font-size:20px;color:#800000;font-weight:bold;line-height:1.4">
                        السَّلَامُ عَلَيْكُمْ وَرَحْمَةُ اللَّهِ وَبَرَكَاتُهُ
                    </p>
                </td>
            </tr>
            <tr>
                <td style="padding:0 24px 30px;background-color:#fff;border-left:1px solid #f0f0f0;border-right:1px solid #f0f0f0;color:#444;font-size:15px;line-height:1.7;overflow-wrap:anywhere">
                    <p style="margin:0 0 18px">
                        Dear Parents of <strong style="color:#800000">{{ $data['parent_name'] ?? '' }}</strong>,<br>
                        We have received your merchandise payment. The payment receipt is attached to this email.
                    </p>
                    <p style="margin:0 0 20px">
                        <i>Berikut kami sampaikan konfirmasi pembayaran merchandise Ayah/Bunda. Bukti pembayaran terlampir pada email ini.</i>
                    </p>
                    {!! $data['content'] !!}
                </td>
            </tr>
            <tr>
                <td align="center" style="padding:30px 24px 40px;background-color:#fff;border-left:1px solid #f0f0f0;border-right:1px solid #f0f0f0;border-bottom:1px solid #f0f0f0">
                    <p style="margin:0 0 20px;font-size:20px;color:#800000;font-weight:bold;line-height:1.4">
                        وَالسَّلَامُ عَلَيْكُمْ وَرَحْمَةُ اللَّهِ وَبَرَكَاتُهُ
                    </p>
                    <p style="margin:0;color:#333;font-size:15px;line-height:1.6">
                        With warm regards,<br>
                        <strong style="font-size:18px;color:#800000;display:block;margin:15px 0 5px;letter-spacing:.5px">Mutiara Harapan Islamic School</strong>
                        <em style="color:#d4af37;font-size:14px">Home of The Champions</em>
                    </p>
                </td>
            </tr>
            <tr>
                <td align="center" style="padding:25px 24px;background-color:#f5f1ec;color:#777;font-size:12px;line-height:1.5;border-top:1px solid #e8e4e0">
                    <p style="margin:0 0 10px">&copy; 2026 Mutiara Harapan Islamic School. All Rights Reserved.</p>
                    <p style="margin:0;font-size:11px;color:#999">
                        Jl. Pondok Kacang Raya No.2 Pondok Kacang Timur, Pondok Aren
                        Tangerang Selatan – 15426<br>
                        <a href="https://wa.me/6281291823247" style="color:#800000">+62 812 9182 3247</a>
                        &nbsp;|&nbsp;
                        <a href="mailto:admission@mutiaraharapan.sch.id" style="color:#800000">admission@mutiaraharapan.sch.id</a>
                    </p>
                </td>
            </tr>
        </table>
    </center>
</body>
</html>
