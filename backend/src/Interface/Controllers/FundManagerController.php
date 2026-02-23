<?php

namespace FMS\Interface\Controllers;

use App\Http\Controllers\Controller;
use FMS\Core\UseCases\ListFundManagersUseCase;
use FMS\Interface\Resources\FundManagerListResource;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class FundManagerController extends Controller
{
    public function list(ListFundManagersUseCase $listFundManagersUseCase): AnonymousResourceCollection
    {
        return FundManagerListResource::collection($listFundManagersUseCase->execute());
    }
}
