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
                            <option value="{{ $distributor->id }}" data-lat="{{ $distributor->latitude }}" data-lng="{{ $distributor->longitude }}" data-discount="{{ $distributor->discount_percentage }}">
                                {{ $distributor->name }} ({{ $distributor->contact_person }}){{ $distributor->discount_percentage ? ' - ' . $distributor->discount_percentage . '% discount' : '' }}
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
                    <div style="display: flex; justify-content: space-between; margin-bottom: 0.25rem; font-size: 0.875rem;">
                        <span>MRP Total:</span>
                        <span id="cart-mrp-total" style="font-weight: 500; text-decoration: line-through; color: var(--text-muted);">{{ $companySettings->currency_symbol }}0.00</span>
                    </div>
                    <div style="display: flex; justify-content: space-between; margin-bottom: 0.25rem; font-size: 0.8125rem;">
                        <span>Item Discount:</span>
                        <span id="cart-item-discount" style="font-weight: 500; color: #f87171;">-{{ $companySettings->currency_symbol }}0.00</span>
                    </div>
                    <div style="display: flex; justify-content: space-between; margin-bottom: 0.25rem; font-size: 0.8125rem;">
                        <span>Dist. Discount:</span>
                        <span id="cart-dist-discount" style="font-weight: 500; color: #10b981;">-{{ $companySettings->currency_symbol }}0.00</span>
                    </div>
                    <div style="display: flex; justify-content: space-between; margin-bottom: 0.5rem; font-size: 0.9375rem; padding-top: 0.25rem; border-top: 1px dashed var(--border-subtle);">
                        <span>Subtotal:</span>
                        <span id="cart-subtotal" style="font-weight: 500;">{{ $companySettings->currency_symbol }}0.00</span>
                    </div>
                    <!-- GST Breakdown -->
                    <div id="gst-section" style="display: none;">
                        @if($companySettings->gst_type !== 'none')
                            <div style="font-size: 0.6875rem; color: var(--text-muted); margin-bottom: 0.25rem; text-transform: uppercase;">
                                GST ({{ $companySettings->gst_type === 'b2b' ? 'IGST' : 'CGST+SGST' }} {{ $companySettings->gst_percentage }}%)
                            </div>
                            @if($companySettings->gst_type === 'b2c')
                                <div style="display: flex; justify-content: space-between; font-size: 0.8125rem; margin-bottom: 0.25rem;">
                                    <span>CGST ({{ $companySettings->cgst_percentage }}%):</span>
                                    <span id="cart-cgst">{{ $companySettings->currency_symbol }}0.00</span>
                                </div>
                                <div style="display: flex; justify-content: space-between; font-size: 0.8125rem; margin-bottom: 0.25rem;">
                                    <span>SGST ({{ $companySettings->sgst_percentage }}%):</span>
                                    <span id="cart-sgst">{{ $companySettings->currency_symbol }}0.00</span>
                                </div>
                            @else
                                <div style="display: flex; justify-content: space-between; font-size: 0.8125rem; margin-bottom: 0.25rem;">
                                    <span>IGST ({{ $companySettings->igst_percentage }}%):</span>
                                    <span id="cart-igst">{{ $companySettings->currency_symbol }}0.00</span>
                                </div>
                            @endif
                            <div style="display: flex; justify-content: space-between; margin-bottom: 0.5rem; font-size: 0.9375rem; padding-top: 0.25rem; border-top: 1px dashed var(--border-subtle);">
                                <span>Total Tax:</span>
                                <span id="cart-tax" style="font-weight: 500;">{{ $companySettings->currency_symbol }}0.00</span>
                            </div>
                        @endif
                    </div>
                    <div style="display: flex; justify-content: space-between; font-size: 1.25rem; font-weight: 600; padding-top: 0.75rem; border-top: 1px solid var(--border-subtle);">
                        <span>Total:</span>
                        <span id="cart-total" style="color: #34d399;">{{ $companySettings->currency_symbol }}0.00</span>
                    </div>
                    <div style="text-align: right; font-size: 0.75rem; color: #10b981; margin-top: 0.25rem;">
                        <span id="cart-total-savings">You save {{ $companySettings->currency_symbol }}0.00</span>
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
                            <div class="product-card" data-id="{{ $product->id }}" data-name="{{ $product->name }}" data-price="{{ $product->distributor_price }}" data-mrp="{{ $product->mrp_price }}" data-unit="{{ $product->unit }}" style="background: rgba(255,255,255,0.05); border: 1px solid var(--border-subtle); border-radius: var(--radius-md); padding: 1rem; cursor: pointer; transition: all 0.2s;">
                                @if($product->image)
                                    <div style="width: 100%; height: 80px; margin-bottom: 0.5rem; display: flex; align-items: center; justify-content: center;">
                                        <img src="{{ Storage::url($product->image) }}" alt="{{ $product->name }}" style="max-width: 100%; max-height: 80px; object-fit: contain; border-radius: var(--radius-sm);">
                                    </div>
                                @else
                                    <div style="font-size: 2rem; margin-bottom: 0.5rem; text-align: center;">📦</div>
                                @endif
                                <div style="font-size: 0.875rem; font-weight: 500; margin-bottom: 0.25rem; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">{{ $product->name }}</div>
                                <div style="font-size: 0.75rem; color: var(--text-muted); margin-bottom: 0.5rem;">{{ $product->unit }}</div>
                                <div class="product-price" style="font-size: 0.9375rem; font-weight: 600; color: #34d399;">{{ $companySettings->currency_symbol }}{{ number_format($product->distributor_price, 2) }}</div>
                                <div class="product-mrp" style="font-size: 0.6875rem; color: var(--text-muted); display: none;">MRP: {{ $companySettings->currency_symbol }}{{ number_format($product->mrp_price, 2) }}</div>
                                <div class="discount-badge" style="font-size: 0.6875rem; color: #f87171; display: none;"></div>
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
                <div id="checkout-total" style="font-size: 1.5rem; font-weight: 600; color: #34d399;">{{ $companySettings->currency_symbol }}0.00</div>
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
    let companySettings = @json($companySettings);

    // Distributor selection change
    document.getElementById('distributor-select').addEventListener('change', function() {
        const distributorId = this.value;
        const selectedOption = this.options[this.selectedIndex];

        if (!distributorId) {
            document.getElementById('distributor-info').style.display = 'none';
            resetProductPrices();
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

        // Update product prices based on distributor discount
        updateProductPrices(distributorId);
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
        const distributorId = document.getElementById('distributor-select').value;

        if (!warehouseId) {
            document.querySelectorAll('.stock-status').forEach(el => {
                el.textContent = 'Select warehouse to check stock';
                el.style.color = 'var(--text-muted)';
            });
            return;
        }

        document.querySelectorAll('.stock-status').forEach(el => {
            const productId = el.dataset.productId;
            let url = '{{ route('admin.pos.check-inventory') }}?product_id=' + productId + '&warehouse_id=' + warehouseId;
            if (distributorId) {
                url += '&distributor_id=' + distributorId;
            }

            fetch(url)
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

    // Update product prices based on distributor discount
    function updateProductPrices(distributorId) {
        const distributorSelect = document.getElementById('distributor-select');
        const selectedOption = distributorSelect.options[distributorSelect.selectedIndex];
        const distributors = @json($distributors);
        const distributor = distributors.find(d => d.id == distributorId);

        document.querySelectorAll('.product-card').forEach(card => {
            const basePrice = parseFloat(card.dataset.price);
            const mrpPrice = parseFloat(card.dataset.mrp);
            const priceEl = card.querySelector('.product-price');
            const mrpEl = card.querySelector('.product-mrp');
            const discountBadge = card.querySelector('.discount-badge');

            if (distributor && distributor.discount_percentage && distributor.discount_percentage > 0) {
                const discountedPrice = mrpPrice - (mrpPrice * (distributor.discount_percentage / 100));
                priceEl.textContent = companySettings.currency_symbol + discountedPrice.toFixed(2);
                mrpEl.style.display = 'block';
                discountBadge.style.display = 'block';
                discountBadge.textContent = distributor.discount_percentage + '% off MRP';
                card.dataset.price = discountedPrice;
            } else {
                priceEl.textContent = companySettings.currency_symbol + basePrice.toFixed(2);
                mrpEl.style.display = 'none';
                discountBadge.style.display = 'none';
                card.dataset.price = basePrice;
            }
        });
    }

    // Reset product prices to default
    function resetProductPrices() {
        document.querySelectorAll('.product-card').forEach(card => {
            const basePrice = parseFloat(card.dataset.price);
            const priceEl = card.querySelector('.product-price');
            const mrpEl = card.querySelector('.product-mrp');
            const discountBadge = card.querySelector('.discount-badge');

            priceEl.textContent = companySettings.currency_symbol + basePrice.toFixed(2);
            mrpEl.style.display = 'none';
            discountBadge.style.display = 'none';
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
            const productId = parseInt(this.dataset.id);
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
        const distributorSelect = document.getElementById('distributor-select');
        const distributorId = distributorSelect.value;
        const distributor = distributors.find(d => d.id == distributorId);
        const distributorDiscount = distributor ? (distributor.discount_percentage || 0) : 0;

        if (cart.length === 0) {
            cartContainer.style.display = 'none';
            emptyCart.style.display = 'block';
        } else {
            cartContainer.style.display = 'block';
            emptyCart.style.display = 'none';

            cartContainer.innerHTML = cart.map((item, index) => {
                const product = products.find(p => p.id == item.product_id);
                const mrpPrice = product ? product.mrp_price : item.unit_price;
                
                // Calculate item-level discount from discount_percent
                const itemSubtotal = item.quantity * mrpPrice;
                const itemDiscountPercent = item.discount_percent || 0;
                const itemDiscountAmount = itemSubtotal * (itemDiscountPercent / 100);
                const finalPrice = itemSubtotal - itemDiscountAmount;
                
                // Show distributor discount info
                const hasDistributorDiscount = distributorDiscount > 0;
                const distributorSavings = hasDistributorDiscount ? (mrpPrice - item.unit_price) * item.quantity : 0;
                
                return `
                <div class="cart-item" style="grid-template-columns: 2fr 60px 90px 100px 30px; gap: 0.5rem;">
                    <div style="min-width: 0;">
                        <div style="font-weight: 500; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">${item.name}</div>
                        <div style="font-size: 0.7rem; color: var(--text-muted);">
                            MRP: ${companySettings.currency_symbol}${mrpPrice.toFixed(2)} 
                            ${itemDiscountPercent > 0 ? `<span style="color: #f87171;">(${itemDiscountPercent}% off)</span>` : ''}
                        </div>
                        ${hasDistributorDiscount ? `
                        <div style="font-size: 0.7rem; color: #10b981;">
                            Dist. Price: ${companySettings.currency_symbol}${item.unit_price.toFixed(2)} 
                            <span style="color: #f87171;">(Save ${companySettings.currency_symbol}${distributorSavings.toFixed(2)})</span>
                        </div>
                        ` : `
                        <div style="font-size: 0.7rem; color: #fbbf24;">
                            No distributor discount
                        </div>
                        `}
                    </div>
                    <input type="number" class="qty-input" value="${item.quantity}" min="0.01" step="0.01" onchange="updateQuantity(${index}, this.value)" style="width: 55px; padding: 0.25rem; font-size: 0.8rem;">
                    <div style="text-align: right; font-size: 0.8rem;">
                        ${itemDiscountAmount > 0 ? `
                            <div style="text-decoration: line-through; color: var(--text-muted);">${companySettings.currency_symbol}${itemSubtotal.toFixed(2)}</div>
                            <div style="color: #f87171; font-size: 0.7rem;">-${companySettings.currency_symbol}${itemDiscountAmount.toFixed(2)}</div>
                        ` : ''}
                    </div>
                    <div style="font-weight: 600; min-width: 80px; text-align: right; color: #34d399; font-size: 0.9rem;">
                        ${companySettings.currency_symbol}${finalPrice.toFixed(2)}
                    </div>
                    <button onclick="removeItem(${index})" style="background: none; border: none; color: #f87171; cursor: pointer; font-size: 1rem; padding: 0;">🗑️</button>
                </div>
                `;
            }).join('');
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

    // Calculate totals with GST
    function calculateTotals() {
        const distributorSelect = document.getElementById('distributor-select');
        const distributorId = distributorSelect.value;
        const distributor = distributors.find(d => d.id == distributorId);
        const distributorDiscount = distributor ? (distributor.discount_percentage || 0) : 0;

        let mrpTotal = 0;
        let itemDiscountTotal = 0;
        let distDiscountTotal = 0;
        let subtotal = 0;

        cart.forEach(item => {
            const product = products.find(p => p.id == item.product_id);
            const mrpPrice = product ? product.mrp_price : item.unit_price;
            const distPrice = item.unit_price;
            
            const itemMrpSubtotal = item.quantity * mrpPrice;
            const itemDistSubtotal = item.quantity * distPrice;
            const itemDiscount = itemMrpSubtotal * ((item.discount_percent || 0) / 100);
            const itemDistDiscount = (mrpPrice - distPrice) * item.quantity;
            
            mrpTotal += itemMrpSubtotal;
            itemDiscountTotal += itemDiscount;
            distDiscountTotal += itemDistDiscount;
            subtotal += itemDistSubtotal - itemDiscount;
        });

        const taxableAmount = subtotal;
        let cgst = 0, sgst = 0, igst = 0, totalTax = 0;

        // Calculate GST based on company settings
        if (companySettings && companySettings.gst_type !== 'none' && companySettings.gst_percentage > 0) {
            if (companySettings.gst_type === 'b2b') {
                // B2B: IGST
                igst = taxableAmount * (companySettings.igst_percentage / 100);
                totalTax = igst;
            } else {
                // B2C: CGST + SGST
                cgst = taxableAmount * (companySettings.cgst_percentage / 100);
                sgst = taxableAmount * (companySettings.sgst_percentage / 100);
                totalTax = cgst + sgst;
            }
        }

        const total = taxableAmount + totalTax;
        const totalSavings = itemDiscountTotal + distDiscountTotal;

        // Update all summary fields
        const mrpTotalEl = document.getElementById('cart-mrp-total');
        const itemDiscountEl = document.getElementById('cart-item-discount');
        const distDiscountEl = document.getElementById('cart-dist-discount');
        const subtotalEl = document.getElementById('cart-subtotal');
        const totalEl = document.getElementById('cart-total');
        const checkoutTotalEl = document.getElementById('checkout-total');
        const totalSavingsEl = document.getElementById('cart-total-savings');

        if (mrpTotalEl) mrpTotalEl.textContent = companySettings.currency_symbol + mrpTotal.toFixed(2);
        if (itemDiscountEl) itemDiscountEl.textContent = '-' + companySettings.currency_symbol + itemDiscountTotal.toFixed(2);
        if (distDiscountEl) distDiscountEl.textContent = '-' + companySettings.currency_symbol + distDiscountTotal.toFixed(2);
        if (subtotalEl) subtotalEl.textContent = companySettings.currency_symbol + subtotal.toFixed(2);
        if (totalEl) totalEl.textContent = companySettings.currency_symbol + total.toFixed(2);
        if (checkoutTotalEl) checkoutTotalEl.textContent = companySettings.currency_symbol + total.toFixed(2);
        if (totalSavingsEl) totalSavingsEl.textContent = 'You save ' + companySettings.currency_symbol + totalSavings.toFixed(2);

        // Show/hide GST section and update values
        const gstSection = document.getElementById('gst-section');
        if (companySettings && companySettings.gst_type !== 'none' && companySettings.gst_percentage > 0) {
            gstSection.style.display = 'block';
            const taxEl = document.getElementById('cart-tax');
            if (taxEl) taxEl.textContent = companySettings.currency_symbol + totalTax.toFixed(2);

            if (companySettings.gst_type === 'b2c') {
                const cgstEl = document.getElementById('cart-cgst');
                const sgstEl = document.getElementById('cart-sgst');
                if (cgstEl) cgstEl.textContent = companySettings.currency_symbol + cgst.toFixed(2);
                if (sgstEl) sgstEl.textContent = companySettings.currency_symbol + sgst.toFixed(2);
            } else {
                const igstEl = document.getElementById('cart-igst');
                if (igstEl) igstEl.textContent = companySettings.currency_symbol + igst.toFixed(2);
            }
        } else {
            gstSection.style.display = 'none';
        }
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
