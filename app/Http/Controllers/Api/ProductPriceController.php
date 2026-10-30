<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\StoreProductPriceRequest;
use App\Http\Resources\ProductPriceResource;
use App\Models\Currency;
use App\Models\Product;
use App\Models\ProductPrice;
use Illuminate\Database\QueryException;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

class ProductPriceController extends BaseApiController
{
    /**
     * Obtener todos los precios fijados para un producto en diferentes divisas.
     */
    public function index(int $productId): JsonResponse
    {
        $product = Product::find($productId);

        if (!$product) {
            return $this->errorResponse(
                'Producto no encontrado.',
                Response::HTTP_NOT_FOUND
            );
        }

        $prices = ProductPrice::with('currency')
            ->where('product_id', $productId)
            ->get();

        return $this->successResponse(
            ProductPriceResource::collection($prices),
            'Precios del producto obtenidos exitosamente.'
        );
    }

    /**
     * Registrar un nuevo precio para un producto en una divisa específica.
     */
    public function store(StoreProductPriceRequest $request, int $productId): JsonResponse
    {
        $product = Product::find($productId);

        if (!$product) {
            return $this->errorResponse(
                'Producto no encontrado.',
                Response::HTTP_NOT_FOUND
            );
        }

        try {
            $productPrice = DB::transaction(function () use ($request, $productId) {
                return ProductPrice::create([
                    'product_id' => $productId,
                    'currency_id' => $request->validated('currency_id'),
                    'price' => $request->validated('price'),
                ]);
            });

            $productPrice->load('currency');

            return $this->successResponse(
                new ProductPriceResource($productPrice),
                'Precio registrado exitosamente para la divisa.',
                Response::HTTP_CREATED
            );
        } catch (QueryException $e) {
            // Manejo de seguridad en caso de colisión de concurrencia en la restricción UNIQUE
            if ($e->getCode() === '23000') {
                return $this->errorResponse(
                    'Conflicto: ya existe un precio registrado para este producto y divisa.',
                    Response::HTTP_CONFLICT
                );
            }

            throw $e;
        }
    }
}
