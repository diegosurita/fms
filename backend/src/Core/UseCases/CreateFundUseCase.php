<?php

namespace FMS\Core\UseCases;

use FMS\Core\Contracts\CompanyRepository;
use FMS\Core\Contracts\EventDispatcherInterface;
use FMS\Core\Contracts\FundRepository;
use FMS\Core\DataTransferObjects\SaveFundDTO;
use FMS\Core\Entities\FundEntity;
use FMS\Core\Events\FundCreatedEvent;

class CreateFundUseCase
{
    public function __construct(
        private readonly FundRepository $fundRepository,
        private readonly CompanyRepository $companyRepository,
        private readonly EventDispatcherInterface $eventDispatcher,
    ) {
    }

    public function execute(SaveFundDTO $saveFundDTO): FundEntity
    {
        try {
            $this->fundRepository->startTransaction();

            $fund = $this->fundRepository->create($saveFundDTO);
            
            if (!empty($saveFundDTO->companies)) {
                $fundId = $fund->getId();

                if ($fundId === null) {
                    throw new \RuntimeException('Fund id is required to sync fund companies.');
                }
    
                $this->companyRepository->syncFundCompanies(
                    $fundId,
                    $this->normalizeCompanies($saveFundDTO->companies),
                );
            }

            $this->fundRepository->commitTransaction();
            
            $this->eventDispatcher->dispatch(new FundCreatedEvent($fund));

            return $fund;
        } catch (\InvalidArgumentException $exception) {
            $this->fundRepository->rollbackTransaction();

            throw $exception;
        } catch (\Throwable $exception) {
            $this->fundRepository->rollbackTransaction();

            throw new \RuntimeException('Failed to create fund.', 0, $exception);
        }
    }

    /**
     * @param int[] $companies
     *
     * @return int[]
     */
    private function normalizeCompanies(array $companies): array
    {
        return array_values(array_unique(array_filter(array_map(
            static fn (mixed $companyId): int => (int) $companyId,
            $companies,
        ), static fn (int $companyId): bool => $companyId > 0)));
    }
}
