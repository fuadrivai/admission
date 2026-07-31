<!doctype html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="x-apple-disable-message-reformatting" />
    <title>Uniform Payment - Mutiara Harapan Islamic School</title>
  </head>

  <body
    style="
      margin: 0;
      padding: 0;
      background-color: #f9f7f4;
      font-family: &quot;Arial&quot;, Poppins;
      -webkit-text-size-adjust: 100%;
      -ms-text-size-adjust: 100%;
    "
  >
    <center style="width: 100%; background-color: #f9f7f4">
      <table
        role="presentation"
        align="center"
        border="0"
        cellpadding="0"
        cellspacing="0"
        width="100%"
        style="max-width: 600px"
      >
        <tr>
          <td
            height="4"
            style="
              background: linear-gradient(
                90deg,
                #800000 0%,
                #d4af37 50%,
                #800000 100%
              );
            "
          ></td>
        </tr>
      </table>

      <!-- Main Container -->
      <table
        role="presentation"
        align="center"
        border="0"
        cellpadding="0"
        cellspacing="0"
        width="100%"
        style="max-width: 600px; margin: 0 auto"
      >
        <!-- Header with Logo & Title -->
        <tr>
          <td
            align="center"
            style="
              padding: 40px 30px 25px;
              background-color: #ffffff;
              border-left: 1px solid #f0f0f0;
              border-right: 1px solid #f0f0f0;
            "
          >
            <!-- School Logo -->
            <img
              src="https://bangka.mutiaraharapan.sch.id/wp-content/uploads/2020/03/LOGO-5-1536x864-1-1024x576.png"
              alt="Mutiara Harapan Islamic School"
              width="200"
              height="61"
              border="0"
              style="
                display: block;
                height: auto;
                max-width: 200px;
                width: 100%;
                margin: 0 auto 20px;
              "
            />

            <!-- Page Title -->
            <h1
              style="
                margin: 0 0 15px;
                font-size: 24px;
                color: #800000;
                font-weight: bold;
                letter-spacing: 0.5px;
              "
            >
              Uniform Payment
            </h1>

            <p
              style="
                margin: 0 0 20px;
                font-size: 20px;
                color: #800000;
                font-weight: bold;
                line-height: 1.4;
              "
            >
              السَّلَامُ عَلَيْكُمْ وَرَحْمَةُ اللَّهِ وَبَرَكَاتُهُ
            </p>
          </td>
        </tr>

        <!-- Main Content Card -->
        <tr>
          <td
            align="center"
            style="
              padding: 0 30px 35px;
              background-color: #ffffff;
              border-left: 1px solid #f0f0f0;
              border-right: 1px solid #f0f0f0;
            "
          >
            <table
              role="presentation"
              border="0"
              cellpadding="0"
              cellspacing="0"
              width="100%"
              style="background-color: #ffffff"
            >
              <!-- Introduction -->
              <tr>
                <td
                  style="
                    padding: 0 0 25px;
                    text-align: left;
                    color: #444444;
                    font-size: 15px;
                    line-height: 1.7;
                  "
                >
                  <p style="margin: 0 0 18px">
                    Dear Parents of
                    <strong style="color: #800000">{{$data['student_name']}}</strong
                    >,<br />
                    Here we share with you payment details of the uniform that
                    you order in the name of {{$data['student_name']}} from {{$data['level_name']}} {{$data['grade_name']}} Mutiara Harapan Islamic School.
                  </p>
                  <p style="margin: 0">
                    <i
                      >Berikut rincian pembayaran yang Ayah/Bunda pesan atas
                      nama ananda {{$data['student_name']}} dari {{$data['level_name']}} {{$data['grade_name']}} di Mutiara Harapan Islamic School:</i
                    >
                  </p>
                </td>
              </tr>

              <!-- Payment Table -->
              <tr>
                <td style="padding: 0 0 20px">
                  <table
                    role="presentation"
                    border="0"
                    cellpadding="0"
                    cellspacing="0"
                    width="100%"
                    style="
                      border-collapse: separate;
                      border-spacing: 0;
                      border: 1px solid #e8e4e0;
                      border-radius: 8px;
                      overflow: hidden;
                      font-size: 15px;
                    "
                  >
                    <!-- Table Header -->
                    <tr>
                      <th
                        style="
                          border-bottom: 1px solid #e8e4e0;
                          padding: 18px 15px;
                          text-align: left;
                          background-color: #faf9f7;
                          color: #800000;
                          font-weight: bold;
                          font-size: 14px;
                          letter-spacing: 0.5px;
                        "
                      >
                        Item
                      </th>
                      <th
                        style="
                          border-bottom: 1px solid #e8e4e0;
                          padding: 18px 15px;
                          text-align: left;
                          background-color: #faf9f7;
                          color: #800000;
                          font-weight: bold;
                          font-size: 14px;
                          letter-spacing: 0.5px;
                        "
                      >
                        Qty
                      </th>
                      <th
                        style="
                          border-bottom: 1px solid #e8e4e0;
                          padding: 18px 15px;
                          text-align: right;
                          background-color: #faf9f7;
                          color: #800000;
                          font-weight: bold;
                          font-size: 14px;
                          letter-spacing: 0.5px;
                        "
                      >
                        Amount
                      </th>
                    </tr>

                    @foreach ($data['items'] as $item)
                        <tr>
                                <td
                                    style="
                                        border-bottom: 1px solid #f0f0f0;
                                        padding: 16px 15px;
                                        text-align: left;
                                    "
                                >
                                    {{ $item['product_name'] }} {{ $item['size'] ? '(Size: '. $item['size'] . ')' : '' }}
                                </td>
                                <td
                                    style="
                                        border-bottom: 1px solid #f0f0f0;
                                        padding: 16px 15px;
                                        text-align: left;
                                    "
                                >
                                    {{ $item['qty'] }}
                                </td>
                                <td
                                    style="
                                        border-bottom: 1px solid #f0f0f0;
                                        padding: 16px 15px;
                                        text-align: right;
                                    "
                                >
                                    Rp. {{ number_format($item['subtotal'], 0, ',', '.') }}
                                </td>
                            </tr>
                    @endforeach

                    <tr>
                      <td
                        style="
                          border-bottom: 1px solid #f0f0f0;
                          padding: 16px 15px;
                          text-align: left;
                        "
                        colspan="2"
                      >
                        Bank Charges
                      </td>
                      <td
                        style="
                          border-bottom: 1px solid #f0f0f0;
                          padding: 16px 15px;
                          text-align: right;
                        "
                      >
                        Rp. {{ number_format($data['bank_charger'], 0, ',', '.') }}
                      </td>
                    </tr>
                    <!-- Table Footer -->

                    <tr>
                      <td
                        style="
                          padding: 18px 15px;
                          text-align: left;
                          font-weight: bold;
                          color: #800000;
                          background-color: #fcfaf8;
                        "
                        colspan="2"
                      >
                        Total Amount
                      </td>
                      <td
                        style="
                          padding: 18px 15px;
                          text-align: right;
                          font-weight: bold;
                          color: #800000;
                          background-color: #fcfaf8;
                        "
                      >
                        Rp. {{ number_format($data['total_amount'], 0, ',', '.') }}
                      </td>
                    </tr>
                  </table>
                </td>
              </tr>

              <!-- Payment Guide Button -->
              <tr>
                <td align="center" style="padding: 0 0 30px">
                  <a
                    href="{{ $data['order_link'] }}"
                    style="
                      background-color: #800000;
                      border: 2px solid #800000;
                      border-radius: 8px;
                      color: #ffffff;
                      display: inline-block;
                      font-family: &quot;Arial&quot;, Poppins;
                      font-size: 16px;
                      font-weight: bold;
                      line-height: 48px;
                      text-align: center;
                      text-decoration: none;
                      width: 260px;
                      -webkit-text-size-adjust: none;
                      mso-hide: all;
                      box-shadow: 0 4px 12px rgba(128, 0, 0, 0.15);
                    "
                  >
                    <span style="color: #ffffff">View Payment Guide</span>
                  </a>
                  <p
                    style="
                      margin: 12px 0 0;
                      color: #666666;
                      font-size: 13px;
                      font-style: italic;
                    "
                  >
                    Click for detailed payment instructions & banking
                    information
                  </p>
                </td>
              </tr>

              <!-- Payment Guide Section -->
              <tr>
                <td
                  style="
                    padding: 0 0 25px;
                    text-align: left;
                    color: #444444;
                    font-size: 15px;
                    line-height: 1.7;
                  "
                >
                  <!-- Section Title -->
                  <table
                    role="presentation"
                    border="0"
                    cellpadding="0"
                    cellspacing="0"
                    width="100%"
                    style="margin-bottom: 18px"
                  >
                    <tr>
                      <td valign="middle">
                        <h3
                          style="
                            margin: 0;
                            font-size: 17px;
                            color: #800000;
                            font-weight: bold;
                            letter-spacing: 0.3px;
                          "
                        >
                          Important Instructions
                        </h3>
                      </td>
                      <td width="80" align="right" valign="middle">
                        <div style="font-size: 24px; color: #d4af37">•</div>
                      </td>
                    </tr>
                  </table>

                  <!-- Main Instructions -->
                  <p
                    style="
                      margin: 0 0 18px;
                      padding-left: 10px;
                      border-left: 3px solid #f0e6d6;
                    "
                  >
                    <strong
                      >Please make the confirmation for PE size availability to
                      Admissions</strong
                    >
                    and the uniform will be prepared after we receive your
                    payment in 3 days, so you can take the uniform to the
                    Admission room.
                  </p>
                  <p
                    style="
                      margin: 0 0 18px;
                      padding-left: 10px;
                      border-left: 3px solid #f0e6d6;
                    "
                  >
                    <i>
                      <strong
                        >Tolong lakukan konfirmasi terkait ketersediaan size
                        seragam olahraga terlebih dahulu</strong
                      >
                      dan seragam akan disiapkan setelah kami menerima
                      pembayaran dalam 3 hari. Pengambilan seragam dapat diambil
                      di ruang Admission.</i
                    >
                  </p>
                </td>
              </tr>

              <!-- Contact Section -->
              <tr>
                <td style="padding: 25px 0 10px; text-align: left">
                  <table
                    role="presentation"
                    border="0"
                    cellpadding="0"
                    cellspacing="0"
                    width="100%"
                    style="
                      background: linear-gradient(to right, #fdf8f3, #faf9f7);
                      border-radius: 8px;
                      padding: 20px;
                    "
                  >
                    <tr>
                      <td
                        style="
                          color: #444444;
                          font-size: 15px;
                          line-height: 1.7;
                        "
                      >
                        <p style="margin: 0 0 12px">
                          Should you have any questions or require assistance
                          with the payment process, our admissions team is ready
                          to help:
                        </p>
                        <p style="margin: 0">
                          <strong style="color: #800000">Contact:</strong>
                          <a href="https://wa.me/6281291823247" target="_blank">
                            +62 812 9182 3247 </a
                          ><br />
                          <strong style="color: #800000">Office Hours:</strong>
                          8:00 a.m. – 3:00 p.m.
                        </p>
                      </td>
                    </tr>
                  </table>
                </td>
              </tr>
            </table>
          </td>
        </tr>

        <!-- Closing Section -->
        <tr>
          <td
            align="center"
            style="
              padding: 30px 30px 40px;
              background-color: #ffffff;
              border-left: 1px solid #f0f0f0;
              border-right: 1px solid #f0f0f0;
              border-bottom: 1px solid #f0f0f0;
            "
          >
            <!-- Arabic Closing -->
            <p
              style="
                margin: 0 0 20px;
                font-size: 20px;
                color: #800000;
                font-weight: bold;
                line-height: 1.4;
              "
            >
              وَالسَّلَامُ عَلَيْكُمْ وَرَحْمَةُ اللَّهِ وَبَرَكَاتُهُ
            </p>

            <!-- School Signature -->
            <p
              style="
                margin: 0;
                color: #333333;
                font-size: 15px;
                line-height: 1.6;
              "
            >
              With warm regards,<br />
              <strong
                style="
                  font-size: 18px;
                  color: #800000;
                  display: block;
                  margin: 15px 0 5px;
                  letter-spacing: 0.5px;
                "
                >Mutiara Harapan Islamic School</strong
              >
              <em style="color: #d4af37; font-size: 14px"
                >Home of The Champions</em
              >
            </p>
          </td>
        </tr>

        <!-- Footer -->
        <tr>
          <td
            align="center"
            style="
              padding: 25px 30px;
              background-color: #f5f1ec;
              color: #777777;
              font-size: 12px;
              line-height: 1.5;
              border-top: 1px solid #e8e4e0;
            "
          >
            <p style="margin: 0 0 10px">
              &copy; 2026 Mutiara Harapan Islamic School. All Rights
              Reserved.
            </p>
            <p style="margin: 0; font-size: 11px; color: #999999">
                        Jl. Pondok Kacang Raya No.2 Pondok Kacang Timur, Pondok Aren
                        Tangerang Selatan – 15426 <br /><a href="https://wa.me/6281291823247" target="_blank">
                            +62 812 9182 3247
                        </a>
                        |
                        <a href="mailto:admission@mutiaraharapan.sch.id">admission@mutiaraharapan.sch.id</a>
                    </p>
          </td>
        </tr>
      </table>
    </center>
  </body>
</html>
