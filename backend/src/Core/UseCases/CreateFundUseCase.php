<?php

namespace FMS\Core\UseCases;

use FMS\Core\Contracts\EventDispatcherInterface;
use FMS\Core\Contracts\FundRepository;
use FMS\Core\DataTransferObjects\SaveFundDTO;
use FMS\Core\Entities\FundEntity;
use FMS\Core\Events\FundCreatedEvent;

class CreateFundUseCase
{
    public function __construct(
        private readonly FundRepository $fundRepository,
        private readonly EventDispatcherInterface $eventDispatcher,
    ) {
    }

    public function execute(SaveFundDTO $saveFundDTO): FundEntity
    {
        // TODO: add the logic to create companies and aliases when creating a fund
        try {
            $fund = $this->fundRepository->create($saveFundDTO);

            $this->eventDispatcher->dispatch(new FundCreatedEvent($fund));

            return $fund;
        } catch (\InvalidArgumentException $exception) {
            throw $exception;
        } catch (\Throwable $exception) {
            throw new \RuntimeException('Failed to create fund.', 0, $exception);
        }
    }
}
