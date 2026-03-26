<footer class="footer_area clearfix">
    <div class="container">
        <div class="row">
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
                        </ul>
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-3">
                <div class="single_widget_area mb-30">
                    <h6 style="font-weight:700;margin-bottom:16px;color:#333;">Sellers</h6>
                    <ul class="footer_widget_menu">
                        <li><a href="{{ route('seller.register') }}">Start Selling</a></li>
                        <li><a href="{{ route('seller.login') }}">Seller Login</a></li>
                        <li><a href="#">Seller Guide</a></li>
                        <li><a href="#">Fees &amp; Commission</a></li>
                    </ul>
                </div>
            </div>
            <div class="col-12 col-md-3">
                <div class="single_widget_area mb-30">
                    <h6 style="font-weight:700;margin-bottom:16px;color:#333;">Support</h6>
                    <ul class="footer_widget_menu">
                        <li><a href="#">Order Status</a></li>
                        <li><a href="#">Payment Options</a></li>
                        <li><a href="{{ route('rider.booking') }}">Shipping &amp; Delivery</a></li>
                        <li><a href="#">Privacy Policy</a></li>
                        <li><a href="#">Terms of Use</a></li>
                    </ul>
                </div>
            </div>
            <div class="col-12 col-md-3">
                <div class="single_widget_area">
                    <div class="footer_heading mb-30">
                        <h6>Subscribe to Newsletter</h6>
                    </div>
                    <div class="subscribtion_form">
                        <form action="{{ route('newsletter.subscribe') }}" method="POST">
                            @csrf
                            <input type="email" name="email" class="mail" placeholder="Your email here" required>
                            <button type="submit" class="submit">
                                <i class="fa fa-long-arrow-right"></i>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <div class="row align-items-center mt-30">
            <div class="col-12 col-md-6">
                <div class="footer_social_area">
                    <a href="#" title="Facebook"><i class="fa fa-facebook"></i></a>
                    <a href="#" title="Instagram"><i class="fa fa-instagram"></i></a>
                    <a href="#" title="Twitter"><i class="fa fa-twitter"></i></a>
                    <a href="#" title="Pinterest"><i class="fa fa-pinterest"></i></a>
                    <a href="#" title="Youtube"><i class="fa fa-youtube-play"></i></a>
                </div>
            </div>
            <div class="col-12 col-md-6 text-right">
                <p class="mb-0" style="font-size:13px;color:#888;">
                    &copy; {{ date('Y') }} Orderer. All rights reserved.
                </p>
            </div>
        </div>
    </div>
</footer>
