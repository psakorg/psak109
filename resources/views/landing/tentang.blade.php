
@include('landing.layouts.app')
 <!-- FAQs Start -->
 <div class="container-fluid faq-section bg-light py-5">
    <div class="container py-5">
        <div class="row g-5 align-items-center">
            <div class="col-xl-6 wow fadeInLeft" data-wow-delay="0.2s">
                <div class="h-100">
                    <div class="mb-5">
                        <h4 class="text-primary">Beberapa Pertanyaan Penting
                        </h4>
                        <h1 class="display-4 mb-0">Pertanyaan yang Sering Diajukan</h1>
                    </div>
                    <div class="accordion" id="accordionExample">
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="headingOne">
                                <button class="accordion-button border-0" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                                    Pertanyaan: Apa itu PSAK?
                                </button>
                            </h2>
                            <div id="collapseOne" class="accordion-collapse collapse show active" aria-labelledby="headingOne" data-bs-parent="#accordionExample">
                                <div class="accordion-body rounded">
                                    Jawaban: PSAK (Pernyataan Standar Akuntansi Keuangan) adalah standar yang ditetapkan oleh Ikatan Akuntan Indonesia (IAI) yang mengatur bagaimana laporan keuangan harus disusun dan disajikan di Indonesia. PSak dirancang untuk memastikan transparansi, konsistensi, dan akurasi dalam pelaporan keuangan.
                                </div>
                            </div>
                        </div>
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="headingTwo">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
                                    Pertanyaan:  Mengapa PSAK penting?
                                </button>
                            </h2>
                            <div id="collapseTwo" class="accordion-collapse collapse" aria-labelledby="headingTwo" data-bs-parent="#accordionExample">
                                <div class="accordion-body">
                                    Jawaban: PSAK penting karena memastikan bahwa laporan keuangan yang dibuat oleh perusahaan di Indonesia mengikuti standar yang diakui secara nasional. Ini membantu dalam meningkatkan kepercayaan pemangku kepentingan, seperti investor, kreditur, dan pihak berwenang, terhadap informasi keuangan yang disajikan.
                                </div>
                            </div>
                        </div>
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="headingThree">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseThree" aria-expanded="false" aria-controls="collapseThree">
                                    Pertanyaan: Siapa yang harus mematuhi PSAK?
                                </button>
                            </h2>
                            <div id="collapseThree" class="accordion-collapse collapse" aria-labelledby="headingThree" data-bs-parent="#accordionExample">
                                <div class="accordion-body">
                                    Jawaban: PSAK berlaku untuk semua entitas yang beroperasi di Indonesia, termasuk perusahaan publik dan swasta, lembaga non-profit, serta entitas pemerintah. Setiap entitas yang diwajibkan untuk menyusun laporan keuangan harus mematuhi PSak.
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-6 wow fadeInRight" data-wow-delay="0.4s">
                <img src="{{ asset('landing/img/carousel-2.png') }}" class="img-fluid w-100" alt="">
            </div>
        </div>
    </div>
</div>
<!-- FAQs End -->
@include('landing.layouts.footer')
