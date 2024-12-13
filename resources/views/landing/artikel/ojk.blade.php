@include('landing.layouts.app')

<div class="row justify-content-center">
    <div class="col-md-10 wow fadeInUp text-center" data-wow-delay="0.2s">
        <div class="feature-item mb-5">
            <img src="{{ asset('img/OJK.jpeg') }}" class="img-fluid mb-3" alt="Terpercaya" style="width: 80%; max-width: 500px;">
            <h4 class="mb-3">Siaran Pers:</h4>
            <h5>OJK Keluarkan Panduan Penerapan PSAK 71 dan PSAK 68 untuk Perbankan di Masa Pandemi Covid-19</h5>
            <div style="text-align: justify;">
                <p>
                    Jakarta, 15 April 2020, Otoritas Jasa Keuangan (OJK) telah mengeluarkan panduan perlakuan akuntansi terutama dalam penerapan PSAK 71-Instrumen Keuangan dan PSAK 68-Pengukuran Nilai Wajar. Panduan ini dikeluarkan terkait dengan dampak pandemi Covid-19 yang telah menimbulkan ketidakpastian ekonomi global dan domestik serta secara signifikan memengaruhi pertimbangan entitas dalam menyusun laporan keuangan. Surat Edaran mengenai hal tersebut ditandatangani oleh Kepala Eksekutif Pengawas Perbankan OJK, Heru Kristiyana.
                </p>
                <p>
                    Surat tersebut mengacu pada POJK No. 11/POJK.03/2020 serta panduan Dewan Standar Akuntansi Keuangan - Ikatan Akuntan Indonesia (DSAK-IAI) pada tanggal 2 April 2020 tentang Dampak Pandemi Covid-19 terhadap Penerapan PSAK 8 - Peristiwa setelah Periode Pelaporan dan PSAK 71 - Instrumen Keuangan. Oleh karena itu, kepada perbankan diminta untuk:
                </p>
                <ul>
                    <li>Mematuhi dan melaksanakan POJK No. 11/POJK.03/2020 serta secara proaktif mengidentifikasi debitur-debitur yang selama ini berkinerja baik namun menurun kinerjanya karena terdampak Covid-19.</li>
                    <li>Menerapkan skema restrukturisasi mengacu pada hasil asesmen yang akurat disesuaikan dengan profil debitur, dengan jangka waktu maksimal 1 (satu) tahun, dan hanya diberikan kepada debitur yang benar-benar terdampak Covid-19.</li>
                    <li>Menggolongkan debitur yang mendapatkan skema restrukturisasi dalam Stage-1 dan tidak diperlukan tambahan Cadangan Kerugian Penurunan Nilai (CKPN).</li>
                    <li>Melakukan identifikasi dan monitoring secara berkelanjutan serta siap untuk membentuk CKPN apabila debitur yang telah mendapatkan fasilitas restrukturisasi berkinerja baik pada awalnya, namun diperkirakan menurun karena terdampak Covid-19 dan tidak dapat pulih pasca restrukturisasi.</li>
                </ul>
                <p>
                    Selain itu, OJK, dengan mempertimbangkan release DSAK-IAI tanggal 5 April tentang Dampak Pandemi Covid-19 terhadap PSAK 68 - Pengukuran Nilai Wajar, juga memberikan panduan penyesuaian bagi perbankan dalam pengukuran nilai wajar, khususnya terkait penilaian surat-surat berharga. Hal ini mengingat tingginya volatilitas dan penurunan signifikan volume transaksi di bursa efek yang mempengaruhi pertimbangan bank dalam menentukan nilai wajar surat berharga. Panduan yang diberikan kepada bank adalah:
                </p>
                <ul>
                    <li>Menunda penilaian yang mengacu pada harga pasar (mark to market) untuk Surat Utang Negara dan surat-surat berharga lain yang diterbitkan Pemerintah, termasuk surat berharga yang diterbitkan oleh Bank Indonesia, selama 6 (enam) bulan. Selama masa penundaan, perbankan dapat menggunakan harga kuotasian tanggal 31 Maret 2020 untuk penilaian surat-surat berharga tersebut.</li>
                    <li>Menunda penilaian yang mengacu pada harga pasar untuk surat-surat berharga lain selama 6 (enam) bulan, sepanjang perbankan meyakini kinerja penerbit (issuer) surat-surat berharga tersebut dinilai baik sesuai kriteria tertentu. Jika kinerja penerbit dinilai tidak/kurang baik, perbankan dapat melakukan penilaian berdasarkan model sendiri dengan menggunakan asumsi seperti suku bunga, credit spread, dan risiko kredit penerbit.</li>
                    <li>Melakukan pengungkapan yang menjelaskan perbedaan perlakuan akuntansi yang mengacu pada panduan OJK dengan SAK sebagaimana dipersyaratkan dalam PSAK 68.</li>
                </ul>
                <p>
                    Untuk informasi lebih lanjut, Anda dapat membaca siaran pers lengkapnya di <a href="https://ojk.go.id/id/berita-dan-kegiatan/info-terkini/Pages/Siaran-Pers-OJK-Keluarkan-Panduan-Penerapan-PSAK-71-dan-PSAK-68-untuk-Perbankan-di-Masa-Pandemi-Covid--19.aspx" target="_blank">sini</a>.
                </p>
            </div>
        </div>
    </div>
</div>

<!-- Tombol Kembali di Pojok Kiri Bawah -->
<div style="position: fixed; bottom: 20px; left: 20px;">
    <a class="btn btn-primary rounded-pill py-2 px-4" href="{{ url('/artikel') }}">Kembali</a>
</div>

@include('landing.layouts.footer')
