<?php

namespace FMS\Interface\Controllers;

use App\Http\Controllers\Controller;
use FMS\Core\DataTransferObjects\SaveFundDTO;
use FMS\Core\UseCases\CreateFundUseCase;
use FMS\Core\UseCases\GetDuplicatedFundsUseCase;
use FMS\Interface\Resources\DuplicatedFundsResource;
use FMS\Interface\Resources\FundListResource;
use FMS\Core\UseCases\DeleteFundUseCase;
use FMS\Core\UseCases\ListFundsUseCase;
use FMS\Core\UseCases\UpdateFundUseCase;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class FundController extends Controller
{
    public function list(Request $request, ListFundsUseCase $listFundsUseCase): AnonymousResourceCollection
    {
        $filter = trim((string) $request->query('filter', ''));

        return FundListResource::collection($listFundsUseCase->execute($filter === '' ? null : $filter));
    }

    public function listDuplicated(GetDuplicatedFundsUseCase $getDuplicatedFundsUseCase): JsonResponse
    {
        return DuplicatedFundsResource::collection($getDuplicatedFundsUseCase->execute())->response();
    }

    public function create(Request $request, CreateFundUseCase $createFundUseCase): JsonResource
    {
        // TODO: Add idempotency key to avoid creating multiple funds if the request is retried
        $validated = $request->validate([
            'name' => ['required', 'string'],
            'start_year' => ['required', 'integer'],
            'manager_id' => ['required', 'integer'],
            'aliases' => ['sometimes', 'array'],
            'aliases.*' => ['string'],
            'companies' => ['sometimes', 'array'],
            'companies.*' => ['integer'],
        ]);

        $aliases = array_values(array_map(
            static fn (mixed $alias): string => (string) $alias,
            (array) ($validated['aliases'] ?? []),
        ));

        $companies = array_values(array_map(
            static fn (mixed $companyId): int => (int) $companyId,
            (array) ($validated['companies'] ?? []),
        ));

        return new FundListResource($createFundUseCase->execute(new SaveFundDTO(
            (string) $validated['name'],
            (int) $validated['start_year'],
            (int) $validated['manager_id'],
            aliases: $aliases,
            companies: $companies,
        )));
    }

    public function update(Request $request, int $id, UpdateFundUseCase $updateFundUseCase): JsonResource
    {
        $validated = $request->validate([
            'name' => ['required', 'string'],
            'start_year' => ['required', 'integer'],
            'manager_id' => ['required', 'integer'],
            'aliases' => ['sometimes', 'array'],
            'aliases.*' => ['string'],
            'companies' => ['sometimes', 'array'],
            'companies.*' => ['integer'],
        ]);

        $aliases = array_values(array_map(
            static fn (mixed $alias): string => (string) $alias,
            (array) ($validated['aliases'] ?? []),
        ));

        $companies = array_values(array_map(
            static fn (mixed $companyId): int => (int) $companyId,
            (array) ($validated['companies'] ?? []),
        ));

        return new FundListResource($updateFundUseCase->execute(new SaveFundDTO(
            (string) $validated['name'],
            (int) $validated['start_year'],
            (int) $validated['manager_id'],
            id: $id,
            aliases: $aliases,
            companies: $companies,
        )));
    }

    public function delete(int $id, DeleteFundUseCase $deleteFundUseCase): Response
    {
        $deleteFundUseCase->execute($id);

        return response()->noContent();
    }
}
