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
            'createdAt' => $company->getCreatedAt()?->format(DATE_ATOM),
            'updatedAt' => $company->getUpdatedAt()?->format(DATE_ATOM),
        ];
    }
}
