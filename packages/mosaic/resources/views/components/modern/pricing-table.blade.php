{{--
    Modern Pricing Table Widget
    
    Props:
    - title (string): Section heading
    - plans (array): Array of pricing plan objects
    - currency (string): Currency symbol - Default: '$'
    - billingOptions (string): 'monthly|annual|both' - Default: 'monthly'
    - customizable (bool): Show admin hints
--}}

@props([
    'title' => 'Simple, Transparent Pricing',
    'plans' => [
        [
            'name' => 'Starter',
            'price' => '29',
            'priceAnnual' => '290',
            'description' => 'For individuals',
            'features' => ['Up to 5 projects', '1 GB storage', 'Email support'],
            'cta' => ['label' => 'Get Started', 'url' => '#'],
            'featured' => false,
        ],
        [
            'name' => 'Professional',
            'price' => '79',
            'priceAnnual' => '790',
            'description' => 'For teams',
            'features' => ['Unlimited projects', '100 GB storage', 'Priority support', 'Advanced analytics'],
            'cta' => ['label' => 'Start Free', 'url' => '#'],
            'featured' => true,
        ],
        [
            'name' => 'Enterprise',
            'price' => 'Custom',
            'priceAnnual' => 'Custom',
            'description' => 'For enterprises',
            'features' => ['Everything in Pro', 'Unlimited storage', 'Dedicated support', 'Custom integrations'],
            'cta' => ['label' => 'Contact Sales', 'url' => '#'],
            'featured' => false,
        ],
    ],
    'currency' => '$',
    'billingOptions' => 'monthly',
    'customizable' => true,
])

<section class="mosaic-pricing px-6 py-12 md:px-12 md:py-16">
    {{-- Header --}}
    @if ($title)
        <div class="mx-auto mb-12 max-w-2xl text-center">
            <h2
                class="text-3xl font-bold md:text-4xl"
                style="
                    color: var(--mosaic-on-surface);
                    font-family: var(--mosaic-font-headline);
                "
            >
                {{ $title }}
            </h2>
        </div>
    @endif

    {{-- Billing Toggle --}}
    @if ($billingOptions === 'both')
        <div class="mb-12 flex items-center justify-center gap-4">
            <span
                class="billing-toggle-label"
                style="color: var(--mosaic-on-surface)"
            >
                Monthly
            </span>
            <button
                class="billing-toggle-button"
                style="
                    background-color: var(--mosaic-primary);
                    border: none;
                    cursor: pointer;
                    width: 60px;
                    height: 32px;
                    border-radius: 16px;
                    position: relative;
                    transition: background-color 0.3s;
                "
                onclick="toggleBillingCycle(this)"
            >
                <div
                    class="billing-toggle-dot"
                    style="
                        width: 28px;
                        height: 28px;
                        border-radius: 50%;
                        background-color: white;
                        position: absolute;
                        left: 2px;
                        top: 2px;
                        transition: left 0.3s;
                    "
                ></div>
            </button>
            <span
                class="billing-toggle-label"
                style="color: var(--mosaic-on-surface)"
            >
                Annual
            </span>
            <span
                class="billing-toggle-save"
                style="
                    color: var(--mosaic-primary);
                    font-weight: 600;
                    margin-left: 1rem;
                    font-size: 0.875rem;
                "
            >
                Save 17%
            </span>
        </div>
    @endif

    {{-- Pricing Plans --}}
    <div
        class="pricing-grid mx-auto grid max-w-6xl grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-3"
        data-billing="{{ $billingOptions === 'both' ? 'monthly' : $billingOptions }}"
    >
        @forelse ($plans as $plan)
            <div
                @class([
                    'mosaic-card pricing-plan relative',
                    'lg:scale-105' => $plan['featured'] ?? false,
                ])
                data-price-monthly="{{ $plan['price'] ?? '' }}"
                data-price-annual="{{ $plan['priceAnnual'] ?? $plan['price'] ?? '' }}"
                style="background-color: var(--mosaic-surface-container); @if ($plan['featured'] ?? false) background: linear-gradient(135deg, var(--mosaic-primary-container) 0%, #5a00c6 100%); @endif"
            >
                {{-- Featured Badge --}}
                @if ($plan['featured'] ?? false)
                    <div
                        class="absolute -top-4 left-1/2 -translate-x-1/2 transform"
                        style="transform: translateX(-50%)"
                    >
                        <span
                            class="mosaic-badge mosaic-badge-tertiary px-4 py-1"
                            style="
                                background-color: var(--mosaic-tertiary);
                                color: var(--mosaic-on-tertiary);
                            "
                        >
                            Most Popular
                        </span>
                    </div>
                @endif

                {{-- Plan Name --}}
                <h3
                    class="mb-2 text-2xl font-bold"
                    style="
                        color: {{ $plan['featured'] ?? false ? 'white' : 'var(--mosaic-on-surface)' }};
                    "
                >
                    {{ $plan['name'] }}
                </h3>

                {{-- Description --}}
                @if (isset($plan['description']))
                    <p
                        class="mb-6 text-sm"
                        style="
                            color: {{ $plan['featured'] ?? false ? 'rgba(255,255,255,0.8)' : 'var(--mosaic-on-surface-variant)' }};
                        "
                    >
                        {{ $plan['description'] }}
                    </p>
                @endif

                {{-- Price --}}
                <div class="price-container mb-6">
                    <span
                        class="plan-price text-4xl font-bold"
                        style="
                            color: {{ $plan['featured'] ?? false ? 'white' : 'var(--mosaic-on-surface)' }};
                        "
                    >
                        {{ $currency }}{{ $plan['price'] }}
                    </span>
                    @if ($plan['price'] !== 'Custom')
                        <span
                            class="billing-period"
                            style="
                                color: {{ $plan['featured'] ?? false ? 'rgba(255,255,255,0.7)' : 'var(--mosaic-on-surface-variant)' }};
                            "
                        >
                            /month
                        </span>
                    @endif
                </div>

                {{-- Features List --}}
                @if (isset($plan['features']))
                    <ul class="mb-8 space-y-3">
                        @foreach ($plan['features'] as $feature)
                            <li
                                class="flex items-start gap-2"
                                style="
                                    color: {{ $plan['featured'] ?? false ? 'rgba(255,255,255,0.9)' : 'var(--mosaic-on-surface)' }};
                                "
                            >
                                <span
                                    style="
                                        color: var(--mosaic-tertiary);
                                        font-weight: bold;
                                    "
                                >
                                    ✓
                                </span>
                                <span>{{ $feature }}</span>
                            </li>
                        @endforeach
                    </ul>
                @endif

                {{-- CTA Button --}}
                @if (isset($plan['cta']))
                    <a
                        href="{{ $plan['cta']['url'] }}"
                        @class([
                            'mosaic-btn w-full text-center',
                            'mosaic-btn-primary' => $plan['featured'] ?? false,
                            'mosaic-btn-secondary' => ! ($plan['featured'] ?? false),
                        ])
                        style="@if ($plan['featured'] ?? false) background: white; color: var(--mosaic-primary-container); @endif"
                    >
                        {{ $plan['cta']['label'] }}
                    </a>
                @endif
            </div>
        @empty
            <div class="col-span-full py-12 text-center">
                <p style="color: var(--mosaic-on-surface-variant)">
                    No pricing plans configured
                </p>
            </div>
        @endforelse
    </div>

    {{-- Admin Hint --}}
    @if ($customizable && auth()->check())
            <div
                class="mt-12 max-w-full pt-8 text-center"
                style="
                    border-top: 1px solid var(--mosaic-outline-variant);
                    opacity: 0.6;
                "
            >
                <span class="mosaic-text-label text-xs">
                    ✨ Customize: Add plans, features, pricing, featured plan,
                    billing cycle options
                </span>
            </div>
    @endif
</section>

<script>
    function toggleBillingCycle(button) {
        const grid = document.querySelector('.pricing-grid')
        const currentBilling = grid.getAttribute('data-billing')
        const newBilling = currentBilling === 'monthly' ? 'annual' : 'monthly'

        grid.setAttribute('data-billing', newBilling)

        const plans = document.querySelectorAll('.pricing-plan')
        plans.forEach((plan) => {
            const priceElement = plan.querySelector('.plan-price')
            const periodElement = plan.querySelector('.billing-period')

            if (newBilling === 'annual') {
                const annualPrice = plan.getAttribute('data-price-annual')
                priceElement.textContent = priceElement.textContent.replace(
                    plan.getAttribute('data-price-monthly'),
                    annualPrice,
                )
                if (annualPrice !== 'Custom' && periodElement) {
                    periodElement.textContent = '/year'
                }
            } else {
                const monthlyPrice = plan.getAttribute('data-price-monthly')
                priceElement.textContent = priceElement.textContent.replace(
                    plan.getAttribute('data-price-annual'),
                    monthlyPrice,
                )
                if (monthlyPrice !== 'Custom' && periodElement) {
                    periodElement.textContent = '/month'
                }
            }
        })

        const dot = button.querySelector('.billing-toggle-dot')
        if (newBilling === 'annual') {
            dot.style.left = '30px'
        } else {
            dot.style.left = '2px'
        }
    }
</script>
