<?php

namespace FMS\Interface\Controllers;

use App\Http\Controllers\Controller;
use FMS\Core\DataTransferObjects\SaveFundDTO;
use FMS\Core\UseCases\CreateFundUseCase;
use FMS\Interface\Resources\FundListResource;
use FMS\Core\UseCases\ListFundsUseCase;
use FMS\Core\UseCases\UpdateFundUseCase;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class FundController extends Controller
{
    public function list(Request $request, ListFundsUseCase $listFundsUseCase): AnonymousResourceCollection
    {
        $filter = trim((string) $request->query('filter', ''));

        return FundListResource::collection($listFundsUseCase->execute($filter === '' ? null : $filter));
    }

    public function create(Request $request, CreateFundUseCase $createFundUseCase): JsonResource
    {
        // TODO: Add idempotency key to avoid creating multiple funds if the request is retried
        $validated = $request->validate([
            'name' => ['required', 'string'],
            'startYear' => ['required', 'integer'],
            'managerId' => ['required', 'integer'],
        ]);

        return new FundListResource($createFundUseCase->execute(new SaveFundDTO(
            (string) $validated['name'],
            (int) $validated['startYear'],
            (int) $validated['managerId'],
        )));
    }

    public function update(Request $request, int $id, UpdateFundUseCase $updateFundUseCase): JsonResource
    {
        $validated = $request->validate([
            'name' => ['required', 'string'],
            'startYear' => ['required', 'integer'],
            'managerId' => ['required', 'integer'],
        ]);

        return new FundListResource($updateFundUseCase->execute(new SaveFundDTO(
            (string) $validated['name'],
            (int) $validated['startYear'],
            (int) $validated['managerId'],
            $id,
        )));
    }
}
