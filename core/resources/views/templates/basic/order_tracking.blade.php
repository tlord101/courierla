@extends($activeTemplate.'layouts.frontend')

@section('content')
@include($activeTemplate . 'partials.breadcrumb')

<style>
    /* Bold & Modern Tracking UI */
    .tracking-section { background: #f4f7f6; padding-bottom: 100px; }
    .track-search-box { 
        margin-top: -50px; 
        background: #fff; 
        padding: 30px; 
        border-radius: 15px; 
        box-shadow: 0 15px 30px rgba(0,0,0,0.1);
    }
    
    .main-status-card { 
        background: #fff; 
        border-radius: 20px; 
        box-shadow: 0 20px 60px rgba(0,0,0,0.08); 
        overflow: hidden; 
        border: none;
        margin-top: 40px;
    }

    .status-header { 
        background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%); 
        color: #fff; 
        padding: 50px 20px; 
        text-align: center;
    }

    .status-badge-bold {
        background: #3b82f6; 
        color: white; 
        padding: 12px 35px;
        border-radius: 50px; 
        font-weight: 900; 
        font-size: 1.3rem;
        display: inline-block; 
        text-transform: uppercase; 
        letter-spacing: 2px;
        box-shadow: 0 10px 25px rgba(59, 130, 246, 0.5);
    }

    /* Info Grid for the missing fields */
    .info-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
        background: #fff;
        border-bottom: 1px solid #edf2f7;
    }

    .info-block {
        padding: 30px;
        border-right: 1px solid #edf2f7;
        transition: all 0.3s ease;
    }

    .info-block:last-child { border-right: none; }
    .info-block:hover { background: #f8fafc; }

    .label-bold { 
        display: block; 
        font-size: 0.8rem; 
        font-weight: 800; 
        color: #64748b; 
        text-transform: uppercase; 
        margin-bottom: 8px;
        letter-spacing: 1px;
    }

    .value-bold { 
        display: block; 
        font-size: 1.25rem; 
        font-weight: 900; 
        color: #0f172a; 
    }

    .text-primary-bold { color: #3b82f6 !important; }
    .text-success-bold { color: #10b981 !important; }

    #trackingMap { 
        height: 550px; 
        width: 100%; 
        border-top: 5px solid #3b82f6;
    }

    .address-box {
        background: #fff;
        padding: 35px;
        border-radius: 20px;
        border: 1px solid #e2e8f0;
        height: 100%;
        position: relative;
    }

    .address-icon {
        position: absolute;
        top: 20px;
        right: 20px;
        font-size: 2.5rem;
        opacity: 0.1;
    }

    .btn-invoice {
        background: #0f172a;
        color: #fff;
        border-radius: 50px;
        padding: 15px 40px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 1px;
        transition: all 0.3s;
    }

    .btn-invoice:hover {
        background: #3b82f6;
        color: #fff;
        transform: translateY(-3px);
    }
</style>

<section class="tracking-section">
    <div class="container">
        
        <div class="row justify-content-center">
            <div class="col-lg-7">
                <div class="track-search-box">
                    <form action="{{ route('order.tracking') }}" method="GET">
                        <div class="input-group input-group-lg">
                            <input type="text" name="order_number" value="{{ optional($orderNumber)->code }}" 
                                   placeholder="Enter Tracking Code (e.g. TTECH123...)" 
                                   class="form-control border-0 bg-light px-4" required>
                            <button type="submit" class="btn btn--primary px-4">
                                <i class="las la-search fs-4"></i>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        @if($orderNumber)
        <div class="main-status-card">
            
            <div class="status-header">
                <div id="statusBadge" class="status-badge-bold mb-3"></div>
                <h1 id="statusTitle" class="display-5 fw-bold mb-2"></h1>
                <p id="statusDescription" class="fs-5 text-white-50 fw-medium"></p>
            </div>

            <div class="info-grid">
                <div class="info-block">
                    <span class="label-bold">Shipment Mode</span>
                    <span class="value-bold text-primary-bold">
                        <i class="las la-shipping-fast"></i> {{ $orderNumber->shipment_mode ?? 'Standard' }}
                    </span>
                </div>
                <div class="info-block">
                    <span class="label-bold">Payment Method</span>
                    <span class="value-bold">
                        <i class="las la-credit-card"></i> {{ $orderNumber->payment_mode ?? 'Not Specified' }}
                    </span>
                </div>
                <div class="info-block">
                    <span class="label-bold">Est. Delivery</span>
                    <span class="value-bold text-success-bold">
                        <i class="las la-calendar-check"></i> 
                        {{ $orderNumber->delivery_date ? showDateTime($orderNumber->delivery_date, 'd M, Y') : 'Processing' }}
                    </span>
                    <small class="fw-bold text-muted">{{ $orderNumber->delivery_time }}</small>
                </div>
                <div class="info-block">
                    <span class="label-bold">Tracking ID</span>
                    <span class="value-bold">#{{ $orderNumber->code }}</span>
                    <small class="text-muted">Invoice: {{ $orderNumber->invoice_id }}</small>
                </div>
            </div>

            <div id="trackingMap"></div>
        </div>

        <div class="row mt-5 g-4">
            <div class="col-lg-6">
                <div class="address-box">
                    <i class="las la-box-open address-icon"></i>
                    <h4 class="fw-bold mb-4 text-primary"><i class="las la-map-marker-alt"></i> Shipment Origin</h4>
                    <div class="ps-3 border-start border-4 border-primary">
                        <h5 class="fw-black mb-1 text-uppercase">{{ $orderNumber->sender_name }}</h5>
                        <p class="text-muted mb-2 fs-6">{{ $orderNumber->sender_address }}</p>
                        <span class="badge bg-light text-dark fw-bold border">{{ $orderNumber->sender_phone }}</span>
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="address-box">
                    <i class="las la-truck-loading address-icon"></i>
                    <h4 class="fw-bold mb-4 text-success"><i class="las la-map-pin"></i> Destination Point</h4>
                    <div class="ps-3 border-start border-4 border-success">
                        <h5 class="fw-black mb-1 text-uppercase">{{ $orderNumber->receiver_name }}</h5>
                        <p class="text-muted mb-2 fs-6">{{ $orderNumber->receiver_address }}</p>
                        <span class="badge bg-light text-dark fw-bold border">{{ $orderNumber->receiver_phone }}</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="text-center mt-5">
            <a href="{{ route('tracking.invoice', $orderNumber->code) }}" target="_blank" class="btn btn-invoice">
                <i class="las la-file-invoice-dollar fs-5"></i> View & Print Full Invoice
            </a>
        </div>
        @endif

    </div>
</section>

@endsection

@push('style')
<link href="https://api.mapbox.com/mapbox-gl-js/v2.15.0/mapbox-gl.css" rel="stylesheet">
@endpush

@push('script')
<script src="https://api.mapbox.com/mapbox-gl-js/v2.15.0/mapbox-gl.js"></script>
<script src="https://unpkg.com/@turf/turf@6/turf.min.js"></script>

<script>
document.addEventListener("DOMContentLoaded", function () {
@if($orderNumber)

/* =========================
   STATUS MAPPING
========================== */
const statusIdx = {{ (int) $orderNumber->status }};
const statusMap = {
    0: { title: 'Pending', class: 'bg-warning', progress: 0.05, desc: 'Your order has been registered and is awaiting pickup.' },
    1: { title: 'In Transit', class: 'bg-primary', progress: 0.55, desc: 'Shipment is currently on its way to the destination.' },
    2: { title: 'Arrived', class: 'bg-info', progress: 0.90, desc: 'Package has reached the local delivery branch.' },
    3: { title: 'Completed', class: 'bg-success', progress: 1.0, desc: 'Package has been successfully received by the recipient.' }
};

const currentStatus = statusMap[statusIdx] || statusMap[0];

document.getElementById('statusBadge').textContent = currentStatus.title;
document.getElementById('statusBadge').classList.add(currentStatus.class);
document.getElementById('statusTitle').textContent = currentStatus.title;
document.getElementById('statusDescription').textContent = currentStatus.desc;

/* =========================
   MAPBOX ENGINE
========================== */
mapboxgl.accessToken = 'pk.your_mapbox_access_token_here';

const start = [{{ (float)$orderNumber->sender_longitude }}, {{ (float)$orderNumber->sender_latitude }}];
const end = [{{ (float)$orderNumber->receiver_longitude }}, {{ (float)$orderNumber->receiver_latitude }}];

const map = new mapboxgl.Map({
    container: 'trackingMap',
    style: 'mapbox://styles/mapbox/navigation-night-v1',
    center: start,
    zoom: 4,
    pitch: 45
});

map.on('load', async () => {
    // Generate the path
    const line = turf.lineString([start, end]);
    const fullLine = turf.lineString([start, end]); // In a real app, replace with Directions API coordinates
    
    map.addSource('route', {
        type: 'geojson',
        data: fullLine
    });

    map.addLayer({
        id: 'route-line',
        type: 'line',
        source: 'route',
        paint: {
            'line-color': '#3b82f6',
            'line-width': 4,
            'line-dasharray': [1, 1]
        }
    });

    // Markers
    new mapboxgl.Marker({ color: '#3b82f6' }).setLngLat(start).addTo(map);
    new mapboxgl.Marker({ color: '#10b981' }).setLngLat(end).addTo(map);

    // Calculate position based on progress
    const totalDist = turf.length(fullLine);
    const progressDist = totalDist * currentStatus.progress;
    const progressPoint = turf.along(fullLine, progressDist).geometry.coordinates;

    const movingMarker = document.createElement('div');
    movingMarker.className = 'moving-marker';
    movingMarker.innerHTML = '<i class="las la-truck fs-2" style="color: #fff; text-shadow: 0 0 10px #3b82f6;"></i>';

    new mapboxgl.Marker(movingMarker).setLngLat(progressPoint).addTo(map);
    
    // Auto-focus map
    const bounds = new mapboxgl.LngLatBounds(start, end);
    map.fitBounds(bounds, { padding: 100 });
});

@endif
});
</script>
@endpush
