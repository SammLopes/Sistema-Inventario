<?php

namespace App\Services;

use App\Models\Product;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Arr;

class FakeStoreService 
{

    private $baseUrlGetAllProducts = 'https://fakestoreapi.com/products';


    public function syncProducts(){

        $response = Http::get($this->baseUrlGetAllProducts);
            
        if (!$response->successful()) {
            throw new \Exception('Erro ao consultar API: ' . $response->status());
        }

        $apiProducts = $response->json();
        $apiIds = collect($apiProducts)->pluck('id');

        // Contadores
        $created = 0;
        $updated = 0;
        $deleted = 0;

        // Criar/atualizar produtos da API
        foreach ($apiProducts as $apiProduct) {
            $product = Product::where('api_id', $apiProduct['id'])->first();

            $productData = [
                'name' => $apiProduct['title'],
                'price' => $apiProduct['price'],
                'category' => $apiProduct['category'],
                'description' => $apiProduct['description'],
                'image' => $apiProduct['image'],
                'api_id' => $apiProduct['id']
            ];

            if ($product) {

                $product->update(Arr::except($productData, ['stock']));
                $updated++;

            } else {

                Product::create(array_merge($productData, ['stock' => 0]));
                $created++;
            }
        }

        // Remover produtos que não existem mais na API
        $deletedProducts = Product::whereNotNull('api_id')
                                ->whereNotIn('api_id', $apiIds)
                                ->get();
        
        foreach ($deletedProducts as $product) {
            $product->delete();
            $deleted++;
        }

        Log::info("Sincronização concluída: {$created} criados, {$updated} atualizados, {$deleted} removidos");

        return [
            'success' => true,
            'message' => "Sincronização concluída! {$created} produtos criados, {$updated} atualizados e {$deleted} removidos.",
            'created' => $created,
            'updated' => $updated,
            'deleted' => $deleted
        ];
    }
}