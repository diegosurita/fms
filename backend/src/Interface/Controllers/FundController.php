<?php

namespace FMS\Interface\Controllers;

use App\Http\Controllers\Controller;
use FMS\Interface\Resources\FundListResource;
use FMS\Core\UseCases\ListFundsUseCase;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class FundController extends Controller
{
    public function list(ListFundsUseCase $listFundsUseCase): AnonymousResourceCollection
    {
        return FundListResource::collection($listFundsUseCase->execute());
    }
}
