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
        $perPage  = $request->integer('per_page', 10);
        $category = $request->query('category');   // ?category=Electronics
        $q        = $request->query('q');          // ?q=texto (opcional)

        $hasAnyProducts = Product::query()->exists();

        $categories = Product::query()
            ->select('category')
            ->whereNotNull('category')
            ->distinct()
            ->orderBy('category')
            ->pluck('category');


        $products = Product::query()
            ->when($category, fn($qb) => $qb->where('category', $category))
            ->when($q,        fn($qb) => $qb->where('name', 'like', "%{$q}%"))
            ->orderBy('name')
            ->paginate($perPage)
            ->appends($request->only('category','q','per_page')); // preserva filtros na paginação

        return view('products.index', compact('products', 'categories', 'category', 'q', 'perPage', 'hasAnyProducts'));

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

    public function deleteStock(string $productId){

        
        if( empty($productId) || (int)$productId < 0 ){
            return response()->json([
                'success' => false,
                'message' => 'ID de produto inválido'
            ], 400);
        }
        
        $product = Product::find($productId);
        
        if (!$product) {
            return response()->json(['success' => false, 'message' => 'Produto não encontrado'], 404);
        }
   
        $product->delete();
         
        return response()->json([
            'success' => true,
            'message' => 'Estoque removido com sucesso!',
        ], 200);
    }   
}
