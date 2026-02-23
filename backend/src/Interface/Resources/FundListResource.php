<?php

namespace FMS\Interface\Resources;

use FMS\Core\Entities\FundEntity;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FundListResource extends JsonResource
{
    /**
     * @param Request $request
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        /** @var FundEntity $fund */
        $fund = $this->resource;

        return [
            'id' => $fund->getId(),
            'name' => $fund->getName(),
            'start_year' => $fund->getStartYear(),
            'manager_id' => $fund->getManagerId(),
            'aliases' => $fund->getAliases(),
            'companies' => $fund->getCompanies(),
            'created_at' => $fund->getCreatedAt()?->format(DATE_ATOM),
            'updated_at' => $fund->getUpdatedAt()?->format(DATE_ATOM),
        ];
    }
}
