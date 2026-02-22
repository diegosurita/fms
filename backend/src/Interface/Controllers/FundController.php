<?php

namespace FMS\Interface\Controllers;

use App\Http\Controllers\Controller;
use FMS\Core\DataTransferObjects\CreateFundDTO;
use FMS\Core\UseCases\CreateFundUseCase;
use FMS\Interface\Resources\FundListResource;
use FMS\Core\UseCases\ListFundsUseCase;
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

        return new FundListResource($createFundUseCase->execute(new CreateFundDTO(
            (string) $validated['name'],
            (int) $validated['startYear'],
            (int) $validated['managerId'],
        )));
    }
}
