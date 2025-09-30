<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Repositories\CartRepository;
use App\Http\Resources\Api\CartResource;
use App\Http\Resources\Api\CartItemResource;
use App\Http\Resources\Api\OrderResource;

class CartController extends Controller
{
    protected $cartRepo;

    public function __construct(CartRepository $cartRepo)
    {
        $this->cartRepo = $cartRepo;
    }

    public function index(Request $request)
    {
        $cart = $this->cartRepo->getUserCart($request->user()->id);
        return $this->sendResponse('User cart retrieved' ,new CartResource($cart->load('items.variant.product')), 200);
    }

    public function add(Request $request)
    {
        $request->validate([
            'product_variant_id' => 'required|exists:product_variants,id',
            'quantity' => 'required|integer|min:1',
        ]);

        $item = $this->cartRepo->addItem(
            $request->user()->id,
            $request->product_variant_id,
            $request->quantity
        );
        return $this->sendResponse('Item added to cart' ,new CartItemResource($item), 200);
      
    }

    public function update(Request $request, $itemId)
    {
        $request->validate([
            'added_quantity' => 'required|integer|min:1',
        ]);

        $item = $this->cartRepo->updateItem($itemId, $request->added_quantity);
        return $this->sendResponse('Item updated successfully' ,new CartItemResource($item), 200);
    }

    public function remove($itemId)
    {
        $this->cartRepo->removeItem($itemId);
        return response()->json(['message' => 'Item removed']);
    }

    public function clear(Request $request)
    {
        try {
            $userId = $request->user()->id;

            $this->cartRepo->clearCart($userId);

            return response()->json([
                'status'  => true,
                'message' => 'Cart cleared successfully',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status'  => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }


    public function checkout(Request $request)
    {
        $request->validate([
            'coupon_code' => 'nullable|string|exists:coupons,code',
        ]);

        try {
            $order = $this->cartRepo->checkout($request->user()->id, $request->coupon_code ?? null);
           return $this->sendResponse('Cart Checkedout successfully' ,new OrderResource($order), 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }
}