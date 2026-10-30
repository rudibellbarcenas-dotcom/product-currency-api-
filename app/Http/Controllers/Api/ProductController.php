<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

class ProductController extends BaseApiController
{
    /**
     * Listado paginado de productos con prevención de problema N+1.
     */
    public function index(Request $request): JsonResponse
    {
        $perPage = min(max((int) $request->query('per_page', 15), 1), 100);

        $products = Product::with(['baseCurrency', 'prices.currency'])
            ->latest('id')
            ->paginate($perPage);

        return $this->successResponse([
            'items' => ProductResource::collection($products),
            'pagination' => [
                'total' => $products->total(),
                'per_page' => $products->perPage(),
                'current_page' => $products->currentPage(),
                'last_page' => $products->lastPage(),
                'has_more_pages' => $products->hasMorePages(),
            ],
        ], 'Listado de productos recuperado exitosamente.');
    }

    /**
     * Crear un nuevo producto en la divisa base indicada.
     */
    public function store(StoreProductRequest $request): JsonResponse
    {
        $product = DB::transaction(function () use ($request) {
            $created = Product::create($request->validated());
            $created->load('baseCurrency');
            return $created;
        });

        return $this->successResponse(
            new ProductResource($product),
            'Producto creado exitosamente.',
            Response::HTTP_CREATED
        );
    }

    /**
     * Consultar el detalle de un producto específico.
     */
    public function show(int $id): JsonResponse
    {
        $product = Product::with(['baseCurrency', 'prices.currency'])->find($id);

        if (!$product) {
            return $this->errorResponse(
                'Producto no encontrado.',
                Response::HTTP_NOT_FOUND
            );
        }

        return $this->successResponse(
            new ProductResource($product),
            'Detalle del producto recuperado exitosamente.'
        );
    }

    /**
     * Actualizar los atributos de un producto existente.
     */
    public function update(UpdateProductRequest $request, int $id): JsonResponse
    {
        $product = Product::find($id);

        if (!$product) {
            return $this->errorResponse(
                'Producto no encontrado.',
                Response::HTTP_NOT_FOUND
            );
        }

        DB::transaction(function () use ($product, $request) {
            $product->update($request->validated());
        });

        $product->load(['baseCurrency', 'prices.currency']);

        return $this->successResponse(
            new ProductResource($product),
            'Producto actualizado exitosamente.'
        );
    }

    /**
     * Eliminar un producto y mantener consistencia en registros relacionados.
     */
    public function destroy(int $id): JsonResponse
    {
        $product = Product::find($id);

        if (!$product) {
            return $this->errorResponse(
                'Producto no encontrado.',
                Response::HTTP_NOT_FOUND
            );
        }

        DB::transaction(function () use ($product) {
            // El borrado en cascada está configurado a nivel de base de datos,
            // pero la transacción garantiza consistencia atómica total.
            $product->delete();
        });

        return $this->successResponse(
            null,
            'Producto y sus precios asociados eliminados correctamente.',
            Response::HTTP_OK
        );
    }
}
