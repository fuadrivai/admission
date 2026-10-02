<?php

namespace Database\Seeders;

use App\Models\AdmissionFinancialDocument;
use App\Models\AdmissionFinancialItem;
use App\Models\AdmissionFinancialSection;
use Illuminate\Database\Seeder;

class FinancialDocumentSeeder extends Seeder
{
    public function run()
    {
        if (AdmissionFinancialDocument::where('type', 'financial')->where('version', '1.0')->exists()) {
            return;
        }

        $document = AdmissionFinancialDocument::create([
            'type' => 'financial',
            'name' => 'Financial Agreement',
            'version' => '1.0',
            'status' => 'PUBLISHED',
            'effective_at' => now()->toDateString(),
            'description' => 'Initial published Financial Agreement document.',
        ]);

        $sections = [
            [
                'title_en' => 'Parent Statement: Financial',
                'title_id' => 'Pernyataan Orang Tua terkait Pembayaran',
                'sort_order' => 0,
                'items' => [[
                    'number' => 0,
                    'text_en' => 'I hereby acknowledge, understand, and agree that the payment obligations set forth by Mutiara Harapan Islamic School (MHIS) constitute my responsibility as a parent/guardian. I commit to settling all required fees in accordance with MHIS regulations before my child\'s first day of school, including completing all first-year payment obligations no later than June, as a prerequisite for my child\'s participation in academic activities.',
                    'text_id' => 'Dengan ini saya menyatakan bahwa saya mengetahui, memahami, dan menyetujui bahwa seluruh kewajiban pembayaran yang ditetapkan oleh Mutiara Harapan Islamic School (MHIS) merupakan tanggung jawab saya sebagai orang tua/wali. Saya berkomitmen untuk melunasi seluruh biaya yang diwajibkan sesuai dengan ketentuan MHIS sebelum hari pertama sekolah putra/putri saya, termasuk menyelesaikan seluruh kewajiban pembayaran tahun pertama paling lambat pada bulan Juni, sebagai syarat putra/putri saya mengikuti kegiatan akademik.',
                ]],
            ],
            [
                'title_en' => 'Payment Commitments',
                'title_id' => 'Komitmen Pembayaran',
                'sort_order' => 1,
                'items' => [
                    ['text_en' => 'The Development Fee shall be paid in accordance with the applicable regulations at the time of new student enrolment at Mutiara Harapan Islamic School.', 'text_id' => 'Development Fee akan saya bayarkan sesuai dengan ketentuan yang berlaku pada saat pendaftaran siswa baru di Mutiara Harapan Islamic School.'],
                    ['text_en' => 'The Annual Fee and School Fee shall be paid upon my child being officially accepted at Mutiara Harapan Islamic School.', 'text_id' => 'Annual Fee dan School Fee akan saya bayarkan pada saat putra/putri saya dinyatakan diterima di Mutiara Harapan Islamic School.'],
                    ['text_en' => 'International Examination Fees (Cambridge International Examinations: Checkpoint, IGCSE, and A Level) are mandatory, and the applicable amount shall be determined at the time the examinations are conducted.', 'text_id' => 'Biaya Ujian Internasional (Cambridge International Examinations: Checkpoint, IGCSE, dan A Level) bersifat wajib, dan besarnya akan ditetapkan pada saat ujian dilaksanakan.'],
                    ['text_en' => 'Learning Resource Fees, including printed textbooks, digital learning resources, and other educational materials, shall be charged in accordance with the school\'s applicable provisions.', 'text_id' => 'Biaya Sumber Pembelajaran, termasuk buku cetak, sumber pembelajaran digital, dan materi pembelajaran lainnya, akan dikenakan sesuai dengan ketentuan yang berlaku di sekolah.'],
                    ['text_en' => 'I acknowledge and agree that my child shall participate in the school\'s mandatory extracurricular programme, and I agree to pay the applicable Extracurricular Fee according to the activity selected for my child.', 'text_id' => 'Saya mengetahui dan menyetujui bahwa putra/putri saya akan mengikuti program ekstrakurikuler wajib yang diselenggarakan sekolah, dan saya bersedia membayarkan biaya ekstrakurikuler sesuai dengan kegiatan yang dipilih untuk putra/putri saya.'],
                    ['text_en' => 'Other Activity Fees shall be determined according to the nature and timing of each programme, including but not limited to Gateway Programmes, overseas programmes, immersion programmes, competitions, and other school activities.', 'text_id' => 'Biaya Kegiatan Lainnya akan ditetapkan sesuai dengan jenis dan waktu pelaksanaan setiap kegiatan, termasuk namun tidak terbatas pada Gateway Programme, field study, educational outing, program luar negeri, immersion programme, kompetisi, dan kegiatan sekolah lainnya.'],
                    ['text_en' => 'The School Fee shall be paid no later than the 10th day of each month, in accordance with the payment scheme selected during enrolment.', 'text_id' => 'School Fee akan saya bayarkan paling lambat pada tanggal 10 setiap bulan, sesuai dengan skema pembayaran yang dipilih pada saat pendaftaran.'],
                    ['text_en' => 'The Ittihada Fee, the amount of which shall be determined by Ittihada, shall be paid annually.', 'text_id' => 'Biaya Ittihada, yang besarnya ditetapkan oleh Ittihada, akan dibayarkan setiap tahun.'],
                    ['text_en' => 'For Secondary students, I acknowledge and support my child\'s participation in Mutiara Harapan Student Union (MHSU) activities and agree to pay the applicable fees in accordance with the school\'s provisions.', 'text_id' => 'Untuk siswa Secondary, saya mengetahui dan mendukung keikutsertaan putra/putri saya dalam kegiatan Mutiara Harapan Student Union (MHSU) serta bersedia membayarkan biaya yang berlaku sesuai dengan ketentuan sekolah.'],
                    ['text_en' => 'I agree to purchase and pay for my child\'s school uniform during the initial enrolment process in accordance with the school\'s applicable provisions.', 'text_id' => 'Saya bersedia melakukan pembelian dan pembayaran seragam sekolah pada tahap awal pendaftaran sesuai dengan ketentuan yang berlaku di sekolah.'],
                ],
            ],
            [
                'title_en' => 'Financial Policies',
                'title_id' => 'Kebijakan Pembayaran',
                'sort_order' => 2,
                'items' => [
                    ['number' => 0, 'text_en' => 'I further acknowledge and agree that:', 'text_id' => 'Selanjutnya saya juga menyatakan setuju bahwa:'],
                    ['text_en' => 'The Annual Fee is an annual educational fee that supports my child\'s education for one academic year, including but not limited to school fairs, outings and field studies, the development and maintenance of school systems that strengthen parent communication and student learning, learning toolkits, laboratory facilities and materials, educational resources, and other educational programmes and operational needs that support the learning experience.', 'text_id' => 'Saya mengetahui dan menyetujui bahwa Annual Fee merupakan biaya pendidikan tahunan yang mendukung proses pendidikan putra/putri saya selama satu tahun ajaran, termasuk namun tidak terbatas pada school fairs, outing atau field study, pengembangan dan pemeliharaan sistem sekolah untuk komunikasi dengan orang tua maupun penguatan pembelajaran siswa, learning toolkit, kebutuhan laboratorium beserta perlengkapannya, sumber daya pembelajaran, serta program dan kebutuhan operasional pendidikan lainnya yang mendukung proses belajar.'],
                    ['text_en' => 'The Annual Fee may be adjusted every two (2) years in accordance with the school\'s policy.', 'text_id' => 'Annual Fee dapat mengalami penyesuaian setiap dua (2) tahun sesuai dengan kebijakan sekolah.'],
                    ['text_en' => 'The School Fee may be adjusted every three (3) years in accordance with the school\'s policy.', 'text_id' => 'School Fee dapat mengalami penyesuaian setiap tiga (3) tahun sesuai dengan kebijakan sekolah.'],
                    ['text_en' => 'Any payment made to Mutiara Harapan Islamic School is non-refundable, non-transferable, and may not be reallocated to any other fees, programmes, or payment obligations. The only exception shall apply if my child is officially declared not accepted by Mutiara Harapan Islamic School through the school\'s formal written admission decision after completing the required admission process.', 'text_id' => 'Setiap pembayaran yang telah saya lakukan kepada Mutiara Harapan Islamic School bersifat tidak dapat dikembalikan (non-refundable), tidak dapat dialihkan (non-transferable), serta tidak dapat dialokasikan ke biaya, program, atau kewajiban pembayaran lainnya. Pengecualian hanya berlaku apabila putra/putri saya secara resmi dinyatakan tidak diterima oleh Mutiara Harapan Islamic School melalui keputusan tertulis sekolah setelah menyelesaikan seluruh tahapan penerimaan yang dipersyaratkan.'],
                    ['text_en' => 'My child\'s academic evaluation results may only be released after all administrative obligations required by Mutiara Harapan Islamic School have been fulfilled.', 'text_id' => 'Hasil evaluasi belajar putra/putri saya hanya dapat diberikan setelah seluruh kewajiban administrasi yang ditetapkan oleh Mutiara Harapan Islamic School telah dipenuhi.'],
                    ['text_en' => 'I understand and agree that my child may not attend the first day of school, participate in academic activities, or continue attending classes, and I may not participate in Parent Meetings or receive school administrative services related to my child\'s enrolment, until all first-year financial obligations required by Mutiara Harapan Islamic School have been fully settled.', 'text_id' => 'Saya memahami dan menyetujui bahwa putra/putri saya tidak diperkenankan mengikuti hari pertama sekolah, kegiatan akademik, maupun melanjutkan kehadiran di kelas, serta saya tidak dapat mengikuti Parent Meeting atau memperoleh layanan administrasi sekolah yang berkaitan dengan proses pendidikan putra/putri saya sampai seluruh kewajiban pembayaran tahun pertama yang ditetapkan oleh Mutiara Harapan Islamic School telah dilunasi.'],
                ],
            ],
            [
                'title_en' => 'School–Parent Partnership',
                'title_id' => 'Kemitraan Sekolah–Orang Tua',
                'sort_order' => 3,
                'items' => [
                    ['text_en' => 'I hereby confirm that I have read, understood, and agreed to all provisions contained in this School Fee Payment Agreement. I sign this agreement voluntarily, in sound physical and mental condition, and without any pressure or coercion from any party.', 'text_id' => 'Saya menyatakan bahwa saya telah membaca, memahami, dan menyetujui seluruh ketentuan dalam Surat Persetujuan Pembayaran Biaya Sekolah ini. Saya menandatangani persetujuan ini secara sadar, dalam keadaan sehat jasmani dan rohani, serta tanpa tekanan maupun paksaan dari pihak mana pun.'],
                    ['text_en' => 'Consent to this form shall be valid and binding whether provided through a physical or electronic signature, including by checking the consent box and/or submitting the online form provided by the school.', 'text_id' => 'Persetujuan terhadap form ini sah dan mengikat baik ditandatangani secara fisik maupun elektronik, termasuk dengan mencentang kotak persetujuan dan/atau mengirimkan formulir online yang disediakan sekolah.'],
                ],
            ],
        ];

        foreach ($sections as $sectionData) {
            $items = $sectionData['items'];
            unset($sectionData['items']);

            $section = AdmissionFinancialSection::create(array_merge($sectionData, [
                'document_id' => $document->id,
            ]));

            $nextNumber = 1;
            foreach ($items as $index => $itemData) {
                $number = isset($itemData['number']) ? $itemData['number'] : $nextNumber;
                if ($number > 0) {
                    $nextNumber = $number + 1;
                }

                AdmissionFinancialItem::create(array_merge([
                    'section_id' => $section->id,
                    'number' => $number,
                    'sort_order' => $index,
                    'is_required' => true,
                ], $itemData));
            }
        }
    }
}
