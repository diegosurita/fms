<?php

namespace FMS\Interface\Controllers;

use App\Http\Controllers\Controller;
use FMS\Core\DataTransferObjects\SaveFundManagerDTO;
use FMS\Core\UseCases\CreateFundManagerUseCase;
use FMS\Core\UseCases\DeleteFundManagerUseCase;
use FMS\Core\UseCases\ListFundManagersUseCase;
use FMS\Core\UseCases\UpdateFundManagerUseCase;
use FMS\Interface\Resources\FundManagerListResource;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Resources\Json\JsonResource;

class FundManagerController extends Controller
{
    public function list(ListFundManagersUseCase $listFundManagersUseCase): AnonymousResourceCollection
    {
        return FundManagerListResource::collection($listFundManagersUseCase->execute());
    }

    public function create(Request $request, CreateFundManagerUseCase $createFundManagerUseCase): JsonResource
    {
        $validated = $request->validate([
            'name' => ['required', 'string'],
        ]);

        return new FundManagerListResource($createFundManagerUseCase->execute(new SaveFundManagerDTO(
            (string) $validated['name'],
        )));
    }

    public function update(Request $request, int $id, UpdateFundManagerUseCase $updateFundManagerUseCase): JsonResource
    {
        $validated = $request->validate([
            'name' => ['required', 'string'],
        ]);

        return new FundManagerListResource($updateFundManagerUseCase->execute(new SaveFundManagerDTO(
            (string) $validated['name'],
            $id,
        )));
    }

    public function delete(int $id, DeleteFundManagerUseCase $deleteFundManagerUseCase): Response
    {
        $deleteFundManagerUseCase->execute($id);

        return response()->noContent();
    }
}
