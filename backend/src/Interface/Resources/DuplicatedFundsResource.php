<?php

namespace FMS\Interface\Resources;

use FMS\Core\DataTransferObjects\DuplicatedFundsDTO;
use FMS\Core\Entities\FundEntity;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DuplicatedFundsResource extends JsonResource
{
    /**
     * @param Request $request
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        /** @var DuplicatedFundsDTO $duplicatedFunds */
        $duplicatedFunds = $this->resource;

        return [
            'source' => $this->mapFund($duplicatedFunds->source),
            'duplicated' => $this->mapFund($duplicatedFunds->duplicated),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function mapFund(FundEntity $fund): array
    {
        return [
            'id' => $fund->getId(),
            'name' => $fund->getName(),
            'startYear' => $fund->getStartYear(),
            'managerId' => $fund->getManagerId(),
            'createdAt' => $fund->getCreatedAt()?->format(DATE_ATOM),
            'updatedAt' => $fund->getUpdatedAt()?->format(DATE_ATOM),
        ];
    }
}