<?php
declare(strict_types=1);

namespace Application\UseCase\GenerateLicense;

use Application\Gateway\LicenseGatewayInterface;
use Application\Gateway\LicenseGatewayRequest;
use Domain\Licenses\Factory\LicenseFactoryInterface;
use Domain\Licenses\Repository\LicenseRepositoryInterface;

class GenerateLicenseUseCase
{
    public function __construct(
        private readonly LicenseFactoryInterface $licenseFactory,
        private readonly LicenseRepositoryInterface $licenseRepository,
        private readonly LicenseGatewayInterface $licenseGateway,
    )
    {
    }

    public function __invoke(GenerateLicenseRequest $request): GenerateLicenseResponse
    {
        // Сгенерировать запрос в Сервис для генерации серийных номеров
        $licenseGatewayRequest = new LicenseGatewayRequest($request->userId, $request->serviceId, $request->createDate, $request->period);
        $licenseGatewayResponse = $this->licenseGateway->sendLead($licenseGatewayRequest);

        // Создать лицензию
        $license = $this->licenseFactory->create($request->userId, $request->serviceId, $request->createDate, $request->period, $licenseGatewayResponse->serialNumber);

        // Сохранить лицензию в БД
        $this->licenseRepository->save($license);

        // Вернуть результат
        return new GenerateLicenseResponse(
            $license->getId(),
            $license->getSerialNumber()->getValue(),
        );
    }
}