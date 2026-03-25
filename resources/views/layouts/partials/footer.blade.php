<footer class="footer_area clearfix">
    <div class="container">
        <div class="row">

            {{-- Logo + quick links --}}
            <div class="col-12 col-md-3">
                <div class="single_widget_area d-flex mb-30">
                    <div class="footer-logo mr-50">
                        <a href="{{ route('home') }}">
                            <img src="{{ asset('img/core-img/logo2.png') }}" alt="Orderer">
                        </a>
                    </div>
                    <div class="footer_menu">
                        <ul>
                            <li><a href="{{ route('shop.index') }}">Shop</a></li>
                            <li><a href="{{ route('brands.index') }}">Brands</a></li>
                            <li><a href="{{ route('services.index') }}">Services</a></li>
                            <li><a href="{{ route('houses.index') }}">Properties</a></li>
                            <li><a href="{{ route('rider.booking') }}">Book a Rider</a></li>
                            <li><a href="{{ route('contact') }}">Contact</a></li>
                        </ul>
                    </div>
                </div>
            </div>

            {{-- Seller links --}}
            <div class="col-12 col-md-3">
                <div class="single_widget_area mb-30">
                    <h6 class="footer_heading mb-15">Sellers</h6>
                    <ul class="footer_widget_menu">
                        <li><a href="{{ route('seller.register') }}">Start Selling</a></li>
                        <li><a href="{{ route('seller.login') }}">Seller Login</a></li>
                        <li><a href="#">Seller Guide</a></li>
                        <li><a href="#">Fees & Commissions</a></li>
                    </ul>
                </div>
            </div>

            {{-- Support links --}}
            <div class="col-12 col-md-3">
                <div class="single_widget_area mb-30">
                    <h6 class="footer_heading mb-15">Support</h6>
                    <ul class="footer_widget_menu">
                        <li><a href="#">Order Status</a></li>
                        <li><a href="#">Payment Options</a></li>
                        <li><a href="#">Shipping & Delivery</a></li>
                        <li><a href="#">Privacy Policy</a></li>
                        <li><a href="#">Terms of Use</a></li>
                    </ul>
                </div>
            </div>

            {{-- Newsletter --}}
            <div class="col-12 col-md-3">
                <div class="single_widget_area">
                    <div class="footer_heading mb-30">
                        <h6>Subscribe</h6>
                    </div>
                    <div class="subscribtion_form">
                        <form action="{{ route('newsletter.subscribe') }}" method="POST">
                            @csrf
                            <input type="email"
                                   name="email"
                                   class="mail"
                                   placeholder="Your email here"
                                   required>
                            <button type="submit" class="submit">
                                <i class="fa fa-long-arrow-right" aria-hidden="true"></i>
                            </button>
                        </form>
                    </div>
                </div>
            </div>

        </div>

        {{-- Social + copyright --}}
        <div class="row align-items-center mt-30">
            <div class="col-12 col-md-6">
                <div class="footer_social_area">
                    <a href="#" title="Facebook">
                        <i class="fa fa-facebook" aria-hidden="true"></i>
                    </a>
                    <a href="#" title="Instagram">
                        <i class="fa fa-instagram" aria-hidden="true"></i>
                    </a>
                    <a href="#" title="Twitter">
                        <i class="fa fa-twitter" aria-hidden="true"></i>
                    </a>
                    <a href="#" title="Pinterest">
                        <i class="fa fa-pinterest" aria-hidden="true"></i>
                    </a>
                    <a href="#" title="Youtube">
                        <i class="fa fa-youtube-play" aria-hidden="true"></i>
                    </a>
                </div>
            </div>
            <div class="col-12 col-md-6 text-right">
                <p class="mb-0" style="font-size:13px; color:#888;">
                    &copy; {{ date('Y') }} Orderer. All rights reserved.
                </p>
            </div>
        </div>

    </div>
</footer>