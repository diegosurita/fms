<?php

namespace FMS\Interface\Controllers;

use App\Http\Controllers\Controller;
use FMS\Core\DataTransferObjects\SaveCompanyDTO;
use FMS\Core\UseCases\CreateCompanyUseCase;
use FMS\Core\UseCases\ListCompaniesUseCase;
use FMS\Interface\Resources\CompanyListResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Resources\Json\JsonResource;

class CompanyController extends Controller
{
    public function list(Request $request, ListCompaniesUseCase $listCompaniesUseCase): AnonymousResourceCollection
    {
        $filter = trim((string) $request->query('filter', ''));

        return CompanyListResource::collection($listCompaniesUseCase->execute($filter === '' ? null : $filter));
    }

    public function create(Request $request, CreateCompanyUseCase $createCompanyUseCase): JsonResource
    {
        // TODO: Add idempotency key to avoid creating multiple funds if the request is retried
        $validated = $request->validate([
            'name' => ['required', 'string'],
        ]);

        return new CompanyListResource($createCompanyUseCase->execute(new SaveCompanyDTO(
            (string) $validated['name'],
        )));
    }
}
