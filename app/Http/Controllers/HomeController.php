<?php

namespace App\Http\Controllers;

use App\Models\Product;

class HomeController extends Controller
{
    public function index()
    {
        $products = Product::with('category')->get(['id', 'name', 'price', 'slug']);

        $cartTotal = \Cart::getTotal();
        $cartCount = \Cart::getContent()->count();

        return view('frontend.homepage', compact('products', 'cartTotal', 'cartCount'));
    }

    public function getProducts()
    {
        $products = Product::with('category')->get(['id', 'name', 'price', 'slug']);

        foreach ($products as $product) {
            $galleryImages = [];
            foreach ($product->gallery as $image) {
                $galleryImages[] = $image->original_url;
            }
            $product->gallery_images = $galleryImages;

            $mediaImages = [];
            foreach ($product->media as $image) {
                $mediaImages[] = $image->original_url;
            }
            $product->media_images = $mediaImages;
        }

        return response()->json([
            'status' => 200,
            'products' => $products
        ]);
    }
}