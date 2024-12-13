@include('landing.layouts.app')

<!-- Feature Start -->
<div class="container-fluid feature bg-light py-2">
    <div class="container">
        <div class="text-center mx-auto pb-2 wow fadeInUp" data-wow-delay="0.2s" style="max-width: 800px;">
            <h1 class="display-4 mb-4">Artikel</h1>
        </div>
        <div class="row justify-content-center d-flex">
            <div class="col-md-5 d-flex wow fadeInUp" data-wow-delay="0.2s">
                <div class="feature-item text-center mb-5 d-flex flex-column" style="flex-grow: 1;">
                    <img src="{{ asset('img/psak.jpg') }}" class="img-fluid mb-3 mx-auto" alt="Pembaruan Berkala" style="width: 80%; max-width: 300px;">
                    <h4 class="mb-3">Standar Akuntansi Baru PSAK 71, 72, dan 73 Berlaku 2020, Ini Perbedaannya</h4>
                    <p>Ketiga PSAK itu memiliki poin masing-masing. PSAK 71 misalnya mengatur mengenai instrumen keuangan...</p>
                    <div class="mt-auto">
                        <a class="btn btn-primary rounded-pill py-2 px-4" href="{{ url('/artikel/perbedaanpsak') }}">Lihat Selengkapnya</a>
                    </div>
                </div>
            </div>
            <div class="col-md-5 d-flex wow fadeInUp" data-wow-delay="0.4s">
                <div class="feature-item text-center mb-5 d-flex flex-column" style="flex-grow: 1;">
                    <img src="{{ asset('img/OJK.jpeg') }}" class="img-fluid mb-3 mx-auto" alt="Terpercaya" style="width: 80%; max-width: 300px;">
                    <h4 class="mb-3">Siaran Pers: OJK Keluarkan Panduan Penerapan PSAK 71 dan PSAK 68</h4>
                    <p>Siaran Pers OJK Keluarkan Panduan Penerapan PSAK 71 Dan PSAK 68 Untuk Perbankan...</p>
                    <div class="mt-auto">
                        <a class="btn btn-primary rounded-pill py-2 px-4" href="{{ url('/artikel/ojk') }}">Lihat Selengkapnya</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Feature End -->

@include('landing.layouts.footer')
