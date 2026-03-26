<div class="cart-bg-overlay"></div>
<div class="right-side-cart-area">
    <div class="cart-button">
        <a href="#" id="rightSideCart">
            <img src="{{ asset('img/core-img/bag.svg') }}" alt="">
            <span id="cart-count-sidebar">0</span>
        </a>
    </div>
    <div class="cart-content d-flex">
        <div class="cart-list" id="cartItemsList">
            <p class="p-3 text-muted">Your cart is empty.</p>
        </div>
        <div class="cart-amount-summary">
            <h2>Summary</h2>
            <ul class="summary-table">
                <li><span>Subtotal:</span> <span id="cart-subtotal">$0.00</span></li>
                <li><span>Delivery:</span> <span>At checkout</span></li>
                <li><span>Total:</span> <span id="cart-total">$0.00</span></li>
            </ul>
            <div class="checkout-btn mt-100">
                <a href="{{ route('checkout') }}" class="btn essence-btn">Checkout</a>
            </div>
        </div>
    </div>
</div>
