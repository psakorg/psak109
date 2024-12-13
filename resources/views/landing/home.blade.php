
@include('landing.layouts.app')

<!-- Carousel Start -->
<div class="header-carousel owl-carousel">
    <div class="header-carousel-item bg-primary">
        <div class="carousel-caption">
            <div class="container">
                <div class="row g-4 align-items-center">
                    <div class="col-lg-7 animated fadeInLeft">
                        <div class="text-sm-center text-md-start">
                            <h4 class="text-white text-uppercase fw-bold mb-4">Selamat Datang di PSAK 109</h4>
                            <h1 class="display-1 text-white mb-4">Memahami Keuangan dengan Standar Terpercaya</h1>
                            <p class="mb-5 fs-5">Di dunia yang terus berubah, pengelolaan keuangan yang tepat dan transparan adalah kunci keberhasilan. PSak hadir sebagai panduan utama bagi para profesional dan praktisi akuntansi di Indonesia, membantu Anda menavigasi kompleksitas laporan keuangan sesuai dengan standar yang berlaku.</p>
                            <div class="d-flex justify-content-center justify-content-md-start flex-shrink-0 mb-4">
                                <a class="btn btn-light rounded-pill py-3 px-4 px-md-5 me-2" href="{{ route('under') }}"><i class="fas fa-play-circle me-2"></i> Lihat Selengkapnya</a>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-5 animated fadeInRight">
                        <div class="carousel-img" style="object-fit: cover;">
                            <img src="{{ asset('landing/img/carousel-2.png') }}" class="img-fluid w-100" alt="">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Carousel End -->

       <!-- Feature Start -->
       <div class="container-fluid feature bg-light py-5">
        <div class="container py-5">
            <div class="text-center mx-auto pb-5 wow fadeInUp" data-wow-delay="0.2s" style="max-width: 800px;">
                <h1 class="display-4 mb-4">Mengapa PSAK 109?</h1>
            </div>
            <div class="row g-4">
                <div class="col-md-6 col-lg-6 col-xl-3 wow fadeInUp" data-wow-delay="0.2s">
                    <div class="feature-item p-4 pt-0">
                        <div class="feature-icon p-4 mb-4">
                            <i class="far fa-handshake fa-3x"></i>
                        </div>
                        <h4 class="mb-4">Terpercaya</h4>
                        <p class="mb-4">Standar yang diakui secara nasional dan dirancang untuk memastikan konsistensi dan kejelasan dalam pelaporan keuangan.<br><br><br>
                        </p>
                        <a class="btn btn-primary rounded-pill py-2 px-4" href="{{ route('under') }}">Lihat Selengkapnya</a>
                    </div>
                </div>
                <div class="col-md-6 col-lg-6 col-xl-3 wow fadeInUp" data-wow-delay="0.4s">
                    <div class="feature-item p-4 pt-0">
                        <div class="feature-icon p-4 mb-4">
                            <i class="fa fa-dollar-sign fa-3x"></i>
                        </div>
                        <h4 class="mb-4">Akurasi dan Transparansi</h4>
                        <p class="mb-4">PSAK memberikan panduan yang jelas dan rinci, sehingga setiap laporan keuangan yang dihasilkan dapat dipercaya dan dipahami oleh semua pihak yang berkepentingan.

                        </p>
                        <a class="btn btn-primary rounded-pill py-2 px-4" href="{{ route('under') }}">Lihat Selengkapnya</a>
                    </div>
                </div>
                <div class="col-md-6 col-lg-6 col-xl-3 wow fadeInUp" data-wow-delay="0.6s">
                    <div class="feature-item p-4 pt-0">
                        <div class="feature-icon p-4 mb-4">
                            <i class="fa fa-bullseye fa-3x"></i>
                        </div>
                        <h4 class="mb-4">Mudah Diakses </h4>
                        <p class="mb-4">Semua dokumen dan sumber daya PSAK tersedia dalam satu platform yang mudah diakses kapan saja dan di mana saja, mendukung kebutuhan profesional Anda secara real-time.
                        </p>
                        <a class="btn btn-primary rounded-pill py-2 px-4" href="{{ route('under') }}">Lihat Selengkapnya</a>
                    </div>
                </div>
                <div class="col-md-6 col-lg-6 col-xl-3 wow fadeInUp" data-wow-delay="0.8s">
                    <div class="feature-item p-4 pt-0">
                        <div class="feature-icon p-4 mb-4">
                            <i class="fa fa-headphones fa-3x"></i>
                        </div>
                        <h4 class="mb-4">Pembaruan Berkala</h4>
                        <p class="mb-4">PSAK selalu diperbarui dengan perkembangan terbaru dalam dunia akuntansi dan regulasi keuangan, memastikan Anda selalu selangkah lebih maju.<br><br>
                        </p>
                        <a class="btn btn-primary rounded-pill py-2 px-4" href="{{ route('under') }}">Lihat Selengkapnya</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Feature End -->


@include('landing.layouts.footer')
