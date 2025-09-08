<?php

namespace App\Http\Controllers;

use App\Services\FakeStoreService;
use Illuminate\Http\Request;
use App\Models\Product;

class ProductController extends Controller
{
    private $fakeStoreService;
    
    public function __construct(FakeStoreService $fakeStoreService)
    {
        $this->fakeStoreService = $fakeStoreService;
    }
    
    public function index(Request $request)
    {
        $perPage = $request->integer('per_page', 10);
        $products = Product::orderBy('name')
            ->paginate($perPage)
            ->withQueryString();

        return view('products.index', compact('products'));
    }

    public function updateStock(Request $request, Product $product)
    {

        $request->validate([
            'stock' => 'required|integer|min:0'
        ], [
            'stock.required' => 'O estoque é obrigatório',
            'stock.integer' => 'O estoque deve ser um número inteiro',
            'stock.min' => 'O estoque não pode ser negativo'
        ]);

        $product->update([
            'stock' => $request->stock
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Estoque atualizado com sucesso!',
            'estoque' => $product->stock
        ]);
    }

    public function syncWithApi()
    {
        $result = $this->fakeStoreService->syncProducts();
        return redirect()->route('products.index')->with('success', $result['message']);
    }

}
