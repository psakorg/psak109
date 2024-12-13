@include('landing.layouts.app')

<div class="d-flex justify-content-center align-items-center min-vh-100">
    <div class="col-md-10 wow fadeInUp" data-wow-delay="0.8s">
        <div class="feature-item text-center mb-5">
            <img src="{{ asset('img/psak.jpg') }}" class="img-fluid mb-3" alt="Pembaruan Berkala" style="width: 100%; max-width: 500px;">
            <h4 class="mb-3">Standar Akuntansi Baru PSAK 71, 72, dan 73 Berlaku 2020: Ini Perbedaannya</h4>

            <!-- Wrapper for justified text -->
            <div style="text-align: justify;">
                <p>Ketiga PSAK ini memiliki poin masing-masing. PSAK 71 mengatur mengenai instrumen keuangan, PSAK 72 mengatur mengenai pendapatan dari kontrak dengan pelanggan, dan PSAK 73 mengatur mengenai sewa. Berikut adalah detail perubahan yang harus diadopsi berdasarkan masing-masing PSAK tersebut.</p>

                <h5 style="text-align: center;">PSAK 71</h5>
                <p>Standar Akuntansi Keuangan (PSAK) 71 memberikan panduan tentang pengakuan dan pengukuran instrumen keuangan. Standar ini mengacu kepada International Financial Reporting Standard (IFRS) 9 dan akan menggantikan PSAK 55 yang sebelumnya berlaku. Selain klasifikasi aset keuangan, salah satu poin penting PSAK 71 adalah pencadangan atas penurunan nilai aset keuangan, yang mencakup piutang, pinjaman, atau kredit.</p>
                <p>Standar baru ini mengubah secara mendasar metode penghitungan dan penyediaan cadangan untuk kerugian akibat pinjaman yang tak tertagih. Berdasarkan PSAK 55, kewajiban pencadangan baru muncul setelah terjadi peristiwa yang mengakibatkan risiko gagal bayar (incurred loss). Namun, PSAK 71 memandatkan korporasi untuk menyediakan pencadangan sejak awal periode kredit. Dasar pencadangan kini adalah ekspektasi kerugian kredit (expected credit loss) di masa mendatang, yang mempertimbangkan berbagai faktor, termasuk proyeksi ekonomi.</p>

                <h5 style="text-align: center;">PSAK 72</h5>
                <p>PSAK 72 tentang Pengakuan Pendapatan dari Kontrak dengan Pelanggan merupakan adopsi IFRS 15 yang telah berlaku di Eropa sejak Januari 2018. PSAK 72 disebut sebagai PSAK "sapu jagat" karena mengganti banyak standar sebelumnya. Beberapa standar yang dicabut dengan terbitnya PSAK 72 meliputi:</p>
                <ul>
                    <li>PSAK 34 tentang Kontrak Konstruksi</li>
                    <li>PSAK 32 tentang Pendapatan</li>
                    <li>ISAK 10 tentang Program Loyalitas Pelanggan</li>
                    <li>ISAK 21 tentang Perjanjian Konstruksi Real Estate</li>
                    <li>ISAK 27 tentang Pengalihan Aset dari Pelanggan</li>
                </ul>

                <h5 style="text-align: center;">PSAK 73</h5>
                <p>Standar baru ini akan mengubah secara substansial pembukuan transaksi sewa dari sisi penyewa (lessee). Berdasarkan PSAK 73, korporasi penyewa diwajibkan membukukan hampir semua transaksi sewanya sebagai sewa finansial (financial lease). Pembukuan sewa operasi (operating lease) hanya boleh dilakukan untuk transaksi sewa yang memenuhi dua syarat:</p>
                <ul>
                    <li>Berjangka pendek (di bawah 12 bulan)</li>
                    <li>Bernilai rendah (misalnya sewa ponsel, laptop, dan sejenisnya)</li>
                </ul>
            </div>

            <!-- Tambahan link di bawah -->
            <p class="mt-4" style="text-align: justify;">
                Untuk informasi lebih lanjut, silakan kunjungi:
                <a href="https://investasi.kontan.co.id/news/standarisasi-akuntansi-baru-psak-71-72-dan-73-berlaku-2020-ini-perbedaannya?page=3" target="_blank">Standarisasi Akuntansi Baru PSAK 71, 72, dan 73 Berlaku 2020</a>
            </p>
        </div>
    </div>
</div>

<!-- Tombol Kembali di Pojok Kiri Bawah -->
<div style="position: fixed; bottom: 20px; left: 20px;">
    <a class="btn btn-primary rounded-pill py-2 px-4" href="{{ url('/artikel') }}">Kembali</a>
</div>

@include('landing.layouts.footer')
