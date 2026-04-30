@extends('layouts.admin')

@section('title', 'Point of Sale')

@section('content')
    <div class="page-header">
        <h1>
            <small>Process Sales</small>
            Point of Sale
        </h1>
        <a href="{{ route('admin.pos.history') }}" class="btn-secondary" style="text-decoration: none;">📜 Sales History</a>
    </div>

    <div class="pos-layout" style="display: grid; grid-template-columns: 1fr 380px; gap: 1.5rem;">
        <!-- Left Panel - Cart -->
        <div class="pos-cart">
            <div class="glass-card" style="height: 100%;">
                <div class="card-head">
                    <h2>🛒 Sale Cart</h2>
                </div>

                <!-- Distributor Selection -->
                <div style="padding: 1rem 1.25rem; border-bottom: 1px solid var(--border-subtle);">
                    <label class="detail-label" style="display: block; margin-bottom: 0.5rem;">Select Distributor *</label>
                    <select id="distributor-select" class="filter-select" style="width: 100%; font-size: 0.9375rem;">
                        <option value="">Choose a distributor...</option>
                        @foreach($distributors as $distributor)
                            <option value="{{ $distributor->id }}" data-lat="{{ $distributor->latitude }}" data-lng="{{ $distributor->longitude }}">
                                {{ $distributor->name }} ({{ $distributor->contact_person }})
                            </option>
                        @endforeach
                    </select>
                    <div id="distributor-info" style="margin-top: 0.75rem; font-size: 0.8125rem; color: var(--text-muted); display: none;">
                        📍 <span id="distributor-location"></span>
                    </div>
                </div>

                <!-- Warehouse Selection -->
                <div style="padding: 1rem 1.25rem; border-bottom: 1px solid var(--border-subtle);">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.5rem;">
                        <label class="detail-label">Warehouse *</label>
                        <span id="nearest-badge" style="font-size: 0.6875rem; color: #10b981; display: none;">✓ Nearest auto-selected</span>
                    </div>
                    <select id="warehouse-select" class="filter-select" style="width: 100%; font-size: 0.9375rem;">
                        <option value="">Select warehouse...</option>
                        @foreach($warehouses as $warehouse)
                            <option value="{{ $warehouse->id }}" data-lat="{{ $warehouse->latitude }}" data-lng="{{ $warehouse->longitude }}">
                                {{ $warehouse->name }} ({{ $warehouse->code }}) - {{ $warehouse->city }}
                            </option>
                        @endforeach
                    </select>
                    <div id="warehouse-info" style="margin-top: 0.75rem; font-size: 0.8125rem; color: var(--text-muted); display: none;">
                        📦 <span id="warehouse-address"></span>
                        <span id="distance-info" style="color: #10b981; margin-left: 0.5rem;"></span>
                    </div>
                </div>

                <!-- Cart Items -->
                <div style="padding: 1rem 1.25rem; flex: 1; overflow-y: auto;">
                    <div id="empty-cart" style="text-align: center; padding: 3rem 1rem; color: var(--text-muted);">
                        <div style="font-size: 3rem; margin-bottom: 1rem;">🛒</div>
                        <p>Your cart is empty</p>
                        <p style="font-size: 0.8125rem; margin-top: 0.5rem;">Select products from the right panel</p>
                    </div>
                    <div id="cart-items" style="display: none;"></div>
                </div>

                <!-- Cart Summary -->
                <div style="padding: 1rem 1.25rem; border-top: 1px solid var(--border-subtle); background: rgba(0,0,0,0.2);">
                    <div style="display: flex; justify-content: space-between; margin-bottom: 0.5rem; font-size: 0.9375rem;">
                        <span>Subtotal:</span>
                        <span id="cart-subtotal" style="font-weight: 500;">₹0.00</span>
                    </div>
                    <div style="display: flex; justify-content: space-between; margin-bottom: 0.5rem; font-size: 0.9375rem;">
                        <span>Tax:</span>
                        <span id="cart-tax" style="font-weight: 500;">₹0.00</span>
                    </div>
                    <div style="display: flex; justify-content: space-between; margin-bottom: 0.5rem; font-size: 0.9375rem;">
                        <span>Discount:</span>
                        <span id="cart-discount" style="font-weight: 500; color: #f87171;">-₹0.00</span>
                    </div>
                    <div style="display: flex; justify-content: space-between; font-size: 1.25rem; font-weight: 600; padding-top: 0.75rem; border-top: 1px solid var(--border-subtle);">
                        <span>Total:</span>
                        <span id="cart-total" style="color: #34d399;">₹0.00</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Panel - Products -->
        <div class="pos-products">
            <div class="glass-card" style="height: 100%; display: flex; flex-direction: column;">
                <div class="card-head">
                    <h2>📦 Products</h2>
                </div>

                <!-- Product Search -->
                <div style="padding: 1rem 1.25rem; border-bottom: 1px solid var(--border-subtle);">
                    <div class="search-bar" style="max-width: 100%;">
                        <span class="search-icon">🔍</span>
                        <input type="search" id="product-search" placeholder="Search products..." style="background: transparent;">
                    </div>
                </div>

                <!-- Products Grid -->
                <div style="flex: 1; overflow-y: auto; padding: 1rem;">
                    <div id="products-grid" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(150px, 1fr)); gap: 0.75rem;">
                        @foreach($products as $product)
                            <div class="product-card" data-id="{{ $product->id }}" data-name="{{ $product->name }}" data-price="{{ $product->price }}" data-unit="{{ $product->unit }}" style="background: rgba(255,255,255,0.05); border: 1px solid var(--border-subtle); border-radius: var(--radius-md); padding: 1rem; cursor: pointer; transition: all 0.2s;">
                                <div style="font-size: 2rem; margin-bottom: 0.5rem; text-align: center;">📦</div>
                                <div style="font-size: 0.875rem; font-weight: 500; margin-bottom: 0.25rem; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">{{ $product->name }}</div>
                                <div style="font-size: 0.75rem; color: var(--text-muted); margin-bottom: 0.5rem;">{{ $product->unit }}</div>
                                <div style="font-size: 0.9375rem; font-weight: 600; color: #34d399;">₹{{ number_format($product->price, 2) }}</div>
                                <div class="stock-status" data-product-id="{{ $product->id }}" style="font-size: 0.6875rem; margin-top: 0.5rem; color: var(--text-muted);">
                                    Select warehouse to check stock
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Checkout Bar -->
    <div style="position: fixed; bottom: 0; left: var(--sidebar-w); right: 0; background: var(--bg-card); border-top: 1px solid var(--border-subtle); padding: 1rem 1.5rem; display: flex; justify-content: space-between; align-items: center; z-index: 100;">
        <div style="display: flex; gap: 1rem; align-items: center;">
            <div>
                <label style="font-size: 0.6875rem; color: var(--text-muted); display: block; margin-bottom: 0.25rem;">Payment Method</label>
                <select id="payment-method" class="filter-select">
                    <option value="cash">💵 Cash</option>
                    <option value="credit">💳 Credit</option>
                    <option value="upi">📱 UPI</option>
                    <option value="bank_transfer">🏦 Bank Transfer</option>
                    <option value="cheque">📝 Cheque</option>
                </select>
            </div>
            <div>
                <label style="font-size: 0.6875rem; color: var(--text-muted); display: block; margin-bottom: 0.25rem;">Notes</label>
                <input type="text" id="sale-notes" placeholder="Optional notes..." class="filter-select" style="min-width: 200px;">
            </div>
        </div>
        <div style="display: flex; gap: 1rem; align-items: center;">
            <div style="text-align: right;">
                <div style="font-size: 0.75rem; color: var(--text-muted);">Total Amount</div>
                <div id="checkout-total" style="font-size: 1.5rem; font-weight: 600; color: #34d399;">₹0.00</div>
            </div>
            <button id="checkout-btn" class="btn-primary" style="padding: 0.875rem 2rem; font-size: 1rem;" disabled>
                ✅ Complete Sale
            </button>
        </div>
    </div>
@endsection

@push('styles')
<style>
    .product-card:hover {
        background: rgba(255,255,255,0.1) !important;
        border-color: var(--accent-primary) !important;
        transform: translateY(-2px);
    }

    .cart-item {
        display: grid;
        grid-template-columns: 1fr auto auto auto auto;
        gap: 0.75rem;
        align-items: center;
        padding: 0.75rem;
        background: rgba(255,255,255,0.03);
        border-radius: var(--radius-md);
        margin-bottom: 0.5rem;
    }

    .qty-input {
        width: 60px;
        padding: 0.375rem;
        background: rgba(0,0,0,0.3);
        border: 1px solid var(--border-subtle);
        border-radius: var(--radius-sm);
        color: var(--text-primary);
        text-align: center;
        font-size: 0.875rem;
    }

    .price-input {
        width: 80px;
        padding: 0.375rem;
        background: rgba(0,0,0,0.3);
        border: 1px solid var(--border-subtle);
        border-radius: var(--radius-sm);
        color: var(--text-primary);
        text-align: right;
        font-size: 0.875rem;
    }

    .pos-layout {
        margin-bottom: 80px;
    }
</style>
@endpush

@push('scripts')
<script>
    let cart = [];
    let warehouses = @json($warehouses);
    let products = @json($products);

    // Distributor selection change
    document.getElementById('distributor-select').addEventListener('change', function() {
        const distributorId = this.value;
        const selectedOption = this.options[this.selectedIndex];

        if (!distributorId) {
            document.getElementById('distributor-info').style.display = 'none';
            return;
        }

        // Show distributor info
        const lat = selectedOption.dataset.lat;
        const lng = selectedOption.dataset.lng;
        document.getElementById('distributor-location').textContent = lat && lng ? 'Location available' : 'No location data';
        document.getElementById('distributor-info').style.display = 'block';

        // Find nearest warehouse
        if (lat && lng) {
            fetchNearestWarehouse(distributorId);
        }

        updateCheckoutButton();
    });

    // Warehouse selection change
    document.getElementById('warehouse-select').addEventListener('change', function() {
        const warehouseId = this.value;
        const selectedOption = this.options[this.selectedIndex];

        if (!warehouseId) {
            document.getElementById('warehouse-info').style.display = 'none';
            return;
        }

        // Show warehouse info
        document.getElementById('warehouse-address').textContent = selectedOption.text;
        document.getElementById('warehouse-info').style.display = 'block';

        // Update stock status for all products
        updateAllStockStatuses();
        updateCheckoutButton();
    });

    // Fetch nearest warehouse
    function fetchNearestWarehouse(distributorId) {
        fetch('{{ route('admin.pos.nearest-warehouse') }}?distributor_id=' + distributorId)
            .then(r => r.json())
            .then(data => {
                if (data.warehouse) {
                    document.getElementById('warehouse-select').value = data.warehouse.id;
                    document.getElementById('nearest-badge').style.display = 'inline';
                    document.getElementById('warehouse-address').textContent = data.warehouse.name + ' (' + data.warehouse.code + ')';
                    document.getElementById('warehouse-info').style.display = 'block';

                    if (data.distance_formatted) {
                        document.getElementById('distance-info').textContent = '📏 ' + data.distance_formatted + ' away';
                    } else {
                        document.getElementById('distance-info').textContent = '';
                    }

                    updateAllStockStatuses();
                    updateCheckoutButton();
                }
            });
    }

    // Update stock status for all products
    function updateAllStockStatuses() {
        const warehouseId = document.getElementById('warehouse-select').value;

        if (!warehouseId) {
            document.querySelectorAll('.stock-status').forEach(el => {
                el.textContent = 'Select warehouse to check stock';
                el.style.color = 'var(--text-muted)';
            });
            return;
        }

        document.querySelectorAll('.stock-status').forEach(el => {
            const productId = el.dataset.productId;

            fetch('{{ route('admin.pos.check-inventory') }}?product_id=' + productId + '&warehouse_id=' + warehouseId)
                .then(r => r.json())
                .then(data => {
                    if (data.available_for_sale > 0) {
                        el.textContent = 'Stock: ' + data.available_for_sale;
                        el.style.color = '#34d399';
                    } else {
                        el.textContent = 'Out of stock';
                        el.style.color = '#f87171';
                    }
                });
        });
    }

    // Product search
    document.getElementById('product-search').addEventListener('input', function() {
        const query = this.value.toLowerCase();

        document.querySelectorAll('.product-card').forEach(card => {
            const name = card.dataset.name.toLowerCase();
            card.style.display = name.includes(query) ? 'block' : 'none';
        });
    });

    // Add product to cart
    document.querySelectorAll('.product-card').forEach(card => {
        card.addEventListener('click', function() {
            const productId = this.dataset.id;
            const productName = this.dataset.name;
            const productPrice = parseFloat(this.dataset.price);

            const existingItem = cart.find(item => item.product_id === productId);

            if (existingItem) {
                existingItem.quantity += 1;
            } else {
                cart.push({
                    product_id: productId,
                    name: productName,
                    unit_price: productPrice,
                    quantity: 1,
                    discount_percent: 0,
                    tax_percent: 0,
                });
            }

            renderCart();
            updateCheckoutButton();
        });
    });

    // Render cart
    function renderCart() {
        const cartContainer = document.getElementById('cart-items');
        const emptyCart = document.getElementById('empty-cart');

        if (cart.length === 0) {
            cartContainer.style.display = 'none';
            emptyCart.style.display = 'block';
        } else {
            cartContainer.style.display = 'block';
            emptyCart.style.display = 'none';

            cartContainer.innerHTML = cart.map((item, index) => `
                <div class="cart-item">
                    <div>
                        <div style="font-weight: 500;">${item.name}</div>
                        <div style="font-size: 0.75rem; color: var(--text-muted);">₹${item.unit_price.toFixed(2)} each</div>
                    </div>
                    <input type="number" class="qty-input" value="${item.quantity}" min="1" onchange="updateQuantity(${index}, this.value)">
                    <input type="number" class="price-input" value="${item.unit_price}" min="0" step="0.01" onchange="updatePrice(${index}, this.value)">
                    <div style="font-weight: 500; min-width: 80px; text-align: right;">₹${(item.quantity * item.unit_price).toFixed(2)}</div>
                    <button onclick="removeItem(${index})" style="background: none; border: none; color: #f87171; cursor: pointer; font-size: 1.25rem;">🗑️</button>
                </div>
            `).join('');
        }

        calculateTotals();
    }

    // Update quantity
    function updateQuantity(index, value) {
        cart[index].quantity = parseInt(value) || 1;
        renderCart();
    }

    // Update price
    function updatePrice(index, value) {
        cart[index].unit_price = parseFloat(value) || 0;
        renderCart();
    }

    // Remove item
    function removeItem(index) {
        cart.splice(index, 1);
        renderCart();
        updateCheckoutButton();
    }

    // Calculate totals
    function calculateTotals() {
        let subtotal = 0;
        let tax = 0;
        let discount = 0;

        cart.forEach(item => {
            const itemSubtotal = item.quantity * item.unit_price;
            const itemDiscount = itemSubtotal * (item.discount_percent / 100);
            const itemTaxable = itemSubtotal - itemDiscount;
            const itemTax = itemTaxable * (item.tax_percent / 100);

            subtotal += itemSubtotal;
            discount += itemDiscount;
            tax += itemTax;
        });

        const total = subtotal - discount + tax;

        document.getElementById('cart-subtotal').textContent = '₹' + subtotal.toFixed(2);
        document.getElementById('cart-tax').textContent = '₹' + tax.toFixed(2);
        document.getElementById('cart-discount').textContent = '-₹' + discount.toFixed(2);
        document.getElementById('cart-total').textContent = '₹' + total.toFixed(2);
        document.getElementById('checkout-total').textContent = '₹' + total.toFixed(2);
    }

    // Update checkout button state
    function updateCheckoutButton() {
        const distributorId = document.getElementById('distributor-select').value;
        const warehouseId = document.getElementById('warehouse-select').value;
        const hasItems = cart.length > 0;

        document.getElementById('checkout-btn').disabled = !distributorId || !warehouseId || !hasItems;
    }

    // Checkout
    document.getElementById('checkout-btn').addEventListener('click', function() {
        if (this.disabled) return;

        const data = {
            distributor_id: document.getElementById('distributor-select').value,
            warehouse_id: document.getElementById('warehouse-select').value,
            items: cart.map(item => ({
                product_id: item.product_id,
                quantity: item.quantity,
                unit_price: item.unit_price,
                discount_percent: item.discount_percent,
                tax_percent: item.tax_percent,
            })),
            payment_method: document.getElementById('payment-method').value,
            notes: document.getElementById('sale-notes').value,
        };

        fetch('{{ route('admin.pos.store') }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
            },
            body: JSON.stringify(data),
        })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                window.location.href = data.redirect_url;
            } else {
                alert(data.message || 'Error processing sale');
            }
        })
        .catch(e => {
            alert('Error processing sale: ' + e.message);
        });
    });
</script>
@endpush
