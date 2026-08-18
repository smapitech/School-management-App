<?php

namespace Tests\Feature;

use App\Models\Address;
use App\Models\Order;
use App\Models\Payment;
use App\Models\Product;
use App\Models\Setting;
use App\Models\Subscription;
use App\Models\SubscriptionPlan;
use App\Models\User;
use App\Support\StoreSettings;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class CommerceFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_public_storefront_pages_render(): void
    {
        $this->seed();

        $product = Product::firstOrFail();

        $this->get('/')->assertOk()->assertSee('Premium marketplace PWA');
        $this->get(route('products.index'))->assertOk()->assertSee($product->name);
        $this->get(route('products.show', $product))->assertOk()->assertSee($product->sku);
    }

    public function test_admin_dashboard_is_role_protected(): void
    {
        $admin = User::create([
            'name' => 'Admin',
            'email' => 'admin-test@example.com',
            'role' => 'admin',
            'status' => true,
            'password' => Hash::make('Password123!'),
        ]);

        $customer = User::create([
            'name' => 'Customer',
            'email' => 'customer-test@example.com',
            'role' => 'customer',
            'status' => true,
            'password' => Hash::make('Password123!'),
        ]);

        $this->actingAs($customer)->get(route('admin.dashboard'))->assertForbidden();
        $this->actingAs($admin)
            ->get(route('admin.dashboard'))
            ->assertOk()
            ->assertSee('Store command center')
            ->assertSee('Sales summary')
            ->assertSee('Top products');
    }

    public function test_guest_can_add_product_to_cart(): void
    {
        $this->seed();

        $product = Product::firstOrFail();

        $this->post(route('cart.store', $product), ['quantity' => 2])->assertRedirect();
        $this->get(route('cart.index'))->assertOk()->assertSee($product->name);
    }

    public function test_checkout_keeps_cart_until_payment_is_confirmed(): void
    {
        $this->seed();
        $this->enablePaystack();

        Http::fake([
            'api.paystack.co/transaction/initialize' => Http::response([
                'status' => true,
                'data' => [
                    'authorization_url' => 'https://checkout.paystack.com/cart-retention-test',
                    'access_code' => 'cart-retention-test',
                ],
            ], 200),
        ]);

        $admin = User::where('role', 'admin')->firstOrFail();
        $customer = User::create([
            'name' => 'Payment Customer',
            'email' => 'payment-customer@example.com',
            'role' => 'customer',
            'status' => true,
            'email_verified_at' => now(),
            'password' => Hash::make('Password123!'),
        ]);
        $product = Product::firstOrFail();
        $address = Address::create([
            'user_id' => $customer->id,
            'label' => 'Home',
            'full_name' => $customer->name,
            'email' => $customer->email,
            'phone' => '+2348000000001',
            'country' => 'Nigeria',
            'state' => 'Lagos',
            'city' => 'Lagos',
            'address_line' => '1 Test Street',
        ]);

        $this->actingAs($customer)
            ->post(route('cart.store', $product), ['quantity' => 1])
            ->assertRedirect();

        $this->actingAs($customer)
            ->post(route('checkout.store'), [
                'shipping_address_id' => $address->id,
                'payment_method' => 'paystack',
            ])
            ->assertRedirect();

        $order = Order::where('user_id', $customer->id)->latest('id')->firstOrFail();

        $this->assertDatabaseHas('cart_items', ['product_id' => $product->id, 'quantity' => 1]);
        $this->assertDatabaseHas('orders', [
            'id' => $order->id,
            'payment_status' => 'unpaid',
        ]);

        $this->actingAs($admin)
            ->patch(route('admin.orders.update', $order), [
                'status' => 'processing',
                'payment_status' => 'paid',
            ])
            ->assertRedirect();

        $this->assertDatabaseMissing('cart_items', ['product_id' => $product->id, 'quantity' => 1]);
    }

    public function test_stripe_checkout_redirects_to_hosted_session(): void
    {
        $this->seed();

        Setting::updateOrCreate(
            ['key' => 'stripe_public_key'],
            ['group' => 'payments', 'type' => 'string', 'value' => 'pk_test_'.str_repeat('a', 32)]
        );
        Setting::updateOrCreate(
            ['key' => 'stripe_secret_key'],
            ['group' => 'payments', 'type' => 'secret', 'value' => 'sk_test_'.str_repeat('a', 32)]
        );
        Setting::updateOrCreate(
            ['key' => 'stripe_enabled'],
            ['group' => 'payments', 'type' => 'boolean', 'value' => '1']
        );
        cache()->forget('store_settings');

        Http::fake([
            'api.stripe.com/v1/checkout/sessions' => Http::response([
                'id' => 'cs_test_123',
                'url' => 'https://checkout.stripe.com/pay/cs_test_123',
                'payment_intent' => 'pi_test_123',
                'payment_status' => 'unpaid',
            ], 200),
        ]);

        $customer = User::where('role', 'customer')->firstOrFail();
        $product = Product::firstOrFail();
        $address = Address::create([
            'user_id' => $customer->id,
            'label' => 'Home',
            'full_name' => $customer->name,
            'email' => $customer->email,
            'phone' => '+2348000000004',
            'country' => 'Nigeria',
            'state' => 'Lagos',
            'city' => 'Lagos',
            'address_line' => '4 Stripe Avenue',
        ]);

        $this->actingAs($customer)
            ->post(route('cart.store', $product), ['quantity' => 1])
            ->assertRedirect();

        $this->actingAs($customer)
            ->post(route('checkout.store'), [
                'shipping_address_id' => $address->id,
                'payment_method' => 'stripe',
            ])
            ->assertRedirect('https://checkout.stripe.com/pay/cs_test_123');

        $this->assertDatabaseHas('payments', [
            'provider' => 'stripe',
            'reference' => 'cs_test_123',
            'status' => 'pending',
        ]);

        $payment = Payment::where('provider', 'stripe')->latest('id')->firstOrFail();

        Http::fake([
            'api.stripe.com/v1/checkout/sessions/cs_test_123' => Http::response([
                'id' => 'cs_test_123',
                'status' => 'complete',
                'payment_status' => 'paid',
                'amount_total' => (int) round((float) $payment->amount * 100),
                'currency' => strtolower($payment->currency),
            ], 200),
        ]);

        $this->actingAs($customer)
            ->get(route('payments.callback', ['provider' => 'stripe', 'session_id' => 'cs_test_123']))
            ->assertRedirect(route('customer.orders.show', $payment->order));

        $this->assertDatabaseHas('payments', ['id' => $payment->id, 'status' => 'paid']);
        $this->assertDatabaseHas('orders', ['id' => $payment->order_id, 'payment_status' => 'paid']);
    }

    public function test_paystack_checkout_redirects_and_confirms_payment(): void
    {
        $this->seed();

        $this->enablePaystack();

        Http::fake([
            'api.paystack.co/transaction/initialize' => Http::response([
                'status' => true,
                'message' => 'Authorization URL created',
                'data' => [
                    'authorization_url' => 'https://checkout.paystack.com/test-access-code',
                    'access_code' => 'test-access-code',
                ],
            ], 200),
        ]);

        $customer = User::where('role', 'customer')->firstOrFail();
        $product = Product::firstOrFail();
        $address = Address::create([
            'user_id' => $customer->id,
            'label' => 'Home',
            'full_name' => $customer->name,
            'email' => $customer->email,
            'phone' => '+2348000000005',
            'country' => 'Nigeria',
            'state' => 'Lagos',
            'city' => 'Lagos',
            'address_line' => '5 Paystack Avenue',
        ]);

        $this->actingAs($customer)
            ->post(route('cart.store', $product), ['quantity' => 1])
            ->assertRedirect();

        $this->actingAs($customer)
            ->post(route('checkout.store'), [
                'shipping_address_id' => $address->id,
                'payment_method' => 'paystack',
            ])
            ->assertRedirect('https://checkout.paystack.com/test-access-code');

        $payment = Payment::where('provider', 'paystack')->latest('id')->firstOrFail();
        $reference = $payment->reference;

        Http::fake([
            'api.paystack.co/transaction/verify/*' => Http::response([
                'status' => true,
                'data' => [
                    'status' => 'success',
                    'reference' => $reference,
                    'amount' => (int) round((float) $payment->amount * 100),
                    'currency' => strtoupper($payment->currency),
                ],
            ], 200),
        ]);

        $this->actingAs($customer)
            ->get(route('payments.callback', ['provider' => 'paystack', 'reference' => $reference]))
            ->assertRedirect(route('customer.orders.show', $payment->order));

        $this->assertDatabaseHas('payments', ['id' => $payment->id, 'status' => 'paid']);
        $this->assertDatabaseHas('orders', ['id' => $payment->order_id, 'payment_status' => 'paid']);
    }

    public function test_gateway_failure_returns_to_saved_order_instead_of_error_500(): void
    {
        $this->seed();
        $this->enablePaystack();

        Http::fake([
            'api.paystack.co/transaction/initialize' => Http::response([
                'status' => false,
                'message' => 'Gateway unavailable',
            ], 503),
        ]);

        $customer = User::where('role', 'customer')->firstOrFail();
        $product = Product::firstOrFail();
        $address = Address::create([
            'user_id' => $customer->id,
            'label' => 'Home',
            'full_name' => $customer->name,
            'email' => $customer->email,
            'phone' => '+2348000000006',
            'country' => 'Nigeria',
            'state' => 'Lagos',
            'city' => 'Lagos',
            'address_line' => '6 Gateway Failure Street',
        ]);

        $this->actingAs($customer)
            ->post(route('cart.store', $product), ['quantity' => 1])
            ->assertRedirect();

        $response = $this->actingAs($customer)
            ->post(route('checkout.store'), [
                'shipping_address_id' => $address->id,
                'payment_method' => 'paystack',
            ]);

        $order = Order::where('user_id', $customer->id)->latest('id')->firstOrFail();

        $response
            ->assertRedirect(route('customer.orders.show', $order))
            ->assertSessionHas('error');

        $this->assertDatabaseHas('payments', [
            'order_id' => $order->id,
            'provider' => 'paystack',
            'status' => 'failed',
        ]);
    }

    public function test_admin_can_add_drive_video_and_image_limit_is_enforced(): void
    {
        $this->seed();

        $admin = User::where('role', 'admin')->firstOrFail();
        $product = Product::with('category')->firstOrFail();
        $driveUrl = 'https://drive.google.com/file/d/demo-video-id/view?usp=sharing';

        $this->actingAs($admin)
            ->put(route('admin.products.update', $product), $this->productPayload($product, [
                'video_url' => $driveUrl,
            ]))
            ->assertRedirect(route('admin.products.index'));

        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'video_url' => 'https://drive.google.com/file/d/demo-video-id/preview',
        ]);

        $product = $product->fresh();

        $this->get(route('products.show', $product))
            ->assertOk()
            ->assertSee('Product video')
            ->assertSee('https://drive.google.com/file/d/demo-video-id/preview', false);

        $tooManyImages = collect(range(1, 5))
            ->map(fn (int $index) => UploadedFile::fake()->image("extra-{$index}.jpg"))
            ->all();

        $this->actingAs($admin)
            ->put(route('admin.products.update', $product), $this->productPayload($product, [
                'images' => $tooManyImages,
            ]))
            ->assertSessionHasErrors('images');
    }

    public function test_active_banners_loop_on_admin_preview_and_homepage(): void
    {
        $this->seed();

        $admin = User::where('role', 'admin')->firstOrFail();

        $this->actingAs($admin)
            ->get(route('admin.banners.index'))
            ->assertOk()
            ->assertSee('Continuous homepage loop')
            ->assertSee('x-init="start()"', false)
            ->assertSee('setInterval', false);

        $this->get(route('home'))
            ->assertOk()
            ->assertSee('Premium marketplace PWA')
            ->assertSee('x-init="start()"', false)
            ->assertSee('setInterval', false);
    }

    public function test_customer_can_subscribe_to_product_bundle_and_process_renewals(): void
    {
        $this->seed();
        $this->enablePaystack();

        Http::fake([
            'api.paystack.co/transaction/initialize' => Http::response([
                'status' => true,
                'data' => [
                    'authorization_url' => 'https://checkout.paystack.com/subscription-test',
                    'access_code' => 'subscription-test',
                ],
            ], 200),
        ]);

        $admin = User::where('role', 'admin')->firstOrFail();
        $customer = User::where('role', 'customer')->firstOrFail();
        $products = Product::take(2)->get();

        $this->actingAs($admin)
            ->post(route('admin.subscription-plans.store'), [
                'name' => 'Weekly Essentials',
                'slug' => 'weekly-essentials',
                'description' => 'A weekly product bundle.',
                'interval' => 'weekly',
                'price' => 50000,
                'sort_order' => 1,
                'is_active' => '1',
                'items' => [
                    ['product_id' => $products[0]->id, 'quantity' => 1, 'unit_price' => 25000],
                    ['product_id' => $products[1]->id, 'quantity' => 1, 'unit_price' => 25000],
                ],
            ])
            ->assertRedirect(route('admin.subscription-plans.index'));

        $plan = SubscriptionPlan::where('slug', 'weekly-essentials')->firstOrFail();

        $address = Address::create([
            'user_id' => $customer->id,
            'label' => 'Home',
            'full_name' => $customer->name,
            'email' => $customer->email,
            'phone' => '+2348000000003',
            'country' => 'Nigeria',
            'state' => 'Lagos',
            'city' => 'Lagos',
            'address_line' => '1 Subscription Street',
        ]);

        $this->actingAs($customer)
            ->get(route('subscriptions.plans'))
            ->assertOk()
            ->assertSee('Weekly Essentials');

        $this->actingAs($customer)
            ->post(route('subscriptions.store', $plan), [
                'shipping_address_id' => $address->id,
                'payment_method' => 'paystack',
                'notes' => 'Deliver early.',
            ])
            ->assertRedirect();

        $subscription = Subscription::where('user_id', $customer->id)->where('subscription_plan_id', $plan->id)->firstOrFail();

        $this->assertDatabaseHas('subscriptions', [
            'id' => $subscription->id,
            'status' => 'active',
            'interval' => 'weekly',
            'payment_method' => 'paystack',
        ]);
        $this->assertSame(1, Order::where('subscription_id', $subscription->id)->count());
        $this->assertDatabaseHas('payments', ['provider' => 'paystack', 'status' => 'pending']);

        $subscription->update(['next_billing_at' => now()->subMinute()]);
        Artisan::call('subscriptions:process', ['--limit' => 10]);

        $this->assertStringContainsString('Processed 1 subscription renewal', Artisan::output());
        $this->assertSame(2, Order::where('subscription_id', $subscription->id)->count());

        $this->actingAs($customer)
            ->get(route('customer.subscriptions.index'))
            ->assertOk()
            ->assertSee('Weekly Essentials')
            ->assertSee('Latest order');
    }

    public function test_admin_settings_center_renders_and_preserves_secrets(): void
    {
        $this->seed();

        $admin = User::where('role', 'admin')->firstOrFail();

        $this->actingAs($admin)
            ->get(route('admin.settings.index'))
            ->assertOk()
            ->assertSee('A modern settings center')
            ->assertSee('Payment gateway')
            ->assertSee('Progressive Web App')
            ->assertSee('USD - US Dollar')
            ->assertSee('GBP - Pounds Sterling')
            ->assertSee('EUR - Euro')
            ->assertSee('Catalog modules')
            ->assertDontSee('Paystack webhook secret')
            ->assertDontSee('Stripe webhook secret')
            ->assertDontSee('Flutterwave public key')
            ->assertDontSee('Bank name');

        $payload = $this->settingsPayload([
            'store_name' => 'Better Commerce',
            'currency_code' => 'usd',
            'paystack_public_key' => 'pk_test_'.str_repeat('c', 32),
            'paystack_secret_key' => 'sk_test_'.str_repeat('d', 32),
            'paystack_enabled' => '1',
            'online_payment_enabled' => '1',
        ]);

        $this->actingAs($admin)
            ->patch(route('admin.settings.update'), $payload)
            ->assertRedirect();

        $this->assertDatabaseHas('settings', ['key' => 'store_name', 'value' => 'Better Commerce']);
        $this->assertDatabaseHas('settings', ['key' => 'currency_code', 'value' => 'USD']);
        $this->assertDatabaseHas('settings', ['key' => 'currency_symbol', 'value' => '$']);
        $this->assertDatabaseHas('settings', ['key' => 'paystack_secret_key', 'value' => 'sk_test_'.str_repeat('d', 32)]);

        $this->actingAs($admin)
            ->patch(route('admin.settings.update'), $this->settingsPayload([
                'store_name' => 'Better Commerce Pro',
                'paystack_secret_key' => '',
            ]))
            ->assertRedirect();

        $this->assertDatabaseHas('settings', ['key' => 'store_name', 'value' => 'Better Commerce Pro']);
        $this->assertDatabaseHas('settings', ['key' => 'paystack_secret_key', 'value' => 'sk_test_'.str_repeat('d', 32)]);
    }

    public function test_checkout_reads_fresh_payment_settings_instead_of_stale_shared_cache(): void
    {
        $this->seed();

        cache()->put('store_settings', [
            'online_payment_enabled' => '0',
            'paystack_enabled' => '0',
            'stripe_enabled' => '0',
        ], 300);

        foreach ([
            'online_payment_enabled' => '1',
            'paystack_enabled' => '1',
            'stripe_enabled' => '1',
            'paystack_public_key' => 'pk_live_'.str_repeat('p', 32),
            'paystack_secret_key' => 'sk_live_'.str_repeat('s', 32),
            'stripe_public_key' => 'pk_live_'.str_repeat('p', 48),
            'stripe_secret_key' => '"sk_live_'.str_repeat('s', 48).'"',
        ] as $key => $value) {
            Setting::updateOrCreate(
                ['key' => $key],
                [
                    'group' => 'payments',
                    'type' => str_ends_with($key, 'secret_key') ? 'secret' : 'string',
                    'value' => $value,
                ]
            );
        }

        $this->assertSame([
            'paystack' => 'Paystack',
            'stripe' => 'Stripe',
        ], StoreSettings::enabledPaymentMethods());
    }

    private function productPayload(Product $product, array $overrides = []): array
    {
        return array_merge([
            'category_id' => $product->category_id,
            'brand_id' => $product->brand_id,
            'name' => $product->name,
            'slug' => $product->slug,
            'sku' => $product->sku,
            'short_description' => $product->short_description,
            'description' => $product->description,
            'price' => $product->price,
            'discount_price' => $product->discount_price,
            'stock_quantity' => $product->stock_quantity,
            'low_stock_threshold' => $product->low_stock_threshold,
            'weight' => $product->weight,
            'is_featured' => $product->is_featured ? '1' : null,
            'is_active' => $product->is_active ? '1' : null,
        ], $overrides);
    }

    private function settingsPayload(array $overrides = []): array
    {
        return array_merge([
            'store_name' => 'Shopperzz',
            'store_email' => 'hello@example.com',
            'site_timezone' => 'Africa/Lagos',
            'site_date_format' => 'M d, Y',
            'site_time_format' => 'h:i A',
            'site_default_language' => 'en',
            'currency_code' => 'NGN',
            'currency_position' => 'left',
            'decimal_places' => 2,
            'shipping_flat_rate' => 2500,
            'tax_rate' => 0,
        ], $overrides);
    }

    private function enablePaystack(): void
    {
        Setting::updateOrCreate(
            ['key' => 'paystack_public_key'],
            ['group' => 'payments', 'type' => 'string', 'value' => 'pk_test_'.str_repeat('b', 32)]
        );
        Setting::updateOrCreate(
            ['key' => 'paystack_secret_key'],
            ['group' => 'payments', 'type' => 'secret', 'value' => 'sk_test_'.str_repeat('b', 32)]
        );
        Setting::updateOrCreate(
            ['key' => 'paystack_enabled'],
            ['group' => 'payments', 'type' => 'boolean', 'value' => '1']
        );
        Setting::updateOrCreate(
            ['key' => 'online_payment_enabled'],
            ['group' => 'payments', 'type' => 'boolean', 'value' => '1']
        );

        cache()->forget('store_settings');
    }
}
