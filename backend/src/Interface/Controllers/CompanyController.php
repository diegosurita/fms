<?php

namespace FMS\Interface\Controllers;

use App\Http\Controllers\Controller;
use FMS\Core\UseCases\ListCompaniesUseCase;
use FMS\Interface\Resources\CompanyListResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class CompanyController extends Controller
{
    public function list(Request $request, ListCompaniesUseCase $listCompaniesUseCase): AnonymousResourceCollection
    {
        $filter = trim((string) $request->query('filter', ''));

        return CompanyListResource::collection($listCompaniesUseCase->execute($filter === '' ? null : $filter));
    }
}
