<?php

namespace FMS\Interface\Controllers;

use App\Http\Controllers\Controller;
use FMS\Interface\Resources\FundListResource;
use FMS\Core\UseCases\ListFundsUseCase;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class FundController extends Controller
{
    public function list(Request $request, ListFundsUseCase $listFundsUseCase): AnonymousResourceCollection
    {
        $filter = trim((string) $request->query('filter', ''));

        return FundListResource::collection($listFundsUseCase->execute($filter === '' ? null : $filter));
    }
}
