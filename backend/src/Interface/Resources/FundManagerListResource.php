<?php

namespace FMS\Interface\Resources;

use FMS\Core\Entities\FundManagerEntity;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FundManagerListResource extends JsonResource
{
    /**
     * @param Request $request
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        /** @var FundManagerEntity $fundManager */
        $fundManager = $this->resource;

        return [
            'id' => $fundManager->getId(),
            'name' => $fundManager->getName(),
            'created_at' => $fundManager->getCreatedAt()?->format(DATE_ATOM),
            'updated_at' => $fundManager->getUpdatedAt()?->format(DATE_ATOM),
        ];
    }
}
