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

    <div class="pos-layout">
        <!-- Left Panel - Cart -->
        <div class="pos-cart">
            <div class="glass-card pos-cart-card">
                <div class="card-head">
                    <h2>🛒 Sale Cart</h2>
                </div>

                <!-- Distributor Selection -->
                <div class="pos-section">
                    <label class="detail-label pos-label">Select Distributor *</label>
                    <select id="distributor-select" class="filter-select pos-select">
                        <option value="">Choose a distributor...</option>
                        @foreach($distributors as $distributor)
                            <option value="{{ $distributor->id }}" data-lat="{{ $distributor->latitude }}" data-lng="{{ $distributor->longitude }}" data-discount="{{ $distributor->discount_percentage }}">
                                {{ $distributor->name }} ({{ $distributor->contact_person }}){{ $distributor->discount_percentage ? ' - ' . $distributor->discount_percentage . '% discount' : '' }}
                            </option>
                        @endforeach
                    </select>
                    <div id="distributor-info" class="pos-info-message" style="display: none;">
                        📍 <span id="distributor-location"></span>
                    </div>
                </div>

                <!-- Warehouse Selection -->
                <div class="pos-section">
                    <div class="pos-section-header">
                        <label class="detail-label">Warehouse *</label>
                        <span id="nearest-badge" class="pos-badge-success" style="display: none;">✓ Nearest auto-selected</span>
                    </div>
                    <select id="warehouse-select" class="filter-select pos-select">
                        <option value="">Select warehouse...</option>
                        @foreach($warehouses as $warehouse)
                            <option value="{{ $warehouse->id }}" data-lat="{{ $warehouse->latitude }}" data-lng="{{ $warehouse->longitude }}">
                                {{ $warehouse->name }} ({{ $warehouse->code }}) - {{ $warehouse->city }}
                            </option>
                        @endforeach
                    </select>
                    <div id="warehouse-info" class="pos-info-message" style="display: none;">
                        📦 <span id="warehouse-address"></span>
                        <span id="distance-info" class="pos-success-text" style="margin-left: 0.5rem;"></span>
                    </div>
                </div>

                <!-- Cart Items -->
                <div class="pos-cart-items">
                    <div id="empty-cart" class="pos-empty-cart">
                        <div class="pos-empty-icon">🛒</div>
                        <p>Your cart is empty</p>
                        <p class="pos-empty-subtitle">Select products from the right panel</p>
                    </div>
                    <div id="cart-items" style="display: none;"></div>
                </div>

                <!-- Cart Summary -->
                <div class="pos-cart-summary">
                    <div class="pos-summary-row">
                        <span>MRP Total:</span>
                        <span id="cart-mrp-total" class="pos-mrp-total">{{ $companySettings->currency_symbol }}0.00</span>
                    </div>
                    <div class="pos-summary-row pos-summary-small">
                        <span>Item Discount:</span>
                        <span id="cart-item-discount" class="pos-discount-amount">-{{ $companySettings->currency_symbol }}0.00</span>
                    </div>
                    <div class="pos-summary-row pos-summary-small">
                        <span>Dist. Discount:</span>
                        <span id="cart-dist-discount" class="pos-savings-amount">-{{ $companySettings->currency_symbol }}0.00</span>
                    </div>
                    <div class="pos-summary-row pos-subtotal-row">
                        <span>Subtotal:</span>
                        <span id="cart-subtotal" class="pos-subtotal">{{ $companySettings->currency_symbol }}0.00</span>
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
                            <div class="product-card" 
                                 data-id="{{ $product->id }}" 
                                 data-name="{{ $product->name }}" 
                                 data-price="{{ $product->distributor_price }}" 
                                 data-mrp="{{ $product->mrp_price }}" 
                                 data-unit="{{ $product->unit }}"
                                 style="background: rgba(255,255,255,0.05); border: 1px solid var(--border-subtle); border-radius: var(--radius-md); padding: 1rem; cursor: pointer; transition: all 0.2s;">
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
    
    .product-card {
        cursor: pointer;
        user-select: none;
        position: relative;
        z-index: 1;
    }
    
    .product-card:hover {
        z-index: 10;
    }
    
    .product-card:active {
        transform: scale(0.98);
    }
    
    #products-grid {
        position: relative;
    }
</style>
@endpush

@push('scripts')
<script>
    // Global variables
    let cart = [];
    let warehouses = @json($warehouses);
    let products = @json($products);
    let distributors = @json($distributors);
    let companySettings = @json($companySettings);
    let originalStocks = {}; // Track original stock by product ID

    // Wait for DOM to be fully loaded
    document.addEventListener('DOMContentLoaded', function() {
        console.log('POS System Initialized');
        console.log('Products loaded:', products.length);
        console.log('Products data:', products);
        
        if (products.length === 0) {
            alert('WARNING: No products loaded!');
        }
        
        initializeEventListeners();
        initializeProductClickHandlers();
    });
    
    function initializeProductClickHandlers() {
        const productsGrid = document.getElementById('products-grid');
        
        if (!productsGrid) {
            console.error('Products grid not found');
            alert('ERROR: Products grid not found in DOM');
            return;
        }
        
        const productCards = productsGrid.querySelectorAll('.product-card');
        console.log('Found', productCards.length, 'product cards');
        
        if (productCards.length === 0) {
            alert('ERROR: No product cards found');
            return;
        }
        
        // Attach click handler to each card directly
        productCards.forEach((card, index) => {
            card.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                
                const productId = parseInt(this.dataset.id);
                const productName = this.dataset.name;
                const productPrice = parseFloat(this.dataset.price);
                
                console.log('Card', index, 'clicked:', {productId, productName, productPrice});
                
                if (!productId) {
                    alert('Invalid product ID: ' + this.dataset.id);
                    return;
                }
                
                if (isNaN(productPrice)) {
                    alert('Invalid product price: ' + this.dataset.price);
                    return;
                }
                
                const warehouseId = document.getElementById('warehouse-select').value;
                if (!warehouseId) {
                    alert('Please select a warehouse first');
                    return;
                }
                
                checkStockAndAddToCart(productId, productName, productPrice, this);
            });
        });
        
        console.log('Click handlers attached to', productCards.length, 'products');
    }
    
    function initializeEventListeners() {
        // Distributor selection change
        document.getElementById('distributor-select').addEventListener('change', function() {
            const distributorId = this.value;
            const selectedOption = this.options[this.selectedIndex];

            if (!distributorId) {
                document.getElementById('distributor-info').style.display = 'none';
                resetProductPrices();
                updateCheckoutButton();
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
            renderCart(); // Re-render cart to update discounts
        });

        // Warehouse selection change
        document.getElementById('warehouse-select').addEventListener('change', function() {
            const warehouseId = this.value;
            const selectedOption = this.options[this.selectedIndex];

            if (!warehouseId) {
                document.getElementById('warehouse-info').style.display = 'none';
                updateCheckoutButton();
                return;
            }

            // Show warehouse info
            document.getElementById('warehouse-address').textContent = selectedOption.text;
            document.getElementById('warehouse-info').style.display = 'block';

            // Update stock status for all products
            updateAllStockStatuses().then(() => {
                // After all stocks fetched, update displays with cart quantities
                updateAllProductStockDisplays();
            });
            updateCheckoutButton();
        });

        // Product search
        document.getElementById('product-search').addEventListener('input', function() {
            const query = this.value.toLowerCase();

            document.querySelectorAll('.product-card').forEach(card => {
                const name = card.dataset.name.toLowerCase();
                card.style.display = name.includes(query) ? 'block' : 'none';
            });
        });

        // Checkout button
        document.getElementById('checkout-btn').addEventListener('click', function() {
            if (this.disabled) return;
            processCheckout();
        });
    }
    
    function checkStockAndAddToCart(productId, productName, productPrice, productCard) {
        const warehouseId = document.getElementById('warehouse-select').value;
        const distributorId = document.getElementById('distributor-select').value;
        
        console.log('Adding to cart - Product:', productId, 'Price:', productPrice);
        
        // Add to cart immediately for better UX
        addToCart(productId, productName, productPrice);
        
        // Visual feedback
        productCard.style.transform = 'scale(0.95)';
        setTimeout(() => {
            productCard.style.transform = '';
        }, 150);
        
        // Check stock in background (non-blocking)
        let url = '{{ route('admin.pos.check-inventory') }}?product_id=' + productId + '&warehouse_id=' + warehouseId;
        if (distributorId) {
            url += '&distributor_id=' + distributorId;
        }
        
        fetch(url)
            .then(r => r.json())
            .then(data => {
                console.log('Stock check response:', data);
                // Update stock display for this product with cart logic
                const stockEl = productCard.querySelector('.stock-status');
                if (stockEl && data.available_for_sale !== undefined) {
                    originalStocks[productId] = data.available_for_sale;
                    
                    const cartItem = cart.find(item => item.product_id === productId);
                    const cartQty = cartItem ? cartItem.quantity : 0;
                    const displayStock = Math.max(0, data.available_for_sale - cartQty);
                    
                    console.log('DISPLAY STOCK:', displayStock, 'CART:', cartQty);
                    
                    if (displayStock > 0) {
                        stockEl.textContent = displayStock + ' left • 🛒 ' + cartQty + ' in cart';
                        stockEl.style.color = '#34d399';
                    } else {
                        stockEl.textContent = 'Out of stock';
                        stockEl.style.color = '#f87171';
                        if (data.available_for_sale === 0) {
                            alert('Warning: Product is out of stock at this warehouse!');
                        }
                    }
                }
            })
            .catch(error => {
                console.error('Stock check failed (non-critical):', error);
                // Silently ignore - product already added to cart
            });
    }
    
    function addToCart(productId, productName, productPrice) {
        console.log('Adding to cart:', {productId, productName, productPrice});
        
        const existingItem = cart.find(item => item.product_id === productId);
        
        if (existingItem) {
            existingItem.quantity += 1;
            console.log('Updated quantity for', productName, 'to', existingItem.quantity);
        } else {
            cart.push({
                product_id: productId,
                name: productName,
                unit_price: productPrice,
                quantity: 1,
                discount_percent: 0,
                tax_percent: 0,
            });
            console.log('Added new item to cart:', productName);
        }
        
        console.log('Cart now has', cart.length, 'items');
        renderCart();
        updateCheckoutButton();
        updateProductStockDisplay(productId);
    }
    
    // Update stock display for a product based on cart quantity
    function updateProductStockDisplay(productId) {
        const stockEl = document.querySelector('.stock-status[data-product-id="' + productId + '"]');
        if (!stockEl) return;
        
        const cartItem = cart.find(item => item.product_id === productId);
        const cartQuantity = cartItem ? cartItem.quantity : 0;
        const originalStock = originalStocks[productId];
        
        if (originalStock !== undefined) {
            const displayStock = Math.max(0, originalStock - cartQuantity);
            
            if (cartQuantity > 0) {
                stockEl.textContent = displayStock + ' left • 🛒 ' + cartQuantity + ' in cart';
                stockEl.style.color = '#34d399';
            } else {
                stockEl.textContent = originalStock + ' units';
                stockEl.style.color = '#34d399';
            }
        }
    }
    
    // Update all product stock displays based on current cart
    function updateAllProductStockDisplays() {
        cart.forEach(item => {
            updateProductStockDisplay(item.product_id);
        });
    }
    
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

                    updateAllStockStatuses().then(() => {
                        updateAllProductStockDisplays();
                    });
                    updateCheckoutButton();
                }
            })
            .catch(error => console.error('Error fetching nearest warehouse:', error));
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
            return Promise.resolve();
        }

        const promises = [];
        document.querySelectorAll('.stock-status').forEach(el => {
            const productId = el.dataset.productId;
            let url = '{{ route('admin.pos.check-inventory') }}?product_id=' + productId + '&warehouse_id=' + warehouseId;
            if (distributorId) {
                url += '&distributor_id=' + distributorId;
            }

            const promise = fetch(url)
                .then(r => r.json())
                .then(data => {
                    const productId = parseInt(el.dataset.productId);
                    const cartItem = cart.find(item => item.product_id === productId);
                    const cartQuantity = cartItem ? cartItem.quantity : 0;
                    
                    // Store backend stock as the truth
                    originalStocks[productId] = data.available_for_sale;
                    
                    // Calculate display stock (backend - cart)
                    const displayStock = Math.max(0, data.available_for_sale - cartQuantity);
                    
                    if (data.available_for_sale > 0) {
                        if (cartQuantity > 0) {
                            el.textContent = displayStock + ' left • 🛒 ' + cartQuantity + ' in cart';
                        } else {
                            el.textContent = data.available_for_sale + ' units';
                        }
                        el.style.color = '#34d399';
                    } else {
                        el.textContent = 'Out of stock';
                        el.style.color = '#f87171';
                    }
                })
                .catch(error => {
                    console.error('Error checking stock:', error);
                    el.textContent = 'Stock check failed';
                    el.style.color = '#f87171';
                });
            promises.push(promise);
        });
        
        return Promise.all(promises);
    }

    // Update product prices based on distributor discount
    function updateProductPrices(distributorId) {
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
            const originalPrice = parseFloat(card.dataset.originalPrice || card.dataset.price);
            const priceEl = card.querySelector('.product-price');
            const mrpEl = card.querySelector('.product-mrp');
            const discountBadge = card.querySelector('.discount-badge');

            priceEl.textContent = companySettings.currency_symbol + originalPrice.toFixed(2);
            mrpEl.style.display = 'none';
            discountBadge.style.display = 'none';
        });
    }

    // Render cart
    function renderCart() {
        const cartContainer = document.getElementById('cart-items');
        const emptyCart = document.getElementById('empty-cart');
        
        console.log('renderCart called, cart length:', cart.length);
        
        if (!cartContainer || !emptyCart) {
            console.error('Cart elements not found');
            alert('ERROR: Cart container not found');
            return;
        }
        
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

            try {
            cartContainer.innerHTML = cart.map((item, index) => {
                try {
                const product = products.find(p => p.id == item.product_id);
                const mrpPrice = product ? (parseFloat(product.mrp_price) || item.unit_price) : item.unit_price;
                const unitPrice = parseFloat(item.unit_price) || 0;
                
                // Calculate item-level discount from discount_percent
                const itemSubtotal = item.quantity * mrpPrice;
                const itemDiscountPercent = item.discount_percent || 0;
                const itemDiscountAmount = itemSubtotal * (itemDiscountPercent / 100);
                const finalPrice = itemSubtotal - itemDiscountAmount;
                
                // Show distributor discount info
                const hasDistributorDiscount = distributorDiscount > 0;
                const distributorSavings = hasDistributorDiscount ? (mrpPrice - unitPrice) * item.quantity : 0;
                const currencySymbol = companySettings && companySettings.currency_symbol ? companySettings.currency_symbol : '₹';
                
                return `
                <div class="cart-item" style="margin-bottom: 0.75rem; padding: 0.75rem; background: rgba(255,255,255,0.03); border-radius: 8px;">
                    <div style="min-width: 0; flex: 2;">
                        <div style="font-weight: 500; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">${item.name}</div>
                        <div style="font-size: 0.7rem; color: var(--text-muted);">
                            MRP: ${currencySymbol}${mrpPrice.toFixed(2)} 
                            ${itemDiscountPercent > 0 ? `<span style="color: #f87171;">(${itemDiscountPercent}% off)</span>` : ''}
                        </div>
                        ${hasDistributorDiscount ? `
                        <div style="font-size: 0.7rem; color: #10b981;">
                            Dist. Price: ${currencySymbol}${unitPrice.toFixed(2)} 
                            <span style="color: #f87171;">(Save ${currencySymbol}${distributorSavings.toFixed(2)})</span>
                        </div>
                        ` : `
                        <div style="font-size: 0.7rem; color: #fbbf24;">
                            No distributor discount
                        </div>
                        `}
                    </div>
                    <div style="flex: 1;">
                        <input type="number" class="qty-input" value="${item.quantity}" min="1" step="1" onchange="updateQuantity(${index}, this.value)" style="width: 60px; padding: 0.25rem; font-size: 0.8rem;">
                    </div>
                    <div style="flex: 1; text-align: right; font-size: 0.8rem;">
                        ${itemDiscountAmount > 0 ? `
                            <div style="text-decoration: line-through; color: var(--text-muted);">${currencySymbol}${itemSubtotal.toFixed(2)}</div>
                            <div style="color: #f87171; font-size: 0.7rem;">-${currencySymbol}${itemDiscountAmount.toFixed(2)}</div>
                        ` : ''}
                    </div>
                    <div style="flex: 1; font-weight: 600; text-align: right; color: #34d399; font-size: 0.9rem;">
                        ${currencySymbol}${(unitPrice * item.quantity).toFixed(2)}
                    </div>
                    <div style="flex: 0;">
                        <button onclick="removeItem(${index})" style="background: none; border: none; color: #f87171; cursor: pointer; font-size: 1rem; padding: 0.25rem;">🗑️</button>
                    </div>
                </div>
                `;
                } catch (itemError) {
                    console.error('Error rendering cart item:', item, itemError);
                    return '<div style="color: #f87171; padding: 0.5rem;">Error rendering item</div>';
                }
            }).join('');
            } catch (error) {
                console.error('Error in renderCart:', error);
                alert('Error rendering cart: ' + error.message);
            }
        }

        calculateTotals();
    }

    // Update quantity
    window.updateQuantity = function(index, value) {
        const productId = cart[index].product_id;
        cart[index].quantity = parseFloat(value) || 1;
        renderCart();
        updateCheckoutButton();
        updateProductStockDisplay(productId);
    }

    // Remove item
    window.removeItem = function(index) {
        const removedProductId = cart[index].product_id;
        cart.splice(index, 1);
        renderCart();
        updateCheckoutButton();
        updateProductStockDisplay(removedProductId);
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
        if (companySettings && companySettings.gst_type !== 'none' && companySettings.gst_percentage > 0 && taxableAmount > 0) {
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
        
        const checkoutBtn = document.getElementById('checkout-btn');
        if (checkoutBtn) {
            checkoutBtn.disabled = !distributorId || !warehouseId || !hasItems;
        }
    }

    // Process checkout
    function processCheckout() {
        const distributorId = document.getElementById('distributor-select').value;
        const warehouseId = document.getElementById('warehouse-select').value;
        
        if (!distributorId || !warehouseId) {
            alert('Please select both distributor and warehouse');
            return;
        }
        
        if (cart.length === 0) {
            alert('Cart is empty');
            return;
        }
        
        const data = {
            distributor_id: distributorId,
            warehouse_id: warehouseId,
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
        
        // Disable button to prevent double submission
        const checkoutBtn = document.getElementById('checkout-btn');
        checkoutBtn.disabled = true;
        checkoutBtn.textContent = '⏳ Processing...';
        
        fetch('{{ route('admin.pos.store') }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json',
            },
            body: JSON.stringify(data),
        })
        .then(async (r) => {
            const text = await r.text();
            try {
                return JSON.parse(text);
            } catch (e) {
                console.error('Non-JSON response:', text);
                throw new Error('Server returned HTML instead of JSON. Check console for details.');
            }
        })
        .then(data => {
            if (data.success) {
                window.location.href = data.redirect_url;
            } else {
                alert(data.message || 'Error processing sale');
                checkoutBtn.disabled = false;
                checkoutBtn.textContent = '✅ Complete Sale';
            }
        })
        .catch(e => {
            console.error('Error:', e);
            alert('Error processing sale: ' + e.message);
            checkoutBtn.disabled = false;
            checkoutBtn.textContent = '✅ Complete Sale';
        });
    }
</script>
@endpush