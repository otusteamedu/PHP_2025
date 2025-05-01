<?php

namespace App\Infrastructure\Gateway;

use App\Application\Gateway\InternetGatewayInterface;
use App\Application\Gateway\InternetGatewayRequest;
use App\Application\Gateway\InternetGatewayResponse;
use App\Domain\DomainException\TitleException;
use DOMDocument;
use DOMXPath;

class InternetGateway implements InternetGatewayInterface
{

    /**
     * @throws TitleException
     */
    public function getTitle(InternetGatewayRequest $internetGatewayRequest): InternetGatewayResponse
    {
        $html = file_get_contents($internetGatewayRequest->url);
        $preTitle = explode('<title>', $html,2);
        $title = html_entity_decode(explode('</title>', $preTitle[1]??'',2)[0]);
        if (mb_strlen($title) > 0) {
            return new InternetGatewayResponse($title);
        }
        throw new TitleException("Error retrieving page content: Unable to fetch the document or parse its structure correctly. Please check if the provided URL is valid and accessible.");


    }
}