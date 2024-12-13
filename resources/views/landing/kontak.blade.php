@include('landing.layouts.app')
<!-- Contact Start -->
<div class="container-fluid contact bg-light py-2">
    <div class="container py-2">
        <div class="text-center mx-auto pb-5 wow fadeInUp" data-wow-delay="0.2s" style="max-width: 800px;">
            <h4 class="text-primary">Kontak Kami</h4>
            <h1 class="display-4 mb-4">Jika Anda memiliki pertanyaan jangan ragu untuk menghubungi kami.</h1>
        </div>
        <div class="row g-5 justify-content-center text-center"> <!-- justify-content-center untuk rata tengah -->
            <div class="col-md-6 col-lg-3 wow fadeInUp" data-wow-delay="0.2s">
                <div class="contact-add-item">
                    <div class="contact-icon text-primary mb-4">
                        <a href="https://maps.app.goo.gl/sW92bVXpx1SEWCuy7" target="_blank">
                            <i class="fas fa-map-marker-alt fa-2x"></i>
                        </a>
                    </div>
                    <h4>Alamat</h4>
                    <a href="https://maps.app.goo.gl/sW92bVXpx1SEWCuy7" target="_blank">
                        <p class="mb-0">Menara Kuningan 30th floor, Jl. H.R. Rasuna Said Kav.5.</p>
                    </a>
                </div>
            </div>
            <div class="col-md-6 col-lg-3 wow fadeInUp" data-wow-delay="0.4s">
                <div class="contact-add-item">
                    <div class="contact-icon text-primary mb-4">
                        <a href="https://mail.google.com/mail/?view=cm&fs=1&to=riyadi@pramatech.id" target="_blank">
                            <i class="fas fa-envelope fa-2x"></i>
                        </a>
                    </div>
                    <h4>Email</h4>
                    <a href="https://mail.google.com/mail/?view=cm&fs=1&to=riyadi@pramatech.id">
                        <p class="mb-0">riyadi@pramatech.id</p>
                    </a>
                </div>
            </div>
            <div class="col-md-6 col-lg-3 wow fadeInUp" data-wow-delay="0.6s">
                <div class="contact-add-item">
                    <div class="contact-icon text-primary mb-4">
                        <a href="https://wa.me/628561512634" target="_blank">
                            <i class="fa fa-phone-alt fa-2x"></i>
                        </a>
                    </div>
                    <h4>Telephone</h4>
                    <a href="https://wa.me/628561512634" target="_blank">
                        <p class="mb-0">+62 856 1512 634</p>
                    </a>
                </div>
            </div>
        </div>
        <div class="col-12 wow fadeInUp mt-3" data-wow-delay="0.2s">
            <div class="rounded">
                <iframe class="rounded w-100"
                style="height: 400px;" src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d19811.29817825043!2d106.83146492912765!3d-6.213291566056866!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2e69f6e8e9307c79%3A0x73aa626d81fa6a9f!2sMenara%20Kuningan!5e0!3m2!1sen!2sid!4v1694555139190!5m2!1sen!2sid" frameborder="0" allowfullscreen="" aria-hidden="false" tabindex="0"></iframe>
            </div>
        </div>
    </div>
</div>
<!-- Contact End -->

@include('landing.layouts.footer')
