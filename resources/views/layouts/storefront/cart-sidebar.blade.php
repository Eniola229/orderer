<div class="cart-bg-overlay"></div>
<div class="right-side-cart-area">
    <div class="cart-button">
        <a href="#" id="rightSideCart">
            <img src="{{ asset('img/core-img/bag.svg') }}" alt="">
            <span id="cart-count-sidebar">0</span>
        </a>
    </div>

    {{-- Wrapper overrides the d-flex so items stack vertically --}}
    <div style="display:flex;flex-direction:column;height:100%;overflow:hidden;">

        {{-- Items list - scrollable --}}
        <div id="cartItemsList"
             style="flex:1;overflow-y:auto;padding:20px 15px 10px;">
            <p class="p-3 text-muted">Your cart is empty.</p>
        </div>

        {{-- Summary - fixed at bottom --}}
        <div style="border-top:1px solid #ebebeb;padding:20px 25px;background:#fff;flex-shrink:0;">
            <ul class="summary-table" style="list-style:none;padding:0;margin:0 0 15px;">
                <li style="display:flex;justify-content:space-between;margin-bottom:10px;font-size:14px;font-weight:600;text-transform:uppercase;letter-spacing:0.5px;">
                    <span>Subtotal:</span>
                    <span id="cart-subtotal" style="font-family:'Ubuntu',sans-serif;font-weight:700;">₦0.00</span>
                </li>
                <li style="display:flex;justify-content:space-between;margin-bottom:10px;font-size:14px;font-weight:600;text-transform:uppercase;letter-spacing:0.5px;">
                    <span>Delivery:</span>
                    <span>At checkout</span>
                </li>
                <li style="display:flex;justify-content:space-between;font-size:14px;font-weight:600;text-transform:uppercase;letter-spacing:0.5px;">
                    <span>Total:</span>
                    <span id="cart-total" style="font-family:'Ubuntu',sans-serif;font-weight:700;">₦0.00</span>
                </li>
            </ul>
            <a href="{{ route('checkout') }}"
               class="btn essence-btn"
               style="display:block;text-align:center;width:100%;">
                Checkout
            </a>
        </div>

    </div>
</div>

<script>
const fmt = n => '₦' + Number(n).toLocaleString('en-NG', { minimumFractionDigits: 2 });

function renderCartSidebar(data) {
    const list     = document.getElementById('cartItemsList');
    const countEls = document.querySelectorAll('#cart-count, #cart-count-sidebar');

    countEls.forEach(el => el.textContent = data.count);
    document.getElementById('cart-subtotal').textContent = fmt(data.subtotal);
    document.getElementById('cart-total').textContent    = fmt(data.total);

    if (!data.items || data.items.length === 0) {
        list.innerHTML = '<p class="p-3 text-muted">Your cart is empty.</p>';
        return;
    }

    list.innerHTML = data.items.map(item => `
        <div style="display:flex;align-items:center;gap:12px;padding:12px 0;border-bottom:1px solid #f0f0f0;">
            <div style="flex-shrink:0;">
                ${item.image
                    ? `<img src="${item.image}" alt="${item.name}"
                            style="width:65px;height:65px;object-fit:cover;border-radius:8px;display:block;">`
                    : `<div style="width:65px;height:65px;background:#f0f0f0;border-radius:8px;"></div>`
                }
            </div>
            <div style="flex:1;min-width:0;">
                <p style="margin:0 0 4px;font-size:13px;font-weight:600;color:#333;
                           white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">
                    ${item.name}
                </p>
                <p style="margin:0 0 4px;font-size:12px;color:#888;">Qty: ${item.quantity}</p>
                <p style="margin:0;font-size:13px;font-weight:700;color:#2ECC71;">
                    ${fmt(item.subtotal)}
                </p>
            </div>
            <button onclick="removeFromCart('${item.product_id}')"
                    style="flex-shrink:0;background:none;border:none;
                           color:#e74c3c;font-size:20px;cursor:pointer;
                           line-height:1;padding:0 4px;"
                    title="Remove">
                &times;
            </button>
        </div>
    `).join('');
}

function loadCart() {
    fetch('{{ route('cart.sidebar') }}', {
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(r => r.json())
    .then(data => renderCartSidebar(data))
    .catch(() => {});
}

function removeFromCart(productId) {
    fetch('{{ route('cart.remove') }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'X-Requested-With': 'XMLHttpRequest',
        },
        body: JSON.stringify({ product_id: productId })
    })
    .then(r => r.json())
    .then(() => loadCart())
    .catch(() => {});
}

document.addEventListener('DOMContentLoaded', loadCart);
window.loadCart = loadCart;
</script>