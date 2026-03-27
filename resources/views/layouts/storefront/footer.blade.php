<footer class="ord-footer">
    <div class="ord-footer-top">
        <div class="container">
            <div class="row">

                {{-- Col 1: About --}}
                <div class="col-12 col-md-4 col-lg-4 mb-40">
                    <a href="{{ route('home') }}" class="ord-footer-logo">
                        <img src="{{ asset('img/core-img/logo2.png') }}" alt="Orderer">
                    </a>
                    <p class="ord-footer-about">
                        Orderer is an all-in-one marketplace — buy, sell, and deliver anything, anywhere.
                        From everyday essentials and branded goods to real estate, services, and last-mile rider
                        bookings, Orderer brings commerce to your fingertips. Built for individuals and businesses
                        across the globe.
                    </p>
                    <p class="ord-footer-parent">
                        A product of <strong>AfricGEM International Company Limited</strong>
                    </p>
                    <div class="ord-footer-social">
                        <a href="#" title="Facebook"><i class="fa fa-facebook"></i></a>
                        <a href="#" title="Instagram"><i class="fa fa-instagram"></i></a>
                        <a href="#" title="Twitter / X"><i class="fa fa-twitter"></i></a>
                        <a href="#" title="Pinterest"><i class="fa fa-pinterest"></i></a>
                        <a href="#" title="YouTube"><i class="fa fa-youtube-play"></i></a>
                    </div>
                </div>

                {{-- Col 2: Explore --}}
                <div class="col-6 col-md-2 col-lg-2 mb-40">
                    <h6 class="ord-footer-heading">Explore</h6>
                    <ul class="ord-footer-links">
                        <li><a href="{{ route('shop.index') }}">Shop</a></li>
                        <li><a href="{{ route('brands.index') }}">Brands</a></li>
                        <li><a href="{{ route('services.index') }}">Services</a></li>
                        <li><a href="{{ route('houses.index') }}">Properties</a></li>
                        <li><a href="{{ route('rider.booking') }}">Book a Rider</a></li>
                        <li><a href="{{ route('contact') }}">Contact Us</a></li>
                    </ul>
                </div>

                {{-- Col 3: Sellers --}}
                <div class="col-6 col-md-2 col-lg-2 mb-40">
                    <h6 class="ord-footer-heading">Sellers</h6>
                    <ul class="ord-footer-links">
                        <li><a href="{{ route('seller.register') }}">Start Selling</a></li>
                        <li><a href="{{ route('seller.login') }}">Seller Login</a></li>
                        <li><a href="#">Seller Guide</a></li>
                        <li><a href="#">Fees &amp; Commission</a></li>
                    </ul>
                </div>

                {{-- Col 4: Support --}}
                <div class="col-6 col-md-2 col-lg-2 mb-40">
                    <h6 class="ord-footer-heading">Support</h6>
                    <ul class="ord-footer-links">
                        <li><a href="#">Order Status</a></li>
                        <li><a href="#">Payment Options</a></li>
                        <li><a href="#">Shipping &amp; Delivery</a></li>
                        <li><a href="#">Privacy Policy</a></li>
                        <li><a href="#">Terms of Use</a></li>
                        <li><a href="#">Refund Policy</a></li>
                    </ul>
                </div>

                {{-- Col 5: Newsletter --}}
                <div class="col-12 col-md-2 col-lg-2 mb-40">
                    <h6 class="ord-footer-heading">Newsletter</h6>
                    <p class="ord-footer-newsletter-text">Get deals, new arrivals and updates straight to your inbox.</p>
                    <form class="ord-footer-newsletter" action="{{ route('newsletter.subscribe') }}" method="POST">
                        @csrf
                        <input type="email" name="email" placeholder="Your email address" required>
                        <button type="submit"><i class="fa fa-paper-plane"></i></button>
                    </form>
                </div>

            </div>
        </div>
    </div>

    {{-- Bottom bar --}}
    <div class="ord-footer-bottom">
        <div class="container">
            <div class="ord-footer-bottom-inner">
                <p>&copy; {{ date('Y') }} Orderer — <span>AfricGEM International Company Limited</span>. All rights reserved.</p>
                <div class="ord-footer-bottom-links">
                    <a href="#">Privacy Policy</a>
                    <a href="#">Terms of Use</a>
                    <a href="{{ route('contact') }}">Contact</a>
                </div>
            </div>
        </div>
    </div>
</footer>