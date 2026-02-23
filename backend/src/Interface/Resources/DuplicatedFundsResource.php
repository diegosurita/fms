<?php

namespace FMS\Interface\Resources;

use FMS\Core\DataTransferObjects\DuplicatedFundsDTO;
use FMS\Core\Entities\FundEntity;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\DB;

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
        $fundId = $fund->getId();
        $managerId = $fund->getManagerId();

        $aliases = [];

        if ($fundId !== null) {
            $aliases = DB::table('fund_aliases')
                ->where('fund', $fundId)
                ->pluck('alias')
                ->map(static fn (mixed $alias): string => (string) $alias)
                ->all();
        }

        $managerName = DB::table('fund_managers')
            ->where('id', $managerId)
            ->value('name');

        return [
            'id' => $fundId,
            'name' => $fund->getName(),
            'startYear' => $fund->getStartYear(),
            'managerId' => $managerId,
            'managerName' => $managerName !== null ? (string) $managerName : null,
            'aliases' => $aliases,
            'createdAt' => $fund->getCreatedAt()?->format(DATE_ATOM),
            'updatedAt' => $fund->getUpdatedAt()?->format(DATE_ATOM),
        ];
    }
}