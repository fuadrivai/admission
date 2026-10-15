<?php

namespace Database\Seeders;

use App\Models\AdmissionStatementDocument;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MhisParentStatementSeeder extends Seeder
{
    private const VERSION = '2026.10.15';

    public function run()
    {
        $documentName = 'MHIS Parent Statement';
        $baseVersion = self::VERSION;

        if (AdmissionStatementDocument::where('type', 'parent')
            ->where('name', $documentName)
            ->where('version', $baseVersion)
            ->exists()) {
            $this->command->info('The MHIS Parent Statement draft already exists.');

            return;
        }

        $version = $baseVersion;
        $suffix = 2;
        while (AdmissionStatementDocument::where('type', 'parent')->where('version', $version)->exists()) {
            $version = $baseVersion . '-' . $suffix++;
        }

        $sections = [
            [
                'title_en' => 'Cooperation with the School',
                'title_id' => 'Kerja Sama dengan Sekolah',
                'items' => [
                    [
                        'We are willing to cooperate well with the Principal, Teachers, Administration Staff, and Management in supporting our child\'s education, development, health, and safety.',
                        'Kami bersedia bekerja sama dengan Kepala Sekolah, Guru, Staf Administrasi, dan Manajemen dengan baik dalam mendukung pendidikan, perkembangan, kesehatan, dan keselamatan anak kami.',
                    ],
                    [
                        'We are willing to give priority to the interests of the School in matters relating to our child\'s education and development, and agree to comply with the regulations and requirements established by the School, including the Parents Statement Form, Parents Handbook, school calendars, guidelines or SOPs, finance regulations, etc.',
                        'Kami bersedia mendahulukan dan mengutamakan kepentingan sekolah dalam hal-hal yang berkaitan dengan pendidikan dan perkembangan anak kami serta bersedia mengikuti semua peraturan dan ketentuan yang ditetapkan oleh Sekolah, termasuk Form Pernyataan Orang Tua, Panduan Orang Tua, kalender sekolah, kebijakan atau SOP, aturan keuangan, dan sebagainya.',
                    ],
                    [
                        'We are willing to communicate and coordinate with the School through the official communication channels designated by the School, and to maintain good, respectful, and professional communication with all members of the School community.',
                        'Kami bersedia berkomunikasi dan berkoordinasi dengan pihak Sekolah melalui saluran komunikasi resmi yang telah ditentukan oleh Sekolah, serta menjaga komunikasi yang baik, sopan, dan profesional dengan seluruh warga Sekolah.',
                    ],
                    [
                        'We are willing to attend meetings when requested or invited by the School, particularly when discussion is required regarding our child\'s development, education, behaviour, communication, health, or safety.',
                        'Kami bersedia datang dan menghadiri pertemuan apabila diminta atau diundang oleh pihak Sekolah, khususnya apabila diperlukan pembahasan mengenai perkembangan, pendidikan, perilaku, komunikasi, kesehatan, atau keselamatan anak kami.',
                    ],
                    [
                        'We are willing to raise any questions, complaints, or concerns with the School through the designated procedures and communication channels, and will not use social media or private communication channels to make decisions or resolve School matters without following the applicable procedures.',
                        'Kami bersedia menyampaikan pertanyaan, keluhan, atau permasalahan kepada pihak Sekolah melalui prosedur dan saluran komunikasi yang telah ditentukan, serta tidak menggunakan media sosial atau saluran pribadi untuk mengambil keputusan atau menyelesaikan permasalahan sekolah tanpa melalui prosedur yang berlaku.',
                    ],
                    [
                        'Using the official channel and process for questions, feedback, or concerns.',
                        'Menggunakan media dan proses resmi untuk menyampaikan pertanyaan, masukan, atau kekhawatiran.',
                    ],
                ],
            ],
            [
                'title_en' => 'Support for School Programmes & Policies',
                'title_id' => 'Mendukung Program dan Peraturan Sekolah',
                'items' => [
                    [
                        'We are willing to support all activities of our child, both at School and outside the School, and will provide education and activities at home that are aligned and consistent with the School\'s programs.',
                        'Kami bersedia mendukung seluruh kegiatan anak kami, baik di Sekolah maupun di luar Sekolah, dan akan memberikan pendidikan serta kegiatan di rumah yang searah dan sejalan dengan program Sekolah.',
                    ],
                    [
                        'By submitting this application, we fully agree to follow and support the School in its various programs and activities in pursuit of the School\'s objectives.',
                        'Dengan mengajukan aplikasi ini, kami sepenuhnya akan mengikuti serta mendukung Sekolah dalam berbagai program dan kegiatan untuk pencapaian tujuan Sekolah.',
                    ],
                    [
                        'We understand that, in addition to the national curriculum, the School also implements an international curriculum. Therefore, student assessment and promotion criteria are based on both curricula, and we agree to comply with them.',
                        'Kami memahami bahwa selain kurikulum nasional, Sekolah juga menggunakan kurikulum internasional sehingga penilaian dan kriteria promosi siswa didasarkan pada kedua kurikulum tersebut, dan kami bersedia mematuhinya.',
                    ],
                    [
                        'We are willing to serve as a member of the Parents and Teachers Association (Ittihada) within Mutiara Harapan Islamic School if appointed by the School.',
                        'Kami bersedia apabila ditunjuk oleh pihak Sekolah untuk menjadi pengurus Persatuan Orang Tua Murid dan Guru (Ittihada) di lingkungan Mutiara Harapan Islamic School.',
                    ],
                ],
            ],
            [
                'title_en' => 'Special Consents & Permissions (Development Class, Transportation, Psychological Assessment, Documentation)',
                'title_id' => 'Persetujuan & Izin Khusus (Development Class, Transportasi, Psikologi, Dokumentasi)',
                'items' => [
                    [
                        'If observation and clinical assessment indicate that our child requires special support, we agree to transfer our child to the Development Class and are willing to comply with all applicable Development Class requirements.',
                        'Apabila berdasarkan observasi dan uji klinis ternyata anak kami memerlukan penanganan khusus, kami setuju anak kami dipindahkan ke Development Class dan bersedia mengikuti seluruh ketentuan yang berlaku di Development Class.',
                    ],
                    [
                        'If we require the School\'s transportation service, we fully agree to the School\'s decision regarding transportation arrangements based on route surveys and/or the capacity of the School\'s vehicles.',
                        'Apabila kami memerlukan layanan antar-jemput sekolah, kami sepenuhnya setuju terhadap keputusan Sekolah yang ditetapkan berdasarkan survei rute dan/atau kapasitas kendaraan antar-jemput sekolah.',
                    ],
                    [
                        'We permit the School to conduct psychological assessments of our child when deemed necessary by the School, with notification to us for further approval.',
                        'Kami memberikan izin kepada pihak Sekolah untuk mengadakan tes psikologi pada anak kami apabila dirasakan perlu oleh Sekolah, dengan pemberitahuan kepada kami untuk persetujuan lebih lanjut.',
                    ],
                    [
                        'We give permission to the School to produce, store, process, reproduce, display, and use photographs, videos, audio recordings, and our child\'s work in various media for School purposes, including documentation, learning, communication, publication, and School promotion, in accordance with the applicable provisions.',
                        'Kami memberikan izin kepada Sekolah untuk memproduksi, menyimpan, mengolah, memperbanyak, menampilkan, dan menggunakan foto, video, rekaman suara, serta hasil karya anak kami dalam berbagai media untuk kepentingan sekolah, termasuk dokumentasi, pembelajaran, komunikasi, publikasi, dan promosi sekolah, sesuai dengan ketentuan yang berlaku.',
                    ],
                ],
            ],
            [
                'title_en' => 'Confidentiality of Information',
                'title_id' => 'Kerahasiaan Informasi',
                'items' => [
                    [
                        'We understand and respect the confidentiality of information relating to the School, children, families, and other members of the School community. We will not distribute photographs, videos, screenshots from the School\'s application, internal conversations, documents, or personal information relating to other children or families without written permission from the relevant party or the School.',
                        'Kami memahami dan menghormati kerahasiaan informasi yang berkaitan dengan Sekolah, anak, keluarga, maupun warga Sekolah lainnya. Kami tidak akan menyebarluaskan foto, video, tangkapan layar aplikasi Sekolah, percakapan internal, dokumen, atau informasi pribadi anak/keluarga lain tanpa izin tertulis dari pihak yang bersangkutan atau pihak Sekolah.',
                    ],
                    [
                        'We will not share complaints, photos/videos, screenshots, or information about other families in personal chats, private groups, or on social media.',
                        'Kami tidak akan menyebarkan keluhan, foto/video, tangkapan layar, atau informasi keluarga lain di percakapan personal, grup pribadi, ataupun media sosial.',
                    ],
                ],
            ],
            [
                'title_en' => 'Ethics, Conduct & Attitude toward the School Community',
                'title_id' => 'Etika, Perilaku & Sikap terhadap Komunitas Sekolah',
                'items' => [
                    [
                        'We are committed to upholding ethics, respectful conduct, safety, comfort, courtesy, and positive relationships with fellow parents and the School. We will not engage in any actions or behavior that disrupt the learning process, School safety, the well-being of children and the School community, or harm the positive relationships among parents, the School, and families.',
                        'Kami bersedia menjaga etika, ketertiban, keamanan, kenyamanan, kesopanan, dan hubungan baik sesama orang tua dan dengan Sekolah. Kami tidak akan melakukan tindakan atau perilaku yang mengganggu proses pembelajaran, ketertiban dan keamanan sekolah, kenyamanan anak maupun warga Sekolah, atau merusak hubungan baik antara sesama orang tua, Sekolah, dan keluarga.',
                    ],
                    [
                        'We agree to comply with and support the School in maintaining its good name and will not engage in any actions that may damage the School\'s reputation and good name. We accept that our child may be dismissed from the School if we or our child fail to comply with this provision.',
                        'Kami setuju untuk mematuhi dan mendukung upaya menjaga nama baik Sekolah serta tidak akan melakukan tindakan yang merusak reputasi dan nama baik Sekolah. Kami dapat menerima apabila anak kami dikeluarkan dari sekolah disebabkan oleh kami atau anak kami yang gagal memenuhi ketentuan ini.',
                    ],
                    [
                        'We will communicate respectfully with teachers, administrative staff, security personnel, children, other parents, and all members of the School community. We will not exert pressure, intimidate, threaten, or use inappropriate language or conduct toward anyone.',
                        'Kami akan berkomunikasi dengan hormat kepada guru, staf administrasi, petugas keamanan, anak-anak, orang tua lainnya, dan seluruh warga sekolah. Kami tidak akan memberikan tekanan, melakukan intimidasi, atau menggunakan tindakan maupun bahasa yang mengancam atau tidak pantas kepada pihak mana pun.',
                    ],
                    [
                        'We will engage teachers, administrative staff, security personnel, other parents, and all staff as professionals, with courteous language at all times.',
                        'Kami akan memperlakukan semua pihak yang kami temui sebagai profesional dengan bahasa yang sopan setiap saat.',
                    ],
                    [
                        'We will not yell at, interrogate, touch, record, or directly reprimand another child. We will report any concerns to the teacher or administration.',
                        'Kami tidak akan memarahi, menginterogasi, menyentuh, merekam, atau menegur anak lain secara langsung. Kami akan melaporkan kekhawatiran kepada guru atau administrasi.',
                    ],
                    [
                        'We will shield children from adult tension, whether it happens at the School gate, in a group chat, or on social media.',
                        'Kami akan melindungi anak dari ketegangan orang dewasa, baik yang terjadi di gerbang sekolah, grup chat, maupun media sosial.',
                    ],
                    [
                        'We will not bring household conflicts, financial matters, or personal complaints into children\'s areas or in front of children.',
                        'Kami tidak akan membawa konflik rumah tangga, pembiayaan, atau keluhan pribadi ke area anak atau di depan anak-anak.',
                    ],
                    [
                        'We will keep disagreements between adults private and constructive, resolving them through direct conversation rather than public commentary.',
                        'Kami akan menjaga ketidaksepakatan antarorang dewasa tetap privat dan konstruktif, serta menyelesaikannya melalui percakapan langsung, bukan komentar publik.',
                    ],
                    [
                        'We will not offer gifts, loans, commissions, or personal requests to teachers or staff that may interfere with professionalism.',
                        'Kami tidak akan menawarkan hadiah, pinjaman, komisi, atau permintaan pribadi kepada guru/staf yang dapat mengganggu profesionalisme.',
                    ],
                    [
                        'We will follow security instructions, visitor registration, appointments, and School area boundaries.',
                        'Kami akan mengikuti arahan keamanan, registrasi tamu, appointment, dan batas area sekolah.',
                    ],
                ],
            ],
            [
                'title_en' => 'Consequences of Violations & Disciplinary Actions',
                'title_id' => 'Konsekuensi Pelanggaran & Tindakan Disipliner',
                'items' => [
                    [
                        'We understand that if a parent/guardian or family representative fails to comply with the School\'s regulations, fails to cooperate reasonably, disrupts safety or order, damages the relationship between families and the School, or engages in actions that are detrimental to children, members of the School community, staff, or the School\'s reputation, the School has the right to take proportionate corrective or disciplinary action in accordance with the applicable provisions.',
                        'Kami memahami bahwa apabila orang tua/wali atau pihak yang mewakili keluarga tidak mematuhi peraturan sekolah, tidak bekerja sama secara wajar, mengganggu keamanan atau ketertiban, merusak hubungan antar sesama orang tua murid, sekolah, dan keluarga, atau melakukan tindakan yang merugikan anak, warga Sekolah, staf, maupun reputasi Sekolah, maka Sekolah berhak mengambil tindakan korektif atau disipliner yang proporsional sesuai dengan ketentuan yang berlaku.',
                    ],
                    [
                        'We understand that such corrective or disciplinary action may include verbal or written warnings, mandatory meetings with the School, restrictions on access to School premises, restrictions on communication channels, delays or restrictions relating to certain services or attendance, or other actions in accordance with the Parents Handbook, School policies, and regulations.',
                        'Kami memahami bahwa tindakan korektif atau disipliner tersebut dapat berupa teguran lisan atau tertulis, pertemuan wajib dengan pihak Sekolah, pembatasan akses ke area Sekolah, pembatasan saluran komunikasi, penundaan layanan/kehadiran tertentu, maupun tindakan lain sesuai dengan kebijakan dan ketentuan Sekolah yang dicantumkan dalam Parents Handbook.',
                    ],
                    [
                        'We understand that in the event of serious or repeated violations, the School may take further action, including reviewing our child\'s enrolment status or continuation of education at Mutiara Harapan Islamic School, in accordance with the applicable provisions.',
                        'Kami memahami bahwa apabila terjadi pelanggaran yang serius atau berulang, Sekolah dapat mengambil tindakan lebih lanjut, termasuk melakukan peninjauan terhadap status pendaftaran atau kelanjutan pendidikan anak kami di Mutiara Harapan Islamic School, sesuai dengan ketentuan yang berlaku.',
                    ],
                    [
                        'We understand that corrective or disciplinary action taken by the School does not automatically release us from our payment obligations or other financial requirements, unless otherwise determined in writing by the School.',
                        'Kami memahami bahwa tindakan disipliner atau korektif yang diberikan oleh Sekolah tidak serta-merta menghapus kewajiban pembayaran atau ketentuan finansial lainnya, kecuali ditentukan secara tertulis oleh pihak Sekolah.',
                    ],
                    [
                        'We are willing to follow the directions, support plans, or follow-up measures provided by the School in the best interests of our child\'s education, development, health, and safety.',
                        'Kami bersedia mengikuti arahan, rencana dukungan, atau tindak lanjut yang diberikan oleh Sekolah untuk kepentingan pendidikan, perkembangan, kesehatan, dan keselamatan anak kami.',
                    ],
                    [
                        'We understand that if the family is unwilling to cooperate or our child\'s needs exceed the School\'s capacity to provide safe support within a typical class, the School may review our child\'s placement or continuation of enrolment in accordance with the applicable provisions.',
                        'Kami memahami bahwa apabila keluarga tidak bersedia bekerja sama atau kebutuhan anak kami melebihi kemampuan Sekolah untuk memberikan layanan secara aman dalam standar kelas tipikal, Sekolah dapat melakukan peninjauan terhadap penempatan atau kelanjutan pendaftaran anak kami sesuai dengan ketentuan yang berlaku.',
                    ],
                    [
                        'If a parent/guardian, pick-up person, or family representative does not follow the School\'s rules, fails to cooperate reasonably, disrupts safety or order, damages the School-family relationship, or engages in conduct that may negatively affect the child, the School community, staff, or the School\'s reputation, the School may address the matter directly with the parent/guardian through a dedicated conversation and clarify the expectations for appropriate, constructive, and respectful conduct.',
                        'Apabila orang tua/wali, penjemput, atau perwakilan keluarga tidak mengikuti peraturan Sekolah, tidak bekerja sama secara wajar, mengganggu keselamatan/ketertiban, merusak hubungan Sekolah-keluarga, atau melakukan tindakan yang dapat merugikan anak, komunitas, staf, atau reputasi Sekolah, Sekolah berhak mengambil tindakan korektif dan/atau disipliner secara proporsional terhadap orang tua/wali terkait melalui pertemuan khusus serta menjelaskan kembali perilaku yang sesuai, konstruktif, dan saling menghormati.',
                    ],
                ],
            ],
            [
                'title_en' => 'School-Parent Partnership',
                'title_id' => 'Kemitraan Sekolah-Orang Tua',
                'items' => [
                    [
                        'We understand and agree that the School will take the necessary steps to support our child\'s education, health, and safety. We acknowledge that the School and parents have a close partnership and will work together to provide the best possible support for our child\'s growth and development.',
                        'Kami memahami dan menyetujui bahwa Sekolah akan melakukan langkah-langkah yang diperlukan untuk mendukung pendidikan, kesehatan, dan keselamatan anak kami. Kami menyadari bahwa Sekolah dan orang tua memiliki hubungan kemitraan yang erat dan selalu bekerja sama untuk memberikan yang terbaik bagi pertumbuhan dan perkembangan anak.',
                    ],
                    [
                        'Based on this mutual trust, we hereby release and discharge Mutiara Harapan Islamic School (MHIS), its owners/management, Principals, teachers, staff, vendors, affiliates, partners, and platforms collaborating with or endorsed by MHIS from any claims, demands, actions, objections, requests for damages, royalties, compensation, or other demands arising from the use of my child\'s photographs, videos, audio recordings, artwork, nickname, and/or documentation, provided that such use is made in accordance with this consent and is not intentionally intended to insult, demean, humiliate, or embarrass my child.',
                        'Dengan dasar saling percaya tersebut, kami menyatakan bahwa kami membebaskan, melepaskan, dan tidak akan menuntut Mutiara Harapan Islamic School (MHIS), pemilik/pengurus, Principal, guru, staf, vendor, afiliasi, partner, serta platform yang bekerja sama dengan atau didukung oleh MHIS dari segala tuntutan, klaim, gugatan, keberatan, permintaan ganti rugi, royalti, kompensasi, atau permintaan dalam bentuk apa pun yang timbul dari penggunaan foto, video, rekaman suara, karya anak, nama panggilan, dan/atau dokumentasi anak saya, sepanjang penggunaan tersebut dilakukan sesuai dengan persetujuan ini dan tidak dimaksudkan untuk secara sengaja menghina, merendahkan, atau mempermalukan anak.',
                    ],
                ],
            ],
        ];

        DB::transaction(function () use ($documentName, $version, $sections) {
            $document = AdmissionStatementDocument::create([
                'type' => 'parent',
                'name' => $documentName,
                'version' => $version,
                'status' => 'DRAFT',
            ]);

            foreach ($sections as $sectionIndex => $sectionData) {
                $section = $document->sections()->create([
                    'title_en' => $sectionData['title_en'],
                    'title_id' => $sectionData['title_id'],
                    'sort_order' => $sectionIndex + 1,
                    'is_required' => true,
                ]);

                foreach ($sectionData['items'] as $itemIndex => [$textEn, $textId]) {
                    $section->items()->create([
                        'number' => $itemIndex + 1,
                        'text_en' => $textEn,
                        'text_id' => $textId,
                        'sort_order' => $itemIndex + 1,
                        'is_required' => false,
                    ]);
                }
            }

            $document->update([
                'description_en' => 'Consent to this form shall be valid and binding whether provided through a physical or electronic signature, including by checking the consent box and/or submitting the online form provided by the School.',
                'description_id' => 'Persetujuan terhadap formulir ini sah dan mengikat, baik ditandatangani secara fisik maupun elektronik, termasuk dengan mencentang kotak persetujuan dan/atau mengirimkan formulir online yang disediakan oleh Sekolah.',
            ]);
        });

        $this->command->info('Created the MHIS Parent Statement as a draft (version ' . $version . ').');
    }
}
