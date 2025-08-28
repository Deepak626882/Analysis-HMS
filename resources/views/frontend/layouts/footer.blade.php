  <!-- ======= Footer ======= -->
  <footer id="footer">

      <div class="footer-top">
          <div class="container">
              <div class="row">

                  <div class="col-lg-3 col-md-6 footer-contact">
                      <div class="logo">
                          {{-- <h1 class="text-light"><a href="{{ url('/') }}">
                            {{ config('app.name', 'Laravel') }}</a></h1> --}}
                          <!-- Uncomment below if you prefer to use an image logo -->
                          <a href="{{ url('./') }}"><img src="{{ env('APP_FOOTERIMG', 'assets/img/logofooter.gif') }}"
                                  alt="" class="img-fluid"></a>
                      </div>
                      {{-- <p class="mt-3">
                          A108 Adam Street <br>
                          New York, NY 535022<br>
                          United States <br><br>
                          <strong>Phone:</strong> +1 5589 55488 55<br>
                          <strong>Email:</strong> info@example.com<br>
                      </p> --}}
                  </div>

                  <div class="col-lg-2 col-md-6 footer-links">
                      <h4>Useful Links</h4>
                      <ul>
                          <li><i class="bx bx-chevron-right"></i> <a href="#">Home</a></li>
                          <li><i class="bx bx-chevron-right"></i> <a href="#">About us</a></li>
                          <li><i class="bx bx-chevron-right"></i> <a href="#">Services</a></li>
                          <li><i class="bx bx-chevron-right"></i> <a href="#">Terms of service</a></li>
                          <li><i class="bx bx-chevron-right"></i> <a href="#">Privacy policy</a></li>
                      </ul>
                  </div>

                  <div class="col-lg-3 col-md-6 footer-links">
                      <h4>Our Services</h4>
                      {{-- <ul>
                          <li><i class="bx bx-chevron-right"></i> <a href="#">Web Design</a></li>
                          <li><i class="bx bx-chevron-right"></i> <a href="#">Web Development</a></li>
                          <li><i class="bx bx-chevron-right"></i> <a href="#">Product Management</a></li>
                          <li><i class="bx bx-chevron-right"></i> <a href="#">Marketing</a></li>
                          <li><i class="bx bx-chevron-right"></i> <a href="#">Graphic Design</a></li>
                      </ul> --}}
                  </div>

                  <div class="col-lg-4 col-md-6 footer-newsletter">
                      <h4>Join Our Newsletter</h4>
                      <p>Tamen quem nulla quae legam multos aute sint culpa legam noster magna</p>
                      <form action="" method="post">
                          <input type="email" name="email"><input type="submit" value="Subscribe">
                      </form>
                  </div>

              </div>
          </div>
      </div>

      <div class="container d-md-flex py-4">

          <div class="me-md-auto text-center text-md-start">
              <div class="copyright">
                  &copy; Copyright <strong><span>{{ config('app.name', 'Analysis') }} {{ date('Y') }}</span></strong>                  . All Rights Reserved
              </div>
              <div class="credits">
                  Analysis HMS
              </div>
          </div>
          <div class="social-links text-center text-md-right pt-3 pt-md-0">
              <a href="#" class="twitter"><i class="bx bxl-twitter"></i></a>
              <a href="#" class="facebook"><i class="bx bxl-facebook"></i></a>
              <a href="#" class="instagram"><i class="bx bxl-instagram"></i></a>
              <a href="#" class="google-plus"><i class="bx bxl-skype"></i></a>
              <a href="#" class="linkedin"><i class="bx bxl-linkedin"></i></a>
          </div>
      </div>
  </footer><!-- End Footer -->

  <a href="#" class="back-to-top d-flex align-items-center justify-content-center"><i
          class="bi bi-arrow-up-short"></i></a>

  <!-- Vendor JS Files -->
  <script src="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.js"></script>
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <script src="assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
  <script src="assets/vendor/glightbox/js/glightbox.min.js"></script>
  <script src="assets/vendor/isotope-layout/isotope.pkgd.min.js"></script>
  <script src="assets/vendor/swiper/swiper-bundle.min.js"></script>
  <script src="assets/vendor/waypoints/noframework.waypoints.js"></script>
  <script src="assets/vendor/php-email-form/validate.js"></script>

  <!-- Template Main JS File -->
  <script src="assets/js/main.js"></script>

  </body>

  </html>
  <script>
    $(document).ready(function() {
        AOS.init();
        var csrfToken = $('meta[name="csrf-token"]').attr('content');

        $('#demo-request-form').on('submit', function(e) {
            e.preventDefault();
            var formData = {
                name: $('#name').val(),
                email: $('#email').val(),
                phone_number: $('#phone_number').val(),
                hotel_name: $('#hotel_name').val(),
                message: $('#message').val(),
                _token: csrfToken
            };

            $.ajax({
                url: '{{ route('demo-request.store') }}',
                method: 'POST',
                data: formData,
                success: function(response) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Success!',
                        text: 'Request Submitted Successfully',
                    });
                    $('#demo-request-form')[0].reset();
                },
                error: function(xhr, status, error) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Oops...',
                        text: 'Error: ' + error,
                    });
                }
            });
        });
    });
</script>