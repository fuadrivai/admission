<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Form Persetujuan Orang Tua - Mutiara Harapan Islamic School</title>

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.3/font/bootstrap-icons.css">
    <link rel="stylesheet" href="/assets/extensions/toastify-js/src/toastify.css">
    <link rel="stylesheet" href="/assets/static/css/student-approval.css?v=1.0.0">
</head>

<body>
    <!-- Header Section -->
    <header class="header-section">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-auto">
                    <img src="/assets/images/logo mh menyamping putih-01-01.png" alt="MHIS Logo" class="school-logo"
                        onerror="this.style.display='none';" />
                </div>
                <div class="col">
                    <div class="header-title">
                        <h1 class="h4 mb-1">Parent’s Statement Form</h1>
                        <p class="mb-0">Formulir persetujuan orang tua</p>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <!-- Main Wizard Container -->
    <div class="container main-container">
        <!-- Step Indicator -->
        <div class="step-indicator">
            <div class="step active" data-step="1">
                <div class="step-number">1</div>
                <div class="step-title">Confirmation</div>
            </div>
            <div class="step" data-step="2">
                <div class="step-number">2</div>
                <div class="step-title">Payment</div>
            </div>
            <div class="step" data-step="3">
                <div class="step-number">3</div>
                <div class="step-title">Parent</div>
            </div>
            <div class="step" data-step="4">
                <div class="step-number">4</div>
                <div class="step-title">Guardian</div>
            </div>
            <div class="step" data-step="5">
                <div class="step-number">5</div>
                <div class="step-title">Drug</div>
            </div>
            <div class="step" data-step="6">
                <div class="step-number">6</div>
                <div class="step-title">Student</div>
            </div>
        </div>

        <!-- Step 1: Syarat Pendaftaran -->
        <div class="step-content active" id="step-1">
            {{-- <h2 class="section-title">SYARAT PENDAFTARAN</h2>

            <p>Mengisi dokumen persyaratan pendaftaran sebagai berikut :</p>

            <ol class="mb-4">
                <li>Surat/form persetujuan pembayaran biaya sekolah</li>
                <li>Surat/form pernyataan orang tua/wali murid</li>
            </ol>

            <div class="info-box">
                <p class="mb-2">Untuk rincian berkas di bawah ini dapat diisi melalui link yang berbeda
                    <strong>mhis.link/PengumpulanBerkasMHIS</strong>
                </p>
                <ol class="mb-0">
                    <li>Scan KTP Ayah dan Bunda -> masing – masing 1 lembar</li>
                    <li>Scan Akte Kelahiran siswa -> 1 lembar</li>
                    <li>Scan Kartu keluarga -> 1 lembar</li>
                    <li>Foto 6x4 (kemeja putih berkerah) background merah (dikumpulkan hard file ke sekolah, dapat
                        dikumpulkan sebelum interview dengan Principal)</li>
                </ol>
            </div>

            <div class="info-box">
                <h5 class="mb-2">Dokumen khusus tambahan untuk siswa pindahan:</h5>
                <ol class="mb-0">
                    <li>Surat keterangan pindah dari sekolah asal dan NISN (Nomor Induk Siswa Nasional) yang sudah
                        divalidasi oleh Dinas Pendidikan setempat (harus dikumpulkan sebelum interview dengan Principal)
                    </li>
                    <li>Scan raport cover dan kelas terakhir -> 1 rangkap (harus dikumpulkan sebelum interview dengan
                        Principal)</li>
                </ol>
            </div> --}}

            <h3 class="subsection-title">Authorized Signatory Confirmation</h3>

            <div class="mb-4">
                <input type="hidden" id="admission-code" value="{{ $code }}">
                <label for="parentSelector" class="form-label required">Relationship to Student (select one):</label>
                <select class="form-select" id="parentSelector" required>
                    <option value="" selected disabled>Select an option</option>
                    <option value="father">Father</option>
                    <option value="mother">Mother</option>
                    <option value="guardian">Guardian</option>
                </select>
                <div class="error-message" id="parentSelector-error">Please select a relationship</div>
            </div>

            <!-- Parent Information Display -->
            <div class="parent-info-card" id="parentInfoCard" style="display: none;">
                <h5 class="mb-3">Parent Information : </h5>
                <div class="parent-info-row">
                    <div class="parent-label">Full name:</div>
                    <div class="parent-value parentFullName" id="parentFullName">-</div>
                </div>
                <div class="parent-info-row">
                    <div class="parent-label">Email:</div>
                    <div class="parent-value parentEmail" id="parentEmail">-</div>
                </div>
                <div class="parent-info-row">
                    <div class="parent-label">Phone number:</div>
                    <div class="parent-value parentPhone" id="parentPhone">-</div>
                </div>
                <div class="parent-info-row">
                    <div class="parent-label">Birth Place:</div>
                    <div class="parent-value parentBirthPlace" id="parentBirthPlace">-</div>
                </div>
                <div class="parent-info-row">
                    <div class="parent-label">Birth Date:</div>
                    <div class="parent-value parentBirthDate" id="parentBirthDate">-</div>
                </div>
                <div class="parent-info-row">
                    <div class="parent-label">Id Card:</div>
                    <div class="parent-value parentIdCard" id="parentIdCard">-</div>
                </div>
            </div>

            <div class="statement-item">
                <p>I hereby declare that I am the lawful parent/guardian with full authority over the prospective
                    student of Mutiara Harapan Islamic School as stated below.
                    <br><i><small>Menyatakan bahwa benar Saya adalah merupakan orang tua/wali yang sepenuhnya berwenang
                            dari calon siswa Mutiara Harapan Islamic School tersebut di bawah.</small></i>
                </p>
            </div>

            <!-- Student Information Display -->
            <div class="student-info-card">
                <h5 class="mb-3">Student information</h5>
                <div class="student-info-row">
                    <div class="student-label">Full name:</div>
                    <div class="student-value studentFullName" id="studentFullName">--</div>
                </div>
                <div class="student-info-row">
                    <div class="student-label">Age:</div>
                    <div class="student-value studentAge" id="studentAge">--</div>
                </div>
                <div class="student-info-row">
                    <div class="student-label">Level:</div>
                    <div class="student-value studentLevel" id="studentLevel">-</div>
                </div>
                <div class="student-info-row">
                    <div class="student-label">Grade:</div>
                    <div class="student-value studentGrade" id="studentGrade">--</div>
                </div>
                <div class="student-info-row">
                    <div class="student-label">Academic Year:</div>
                    <div class="student-value academicYear" id="academicYear">--</div>
                </div>
            </div>
        </div>

        <!-- Step 2: Form Persetujuan Pembayaran -->
        <div class="step-content" id="step-2">
            <h2 class="section-title">School Fee Payment Consent Form</h2>

            <div class="statement-item">
                <p>I hereby state that I fully agree to all applicable payment terms and procedures and undertake to
                    fulfil all payment obligations in accordance with the regulations of Mutiara Harapan Islamic School.
                    <br> <i><small>Dengan ini saya menyatakan bahwa saya menyetujui sepenuhnya seluruh ketentuan dan
                            tata cara pembayaran yang berlaku serta bersedia memenuhi dan melaksanakan seluruh kewajiban
                            pembayaran sesuai dengan ketentuan Mutiara Harapan Islamic School.</small></i>
                </p>

                <div class="form-check mb-3">
                    <input class="form-check-input" type="checkbox" id="agreePayment1" required>
                    <label class="form-check-label required" for="agreePayment1">Yes, i Agree</label>
                    <div class="error-message" id="agreePayment1-error">Please agree to this statement.</div>
                </div>
            </div>

            <div class="row mb-4">
                <div class="col-md-6 mb-3">
                    <label for="developmentFee" class="form-label required">Development Fee</label>
                    <div class="money-input-group">
                        <span class="input-group-text">Rp</span>
                        <input type="text" class="form-control number2" id="developmentFee"
                            placeholder="12,500,000" required>
                    </div>
                    <div class="error-message" id="developmentFee-error">Please enter development fee</div>
                    <div class="terbilang-display" id="developmentFeeTerbilang">-</div>
                </div>

                <div class="col-md-6 mb-3">
                    <label for="annualFee" class="form-label required">Annual Fee</label>
                    <div class="money-input-group">
                        <span class="input-group-text">Rp</span>
                        <input type="text" class="form-control number2" id="annualFee" placeholder="4,000,000"
                            required>
                    </div>
                    <div class="error-message" id="annualFee-error">Please enter annual fee</div>
                    <div class="terbilang-display" id="annualFeeTerbilang">-</div>
                </div>

                <div class="col-md-6 mb-3">
                    <label for="schoolFee" class="form-label required">School Fee</label>
                    <div class="money-input-group">
                        <span class="input-group-text">Rp</span>
                        <input type="text" class="form-control number2" id="schoolFee" placeholder="1,750,000"
                            required>
                    </div>
                    <div class="error-message" id="schoolFee-error">Please enter school fee</div>
                    <div class="terbilang-display" id="schoolFeeTerbilang">-</div>
                </div>
            </div>

            <div class="checkbox-declaration">
                <div class="statement-item">
                    <p>Development fee akan Saya bayar sesuai dengan ketentuan yang berlaku pada saat pendaftaran siswa
                        baru Mutiara Harapan Islamic School.</p>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="agreePayment2" required>
                        <label class="form-check-label required" for="agreePayment2">Iya, Saya Sepakat</label>
                        <div class="error-message" id="agreePayment2-error">Harap setujui pernyataan ini</div>
                    </div>
                </div>

                <div class="statement-item">
                    <p>Annual fee dan school fee akan Saya bayar pada saat putra/putri Saya dinyatakan diterima di
                        Mutiara Harapan Islamic School.</p>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="agreePayment3" required>
                        <label class="form-check-label required" for="agreePayment3">Iya, Saya Sepakat</label>
                        <div class="error-message" id="agreePayment3-error">Harap setujui pernyataan ini</div>
                    </div>
                </div>

                <div class="statement-item">
                    <p>Uang Ujian International (Cambridge International Examination: Checkpoint/IGCSE/A Level) yang
                        bersifat wajib dan besarnya ditetapkan pada saat ujian akan dilaksanakan.</p>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="agreePayment4" required>
                        <label class="form-check-label required" for="agreePayment4">Iya, Saya Sepakat</label>
                        <div class="error-message" id="agreePayment4-error">Harap setujui pernyataan ini</div>
                    </div>
                </div>

                <div class="statement-item">
                    <p>Uang sumber pembelajaran, baik dalam bentuk buku cetak atau sumber digital, yang besarnya
                        ditetapkan berdasarkan info dari penerbit.</p>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="agreePayment5" required>
                        <label class="form-check-label required" for="agreePayment5">Iya, Saya Sepakat</label>
                        <div class="error-message" id="agreePayment5-error">Harap setujui pernyataan ini</div>
                    </div>
                </div>

                <div class="statement-item">
                    <p>Uang ekstrakurikuler sesuai dengan ekstrakurikuler yang dipilih putra/putri Saya.</p>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="agreePayment6" required>
                        <label class="form-check-label required" for="agreePayment6">Iya, Saya Sepakat</label>
                        <div class="error-message" id="agreePayment6-error">Harap setujui pernyataan ini</div>
                    </div>
                </div>

                <div class="statement-item">
                    <p>Uang kegiatan lainnya yang besarnya ditetapkan pada saat kegiatan akan dilaksanakan.</p>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="agreePayment7" required>
                        <label class="form-check-label required" for="agreePayment7">Iya, Saya Sepakat</label>
                        <div class="error-message" id="agreePayment7-error">Harap setujui pernyataan ini</div>
                    </div>
                </div>

                <div class="statement-item">
                    <p>School fee Saya bayarkan per bulan, pembayaran paling lambat tanggal 10 bulan berjalan.</p>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="agreePayment8" required>
                        <label class="form-check-label required" for="agreePayment8">Iya, Saya Sepakat</label>
                        <div class="error-message" id="agreePayment8-error">Harap setujui pernyataan ini</div>
                    </div>
                </div>

                <div class="statement-item">
                    <p>Uang Ittihada yang besarnya ditetapkan kemudian oleh Ittihada dan dibayarkan per tahun.</p>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="agreePayment9" required>
                        <label class="form-check-label required" for="agreePayment9">Iya, Saya Sepakat</label>
                        <div class="error-message" id="agreePayment9-error">Harap setujui pernyataan ini</div>
                    </div>
                </div>

                <div class="statement-item">
                    <p>Saya menyatakan kesanggupan saya untuk melunasi seluruh kewajiban pembayaran tersebut di atas
                        sebelum kegiatan belajar di Mutiara Harapan Islamic School dimulai dan saya bersedia menerima
                        segala konsekuensinya apabila tidak memenuhi kewajiban tersebut, termasuk namun tidak terbatas
                        pada hilangnya hak anak saya untuk mengikuti kegiatan belajar di Mutiara Harapan Islamic School
                        sebelum kewajiban tersebut kami lunasi seluruhnya.</p>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="agreePayment10" required>
                        <label class="form-check-label required" for="agreePayment10">Iya, Saya Sepakat</label>
                        <div class="error-message" id="agreePayment10-error">Harap setujui pernyataan ini</div>
                    </div>
                </div>

                <div class="statement-item">
                    <p>Selanjutnya saya juga menyatakan Setuju/Sepakat bahwa :</p>
                    <ol type="A" class="mb-3">
                        <li>Annual fee di setiap jenjang kenaikannya 2 tahun sekali maksimal 20% dari besaran pada tahun
                            tersebut.</li>
                        <li>School fee besarnya ditetapkan melalui kebijakan sekolah dengan ketentuan kenaikannya 3
                            tahun sekali maksimal 20% dari besaran pada tahun tersebut.</li>
                        <li>Development fee, annual fee dan school fee yang sudah Saya bayar tidak akan Saya tarik
                            kembali atau Saya alihkan dengan alasan apapun, kecuali putra/putri Saya dinyatakan tidak
                            diterima di Mutiara Harapan Islamic School.</li>
                        <li>Saya tidak dapat mengambil hasil evaluasi belajar putra/putri Saya sebelum Saya memenuhi
                            seluruh kewajiban Administrasi yang ditetapkan MHIS.</li>
                        <li>Saya menyatakan kesanggupan saya untuk melunasi seluruh kewajiban pembayaran tersebut di
                            atas dan menyerahkan seluruh kelengkapan dokumen yang diperlukan sebelum kegiatan belajar di
                            Mutiara Harapan Islamic School dimulai dan saya bersedia menerima segala konsekuensinya
                            apabila tidak memenuhi kewajiban tersebut, termasuk namun tidak terbatas pada hilangnya hak
                            anak saya untuk mengikuti kegiatan belajar di Mutiara Harapan Islamic School sebelum
                            kewajiban tersebut kami lunasi seluruhnya.</li>
                    </ol>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="agreePayment11" required>
                        <label class="form-check-label required" for="agreePayment11">Iya, Saya Sepakat</label>
                        <div class="error-message" id="agreePayment11-error">Harap setujui pernyataan ini</div>
                    </div>
                </div>

                <div class="statement-item">
                    <p>Saya juga menyatakan bahwa Saya memahami sepenuhnya seluruh isi formulir ini dan Saya
                        menandatanganinya dalam keadaan sehat jasmani dan rohani, tanpa tekanan atau paksaan pihak
                        manapun untuk dipergunakan sebagaimana mestinya.</p>

                    <div class="current-date-display" id="currentDate1"></div>

                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="agreePayment12" required>
                        <label class="form-check-label required" for="agreePayment12">Iya, Saya Sepakat</label>
                        <div class="error-message" id="agreePayment12-error">Harap setujui pernyataan ini</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Step 3: Form Pernyataan Orang Tua -->
        <div class="step-content" id="step-3">
            <h2 class="section-title">FORM PERNYATAAN ORANG TUA / WALI MURID</h2>

            <div class="alert alert-info mb-4">
                <i class="bi bi-info-circle"></i> Mohon dibaca secara seksama sebelum diisi.
            </div>

            <div class="checkbox-declaration">
                <div class="statement-item">
                    <p>Dengan ini Saya menyatakan bahwa saya setuju:</p>
                    <p>Bersedia menaati dan mendukung seluruh Peraturan, Ketentuan dan Tata Tertib yang ditentukan oleh
                        Mutiara Harapan Islamic School, baik yang sudah berjalan, ataupun yang akan ditentukan kemudian.
                    </p>
                </div>

                <div class="statement-item">
                    <p>Bersedia bekerja sama dengan Principal, Teachers, Administration Staff, Manajemen serta seluruh
                        perangkat sekolah dengan baik.</p>
                </div>

                <div class="statement-item">
                    <p>Bersedia mendukung seluruh kegiatan anak Saya baik di sekolah maupun di luar sekolah dan akan
                        memberikan pendidikan serta kegiatan yang searah dan sejalan dengan program sekolah terhadap
                        anak Saya di rumah.</p>
                </div>

                <div class="statement-item">
                    <p>Bersedia berkomunikasi dan berkoordinasi dengan pihak sekolah, termasuk datang jika
                        diminta/diundang oleh pihak sekolah.</p>
                </div>

                <div class="statement-item">
                    <p>Bersedia mendahulukan dan mengutamakan kepentingan sekolah di atas keperluan lainnya.</p>
                </div>

                <div class="statement-item">
                    <p>Bersedia apabila ditunjuk oleh pihak sekolah untuk menjadi pengurus Persatuan Orang Tua Murid dan
                        Guru (Ittihada) di lingkungan Mutiara Harapan Islamic School.</p>
                </div>

                <div class="statement-item">
                    <p>Saya paham bahwa selain kurikulum nasional sekolah juga menggunakan kurikulum internasional
                        sehingga penilaian dan kriteria promosi siswa didasarkan pada kedua kurikulum tersebut sehingga
                        Saya akan mematuhi sepenuhnya.</p>
                </div>

                <div class="statement-item">
                    <p>Dengan mengajukan form ini, Saya sepenuhnya akan mengikuti serta mendukung sekolah dalam berbagai
                        program dan kegiatan, untuk pencapaian tujuan sekolah.</p>
                </div>

                <div class="statement-item">
                    <p>Saya setuju untuk mematuhi dan mendukung seluruh ketentuan ini dengan menjaga dan tidak akan
                        melakukan tindakan dan reputasi dan nama baik Sekolah. Saya juga berkomitmen untuk menjaga
                        kerahasiaan seluruh informasi, data, serta dokumen yang berkaitan dengan sekolah kepada pihak
                        manapun tanpa persetujuan tertulis dari sekolah. Saya dapat menerima segala konsekuensinya,
                        termasuk namun tidak terbatas, pada anak saya dikeluarkan secara sepihak dari sekolah apabila
                        Saya atau anak Saya gagal memenuhi ketentuan ini.</p>
                </div>

                <div class="statement-item">
                    <p>Bahwa apabila berdasarkan observasi dan assessment baik sebelum (trial class) maupun setelah
                        proses belajar mengajar berjalan, ternyata anak saya memerlukan penanganan khusus, Saya
                        menyetujui anak saya dipindahkan ke Development Class.</p>
                </div>

                <div class="statement-item">
                    <p>Sehubungan dengan butir 10 apabila anak saya dipindahkan ke Development Class, Saya bersedia
                        mengikuti semua ketentuan yang berlaku di Development Class.</p>
                </div>

                <div class="statement-item">
                    <p>Saya memberi izin kepada Sekolah untuk mengadakan tes psikologis pada anak saya kapanpun
                        diperlukan oleh sekolah. Pemberitahuan pra-tindakan akan diberikan kepada saya untuk persetujuan
                        lebih lanjut.</p>
                </div>

                <div class="statement-item">
                    <p>Saya mengetahui dan menyetujui sepenuhnya bahwa sekolah melakukan segala hal yang dirasa perlu
                        dengan sebaik-baiknya sesuai tujuan pendidikan dengan mengindahkan prosedur kesehatan dan
                        keamanan dengan mengedepankan kebaikan anak Saya. Oleh karenanya Saya membebaskan sekolah dari
                        segala tuntutan serta membebaskan Mutiara Harapan Islamic School dari segala macam bentuk
                        tanggung jawab termasuk namun tidak terbatas pada gugatan perdata maupun tuntutan pidana.</p>
                </div>

                <div class="statement-item">
                    <p>Apabila Saya memerlukan antar jemput sekolah, Saya sepenuhnya setuju pada keputusan sekolah yang
                        ditetapkan berdasarkan survey rute dan atau kapasitas kendaraan antar jemput sekolah.</p>
                </div>

                <div class="statement-item">
                    <p>Saya memberi izin kepada Sekolah untuk memproduksi foto dan video anak Saya dalam media apapun.
                        Saya mengerti bahwa gambar – gambar tersebut akan digunakan untuk kepentingan internal dan
                        eksternal serta kepentingan promosi/publikasi sekolah.</p>
                </div>

                <div class="statement-item">
                    <p>Atas nama anak Saya, Saya memberi izin kepada pihak Sekolah untuk menggunakan hasil karya anak
                        Saya dalam media apapun (termasuk hasil karya tulis, audio dan materi visual)</p>
                </div>

                <div class="statement-item">
                    <p>Saya juga menyatakan bahwa Saya memahami sepenuhnya seluruh isi formulir ini dan Saya
                        menandatanganinya dalam keadaan sehat jasmani dan rohani, tanpa tekanan atau paksaan pihak
                        manapun untuk dipergunakan sebagaimana mestinya di Mutiara Harapan Islamic School.</p>

                    <div class="current-date-display" id="currentDate2"></div>

                    <div class="form-check">
                        <input class="form-check-input" type="hidden" id="parentAgreeStatementId">
                        <input class="form-check-input" type="checkbox" id="parentAgreeStatement" required>
                        <label class="form-check-label required" for="parentAgreeStatement">Iya, Saya Sepakat</label>
                        <div class="error-message" id="parentAgreeStatement-error">Harap setujui pernyataan ini</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Step 4: Form Pernyataan Wali Murid -->
        <div class="step-content" id="step-4">
            <h2 class="section-title">FORM PERNYATAAN ORANG TUA / WALI MURID</h2>

            <div class="checkbox-declaration">
                <div class="statement-item">
                    <p>Dengan ini menyatakan bersedia mengikuti sosialisasi orang tua peserta didik baru sesuai dengan
                        waktu yang akan ditetapkan kemudian.</p>
                </div>

                <div class="statement-item">
                    <p>Demikian form pernyataan ini Saya sepakati secara sadar tanpa ada tekanan dari pihak manapun.</p>

                    <div class="current-date-display" id="currentDate3"></div>

                    <div class="form-check">
                        <input class="form-check-input" type="hidden" id="guardianAgreeStatementId">
                        <input class="form-check-input" type="checkbox" id="guardianAgreeStatement" required>
                        <label class="form-check-label required" for="guardianAgreeStatement">Iya, Saya
                            Sepakat</label>
                        <div class="error-message" id="guardianAgreeStatement-error">Harap setujui pernyataan ini
                        </div>
                    </div>
                </div>
            </div>

            <!-- Final Submit Section  if leve is under Upper Secondary-->
            <div class="final-submit-section d-none" id="btn-under-upper-secondary">
                <div class="alert alert-success">
                    <h5 class="alert-heading"><i class="bi bi-check-circle-fill"></i> Penyelesaian Formulir</h5>
                    <p class="mb-3">Selamat! Anda telah menyelesaikan semua bagian formulir persetujuan orang
                        tua.
                        Silakan tinjau informasi Anda sebelum mengirimkan.</p>
                    <button type="button" class="btn btn-success btn-lg final-submit-btn" id="final-submit-btn-1">
                        <i class="bi bi-send-check"></i> Kirim Formulir Persetujuan
                    </button>
                </div>
            </div>
        </div>

        <!-- Step 5: Surat Pernyataan Tes Narkotika -->
        <div class="step-content conditional-section" id="step-5">
            <h2 class="section-title">SURAT PERNYATAAN ORANG TUA KESEDIAAN MENJALANI TES NARKOTIKA DAN OBAT TERLARANG
            </h2>

            <div class="parent-info-card">
                <h5 class="mb-3">Saya yang bertanda tangan dibawah ini sebagai :</h5>
                <div class="parent-info-row">
                    <div class="parent-label">Nama Lengkap:</div>
                    <div class="parent-value parentFullName" id="parentFullName2">-</div>
                </div>
                <div class="parent-info-row">
                    <div class="parent-label">Email:</div>
                    <div class="parent-value parentEmail" id="parentEmail2">-</div>
                </div>
                <div class="parent-info-row">
                    <div class="parent-label">Nomor HP:</div>
                    <div class="parent-value parentPhone" id="parentPhone2">-</div>
                </div>
                <div class="parent-info-row">
                    <div class="parent-label">Tempat Lahir:</div>
                    <div class="parent-value parentBirthPlace" id="parentBirthPlace2">-</div>
                </div>
                <div class="parent-info-row">
                    <div class="parent-label">Tanggal Lahir:</div>
                    <div class="parent-value parentBirthDate" id="parentBirthDate2">-</div>
                </div>
                <div class="parent-info-row">
                    <div class="parent-label">No. KTP:</div>
                    <div class="parent-value parentIdCard" id="parentIdCard2">-</div>
                </div>
            </div>

            <div class="statement-item">
                <p>Menyatakan bahwa benar Saya adalah merupakan orang tua/wali yang sepenuhnya berwenang dari calon
                    siswa Mutiara Harapan Islamic School tersebut di bawah.</p>
            </div>

            <div class="student-info-card">
                <h5 class="mb-3">Informasi Calon Siswa</h5>
                <div class="student-info-row">
                    <div class="student-label">Nama Lengkap:</div>
                    <div class="student-value studentFullName" id="studentFullName2">-</div>
                </div>
                <div class="student-info-row">
                    <div class="student-label">Usia:</div>
                    <div class="student-value studentAge" id="studentAge2">-</div>
                </div>
                <div class="student-info-row">
                    <div class="student-label">Level:</div>
                    <div class="student-value studentLevel" id="studentLevel2">-</div>
                </div>
                <div class="student-info-row">
                    <div class="student-label">Kelas:</div>
                    <div class="student-value studentGrade" id="studentGrade2">-</div>
                </div>
                <div class="student-info-row">
                    <div class="student-label">Tahun Ajaran:</div>
                    <div class="student-value academicYear" id="academicYear2">-</div>
                </div>
            </div>

            <div class="checkbox-declaration">
                <div class="statement-item">
                    <p>Dengan ini menyatakan memberi izin kepada sekolah atau pihak yang ditunjuk untuk mengadakan
                        tes/pemeriksaan atas penyalahgunaan narkotika, obat – obatan terlarang dan zat adiktif lainnya.
                    </p>
                    <p>Kami mengerti sepenuhnya atas dampak negatif zat – zat tersebut, dan bersedia menerima
                        konsekuensi atas hasil pemeriksaan sesuai dengan tata tertib yang berlaku.</p>
                    <div class="current-date-display" id="currentDate4"></div>
                    <div class="form-check">
                        <input class="form-check-input" type="hidden" id="narcoticaAgreeStatementId">
                        <input class="form-check-input" type="checkbox" id="narcoticaAgreeStatement" required>
                        <label class="form-check-label required" for="narcoticaAgreeStatement">Iya, Saya
                            Sepakat</label>
                        <div class="error-message" id="narcoticaAgreeStatement-error">Harap setujui pernyataan ini
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Step 6: Surat Pernyataan Siswa -->
        <div class="step-content conditional-section" id="step-6">
            <h2 class="section-title">SURAT PERNYATAAN SISWA</h2>

            <div class="student-info-card">
                <h5 class="mb-3">Informasi Siswa</h5>
                <div class="student-info-row">
                    <div class="student-label">Nama Lengkap:</div>
                    <div class="student-value studentFullName" id="studentFullName3">Muhammad Al-Fatih Sudirman</div>
                </div>
                <div class="student-info-row">
                    <div class="student-label">Usia:</div>
                    <div class="student-value studentAge" id="studentAge3">7 tahun 2 bulan</div>
                </div>
                <div class="student-info-row">
                    <div class="student-label">Level:</div>
                    <div class="student-value studentLevel" id="studentLevel3">SD</div>
                </div>
                <div class="student-info-row">
                    <div class="student-label">Kelas:</div>
                    <div class="student-value studentGrade" id="studentGrade3">Kelas 1</div>
                </div>
                <div class="student-info-row">
                    <div class="student-label">Tahun Ajaran:</div>
                    <div class="student-value academicYear" id="academicYear3">2024/2025</div>
                </div>

                <div class="checkbox-declaration">
                    <div class="statement-item">
                        <p>Dengan ini menyatakan taat dan patuh pada peraturan tata tertib murid dan ketentuan lainnya
                            yang
                            terkait dengan siswa Mutiara Harapan Islamic School yang berlaku di Mutiara Harapan Islamic
                            School.</p>
                        <p>Jika dikemudian hari saya melanggar peraturan tersebut, maka saya bersedia menerima
                            konsekuensinya.</p>

                        <div class="current-date-display" id="currentDate5"></div>

                        <div class="form-check">
                            <input class="form-check-input" type="hidden" id="studentAgreeStatementId">
                            <input class="form-check-input" type="checkbox" id="studentAgreeStatement" required>
                            <label class="form-check-label required" for="studentAgreeStatement">Iya, Saya
                                Sepakat</label>
                            <div class="error-message" id="studentAgreeStatement-error">Harap setujui pernyataan ini
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Final Submit Section -->
                <div class="final-submit-section">
                    <div class="alert alert-success">
                        <h5 class="alert-heading"><i class="bi bi-check-circle-fill"></i> Penyelesaian Formulir</h5>
                        <p class="mb-3">Selamat! Anda telah menyelesaikan semua bagian formulir persetujuan orang
                            tua.
                            Silakan tinjau informasi Anda sebelum mengirimkan.</p>
                        <button type="button" class="btn btn-success btn-lg final-submit-btn"
                            id="final-submit-btn-2">
                            <i class="bi bi-send-check"></i> Kirim Formulir Persetujuan
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <!-- Wizard Navigation -->
        <div class="wizard-navigation">
            <div>
                <button type="button" class="btn btn-outline-primary-custom" id="prev-btn" disabled>
                    <i class="bi bi-chevron-left"></i> Sebelumnya
                </button>
                <button type="button" class="btn btn-primary-custom" id="next-btn">
                    Selanjutnya <i class="bi bi-chevron-right"></i>
                </button>
            </div>
        </div>
    </div>

    <!-- jQuery -->
    <script src="/assets/compiled/js/app.js"></script>
    <script src="/assets/extensions/jquery/jquery.min.js"></script>
    <script src="/assets/extensions/moment/moment.js"></script>
    <script src="/assets/extensions/select2/dist/js/select2.full.min.js"></script>
    <script src="/assets/extensions/bootstrap-datepicker/js/bootstrap-datepicker.js"></script>
    <script src="/assets/extensions/bootstrap-datepicker/js/jquery.timepicker.min.js"></script>
    <script src="/assets/extensions/jquery-blockUI/jquery.blockUI.js"></script>
    <script src="/assets/extensions/toastify-js/src/toastify.js"></script>
    <script src="/assets/compiled/js/script.js?v=1.1.4"></script>
    <script src="/assets/static/js/pages/student-approval.js?v=1.0.1"></script>
</body>

</html>
