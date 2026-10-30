<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\StoreCurrencyRequest;
use App\Http\Requests\UpdateCurrencyRequest;
use App\Http\Resources\CurrencyResource;
use App\Models\Currency;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

class CurrencyController extends BaseApiController
{
    /**
     * Listado de todas las divisas registradas.
     */
    public function index(): JsonResponse
    {
        $currencies = Currency::orderBy('name', 'asc')->get();

        return $this->successResponse(
            CurrencyResource::collection($currencies),
            'Listado de divisas obtenido correctamente.'
        );
    }

    /**
     * Registrar una nueva divisa.
     */
    public function store(StoreCurrencyRequest $request): JsonResponse
    {
        $currency = Currency::create($request->validated());

        return $this->successResponse(
            new CurrencyResource($currency),
            'Divisa creada exitosamente.',
            Response::HTTP_CREATED
        );
    }

    /**
     * Consultar una divisa por su ID.
     */
    public function show(int $id): JsonResponse
    {
        $currency = Currency::find($id);

        if (!$currency) {
            return $this->errorResponse(
                'Divisa no encontrada.',
                Response::HTTP_NOT_FOUND
            );
        }

        return $this->successResponse(
            new CurrencyResource($currency),
            'Detalle de la divisa obtenido correctamente.'
        );
    }

    /**
     * Actualizar los datos o la tasa de cambio de una divisa.
     */
    public function update(UpdateCurrencyRequest $request, int $id): JsonResponse
    {
        $currency = Currency::find($id);

        if (!$currency) {
            return $this->errorResponse(
                'Divisa no encontrada.',
                Response::HTTP_NOT_FOUND
            );
        }

        $currency->update($request->validated());

        return $this->successResponse(
            new CurrencyResource($currency),
            'Divisa actualizada correctamente.'
        );
    }
}
