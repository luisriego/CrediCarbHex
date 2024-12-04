<?php

declare(strict_types=1);

namespace App\Tests\Functional\Controller\Condo;

use App\Tests\Functional\Controller\ControllerTestBase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class UpdateCondoControllerTest extends ControllerTestBase
{
    public function testUpdateCondo(): void
    {
        $payload = [
            'fantasyName' => 'Condomínio Matilda X',
        ];

        // create a Condo
        $condoId = $this->createCondo();
        // update a Condo
        self::$admin->request(Request::METHOD_PATCH, \sprintf(self::ENDPOINT_CONDO, $condoId), [], [], [], \json_encode($payload));
        // checks
        $response = self::$admin->getResponse();
        $responseData = $this->getResponseData($response);

        self::assertEquals(Response::HTTP_OK, $response->getStatusCode());

        self::assertEquals($payload['fantasyName'], $responseData['fantasyName']);
    }

    public function testUpdateCondoWithWrongValue(): void
    {
        $payload = ['fantasyName' => 'A'];

        $userId = $this->createUser();

        self::$admin->request(Request::METHOD_PATCH, \sprintf(self::ENDPOINT_CONDO, $userId), [], [], [], \json_encode($payload));

        $response = self::$admin->getResponse();

        self::assertEquals(Response::HTTP_BAD_REQUEST, $response->getStatusCode());
    }
}