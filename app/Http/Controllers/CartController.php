<?php
namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Product;
use Illuminate\Http\Request;

class CartController extends Controller
{
    private function getCart(): Cart
    {
        if (auth('web')->check()) {
            $cart = Cart::firstOrCreate(
                ['user_id' => auth('web')->id()],
                ['session_id' => null]
            );

            $sessionId = session()->getId();
            $guestCart = Cart::where('session_id', $sessionId)
                             ->whereNull('user_id')
                             ->first();

            if ($guestCart && $guestCart->id !== $cart->id) {
                foreach ($guestCart->items as $guestItem) {
                    $existing = $cart->items()->where('product_id', $guestItem->product_id)->first();
                    if ($existing) {
                        $existing->update([
                            'quantity' => min(
                                $existing->quantity + $guestItem->quantity,
                                $guestItem->product->stock ?? 9999
                            )
                        ]);
                    } else {
                        $cart->items()->create([
                            'product_id'       => $guestItem->product_id,
                            'quantity'         => $guestItem->quantity,
                            'price'            => $guestItem->price,
                            'selected_options' => $guestItem->selected_options,
                        ]);
                    }
                }
                $guestCart->delete();
            }

            return $cart;
        }

        $sessionId = session()->getId();
        return Cart::firstOrCreate(
            ['session_id' => $sessionId, 'user_id' => null]
        );
    }

    // ── Add ───────────────────────────────────────────────────
    public function add(Request $request)
    {
        $request->validate([
            'product_id'       => ['required', 'exists:products,id'],
            'quantity'         => ['required', 'integer', 'min:1'],
            'selected_options' => ['nullable', 'array'],
        ]);

        $product = Product::with('options.values')->findOrFail($request->product_id);

        // ── AUTO-SELECT FIRST OPTION FOR ANY MISSING OPTIONS ───────────────────
        $submittedOptions = collect($request->selected_options ?? [])->keyBy('option_id');
        $autoSelectedOptions = [];

        if ($product->options->isNotEmpty()) {
            foreach ($product->options as $option) {
                if (!$submittedOptions->has($option->id)) {
                    // Auto-select the first value of this option
                    $firstValue = $option->values->first();
                    if ($firstValue) {
                        $autoSelectedOptions[] = [
                            'option_id'   => $option->id,
                            'option_name' => $option->name,
                            'value_id'    => $firstValue->id,
                            'value'       => $firstValue->value,
                            'image_url'   => $firstValue->image_url ?? null,
                        ];
                    }
                }
            }
        }

        // Merge user submitted options with auto-selected ones
        $allSelectedOptions = collect($request->selected_options ?? [])
            ->concat($autoSelectedOptions)
            ->unique(function ($item) {
                return $item['option_id'] ?? null;
            })
            ->values()
            ->toArray();

        // ── Build a clean snapshot of selections ─────────────────────────
        $selectedOptions = null;
        if ($product->options->isNotEmpty() && !empty($allSelectedOptions)) {
            $selectedOptions = collect($allSelectedOptions)->map(function ($sel) use ($product) {
                $option = $product->options->firstWhere('id', $sel['option_id'] ?? null);
                $val    = $option?->values->firstWhere('id', $sel['value_id'] ?? null);

                return [
                    'option_id'   => $sel['option_id']   ?? null,
                    'option_name' => $option?->name       ?? ($sel['option_name'] ?? ''),
                    'value_id'    => $sel['value_id']     ?? null,
                    'value'       => $val?->value          ?? ($sel['value'] ?? ''),
                    'image_url'   => $val?->image_url      ?? null,
                ];
            })->values()->toArray();
        }

        // ── Flash sale price check ────────────────────────────────────────
        $price = $product->current_price;

        if ($request->boolean('flash_sale')) {
            $flashSale = \App\Models\FlashSale::where('product_id', $product->id)
                ->where('is_active', true)
                ->where('starts_at', '<=', now())
                ->where('ends_at', '>=', now())
                ->where(function ($q) {
                    $q->whereNull('quantity_limit')
                      ->orWhereColumn('quantity_sold', '<', 'quantity_limit');
                })
                ->first();

            if ($flashSale) {
                $price = $flashSale->sale_price;
            }
        }

        $cart = $this->getCart();
        
        // FIX: Create a unique hash from the selected options to identify if same product with SAME options
        $optionsHash = $selectedOptions ? md5(json_encode($selectedOptions)) : 'no_options';
        
        // Find existing cart item with SAME product AND SAME options
        $item = $cart->items()
            ->where('product_id', $product->id)
            ->get()
            ->first(function ($cartItem) use ($selectedOptions, $optionsHash) {
                $cartItemHash = $cartItem->selected_options ? md5(json_encode($cartItem->selected_options)) : 'no_options';
                return $cartItemHash === $optionsHash;
            });

        if ($item) {
            $newQty = $item->quantity + $request->quantity;
            if ($newQty > $product->stock) {
                return response()->json(['success' => false, 'message' => 'Not enough stock.']);
            }
            // Update quantity and refresh the option snapshot
            $item->update([
                'quantity'         => $newQty,
                'price'            => $price,
                'selected_options' => $selectedOptions,
            ]);
        } else {
            if ($request->quantity > $product->stock) {
                return response()->json(['success' => false, 'message' => 'Not enough stock.']);
            }
            // Create NEW cart item (different options or first time)
            $cart->items()->create([
                'product_id'       => $product->id,
                'quantity'         => $request->quantity,
                'price'            => $price,
                'selected_options' => $selectedOptions,
            ]);
        }

        // Return the auto-selected options in response so frontend can update
        return response()->json([
            'success' => true, 
            'message' => 'Added to cart.',
            'auto_selected_options' => $autoSelectedOptions
        ]);
    }

    // ── Remove ────────────────────────────────────────────────
    public function remove(Request $request)
    {
        $request->validate(['product_id' => ['required']]);

        $cart = $this->getCart();
        $cart->items()->where('product_id', $request->product_id)->delete();

        $count = $cart->items()->sum('quantity');

        if ($request->expectsJson()) {
            return response()->json(['success' => true, 'count' => $count]);
        }

        return back()->with('success', 'Removed from cart.');
    }

    // ── Update ────────────────────────────────────────────────
    public function update(Request $request)
    {
        $request->validate([
            'product_id' => ['required'],
            'quantity'   => ['required', 'integer', 'min:1'],
        ]);

        $cart = $this->getCart();
        $cart->items()
             ->where('product_id', $request->product_id)
             ->update(['quantity' => $request->quantity]);

        return response()->json(['success' => true]);
    }

    // ── Clear ─────────────────────────────────────────────────
    public function clear()
    {
        $this->getCart()->items()->delete();
        return back()->with('success', 'Cart cleared.');
    }

    // ── View ──────────────────────────────────────────────────
    public function index()
    {
        $cart  = $this->getCart();
        $items = $cart->items()->with(['product.images'])->get();

        return view('storefront.cart', compact('cart', 'items'));
    }

    // ── Cart count ────────────────────────────────────────────
    public function count()
    {
        $cart  = $this->getCart();
        $count = $cart->items()->sum('quantity');

        return response()->json(['count' => $count]);
    }

    // ── Cart sidebar data ─────────────────────────────────────
    public function sidebar()
    {
        $cart  = $this->getCart();
        $items = $cart->items()->with(['product.images'])->get();

        $subtotal = $items->sum(fn($item) => $item->price * $item->quantity);

        $data = $items->map(function ($item) {
            $product    = $item->product;
            $primaryImg = $product?->images->where('is_primary', true)->first()
                          ?? $product?->images->first();

            return [
                'product_id'       => $item->product_id,
                'name'             => $product?->name ?? 'Product',
                'price'            => (float) $item->price,
                'quantity'         => $item->quantity,
                'subtotal'         => (float) ($item->price * $item->quantity),
                'image'            => $primaryImg?->image_url ?? null,
                'selected_options' => $item->selected_options ?? [],
            ];
        });

        return response()->json([
            'items'    => $data,
            'subtotal' => $subtotal,
            'total'    => $subtotal,
            'count'    => $items->sum('quantity'),
        ]);
    }

    public function mergeGuestCart(string $sessionId): void
    {
        if (!auth('web')->check()) return;

        $guestCart = Cart::where('session_id', $sessionId)
                         ->whereNull('user_id')
                         ->first();

        if (!$guestCart) return;

        $userCart = Cart::firstOrCreate(
            ['user_id' => auth('web')->id()],
            ['session_id' => null]
        );

        if ($guestCart->id === $userCart->id) return;

        foreach ($guestCart->items as $guestItem) {
            $existing = $userCart->items()
                                 ->where('product_id', $guestItem->product_id)
                                 ->first();

            if ($existing) {
                $existing->update([
                    'quantity' => min(
                        $existing->quantity + $guestItem->quantity,
                        $guestItem->product->stock ?? 9999
                    ),
                ]);
            } else {
                $userCart->items()->create([
                    'product_id'       => $guestItem->product_id,
                    'quantity'         => $guestItem->quantity,
                    'price'            => $guestItem->price,
                    'selected_options' => $guestItem->selected_options,
                ]);
            }
        }

        $guestCart->delete();
    }
}