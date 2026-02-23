<?php

namespace FMS\Interface\Resources;

use FMS\Core\Entities\CompanyEntity;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CompanyListResource extends JsonResource
{
    /**
     * @param Request $request
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        /** @var CompanyEntity $company */
        $company = $this->resource;

        return [
            'id' => $company->getId(),
            'name' => $company->getName(),
            'created_at' => $company->getCreatedAt()?->format(DATE_ATOM),
            'updated_at' => $company->getUpdatedAt()?->format(DATE_ATOM),
        ];
    }
}
