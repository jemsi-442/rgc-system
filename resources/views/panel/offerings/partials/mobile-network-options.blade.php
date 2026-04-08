@php
    $selectedNetwork = $selectedNetwork ?? old('mobile_network');
    $paymentNetworks = [
        [
            'value' => 'mpesa',
            'label' => __('M-Pesa'),
            'short' => 'M',
            'assets' => [
                'images/payments/networks/mpesa.svg',
                'images/payments/networks/mpesa.png',
                'images/payments/networks/mpesa.webp',
            ],
        ],
        [
            'value' => 'airtel_money',
            'label' => __('Airtel Money'),
            'short' => 'A',
            'assets' => [
                'images/payments/networks/airtel-money.svg',
                'images/payments/networks/airtel-money.png',
                'images/payments/networks/airtel-money.webp',
            ],
        ],
        [
            'value' => 'mixx_by_yas',
            'label' => __('Mixx by Yas'),
            'short' => 'Y',
            'assets' => [
                'images/payments/networks/mixx-by-yas.svg',
                'images/payments/networks/mixx-by-yas.png',
                'images/payments/networks/mixx-by-yas.webp',
            ],
        ],
        [
            'value' => 'halopesa',
            'label' => __('HaloPesa'),
            'short' => 'H',
            'assets' => [
                'images/payments/networks/halopesa.svg',
                'images/payments/networks/halopesa.png',
                'images/payments/networks/halopesa.webp',
            ],
        ],
    ];

    $resolveNetworkAsset = static function (array $assets): ?string {
        foreach ($assets as $asset) {
            if (is_file(public_path($asset))) {
                return asset($asset);
            }
        }

        return null;
    };
@endphp

<div>
    <label class="field-label" for="mobile_network">{{ __('Mobile money network') }}</label>
    <div class="payment-network-grid">
        @foreach($paymentNetworks as $network)
            @php $networkAsset = $resolveNetworkAsset($network['assets']); @endphp
            <label class="payment-network-option">
                <input
                    class="payment-network-input"
                    type="radio"
                    name="mobile_network"
                    value="{{ $network['value'] }}"
                    @checked($selectedNetwork === $network['value'])
                >
                <span class="payment-network-card">
                    @if($networkAsset)
                        <img src="{{ $networkAsset }}" alt="{{ $network['label'] }}" class="payment-network-logo">
                    @else
                        <span class="payment-network-fallback">{{ $network['short'] }}</span>
                    @endif
                    <span class="payment-network-name">{{ $network['label'] }}</span>
                </span>
            </label>
        @endforeach
    </div>
    <p class="form-hint mt-2">{{ __('Choose the payer network if you know it. You can place official network logos in public/images/payments/networks/.') }}</p>
</div>
