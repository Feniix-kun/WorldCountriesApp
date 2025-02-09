<?php

namespace App\Controller;

use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use Exception;

use App\Model\Country;
use App\Model\CountryScenarios;
use App\Model\Exceptions\CountryNotFoundException;
use App\Model\Exceptions\DuplicatedDataException;
use App\Model\Exceptions\InvalidCodeException;
use App\Model\Exceptions\InvalidDataException;



#[Route(path:'api/country', name:"app_api_country")]
final class CountryController extends AbstractController
{
    public function __construct(
        private readonly CountryScenarios $countries
    ) {
        
    }

    #[Route(path:'', name: 'app_api_country_root', methods: ['GET'])]
    public function getAll() : JsonResponse
    {
        $countries = $this->countries->getAll();
        return $this->json(data: $countries, status: 200);
    }

    #[Route(path:'/{code}', name: 'app_api_country_code', methods: ['GET'])]
    public function get(string $code) : JsonResponse
    {
        try{
            $country = $this->countries->get($code);
            return $this->json(data: $country, status: 200);
        } catch (InvalidCodeException $ex){
            $response = $this->buildErrorMessage($ex);
            $response->setStatusCode(400);
            return $response;
        } catch(CountryNotFoundException $ex){
            $response = $this->buildErrorMessage($ex);
            $response->setStatusCode(404);
            return $response;
        }
    }

    #[Route(path:'', name: 'app_api_country_add', methods: ['POST'])]
    public function add(#[MapRequestPayload]Country $country): JsonResponse {
        try{
            $this->countries->add($country);
            return $this->json(data: $country, status: 200);
        } catch (DuplicatedDataException $ex){
            $response = $this->buildErrorMessage($ex);
            $response->setStatusCode(409);
            return $response;
        } catch (InvalidDataException $ex){
            $response = $this->buildErrorMessage($ex);
            $response->setStatusCode(400);
            return $response;
        }
    }

    #[Route(path:'/{code}', name: 'app_api_country_edit', methods: ['PATCH'])]
    public function edit(string $code, #[MapRequestPayload]CountryInfo $countryInfo): JsonResponse {
        try{
            $country = $this->countries->get($code);
            $country->shortName = $countryInfo->shortName;
            $country->fullName = $countryInfo->fullName;
            $country->population = $countryInfo->population;
            $country->square = $countryInfo->square;
            $this->countries->edit($country);
            return $this->json(data: $country, status: 200);
        } catch (DuplicatedDataException $ex){
            $response = $this->buildErrorMessage($ex);
            $response->setStatusCode(409);
            return $response;
        } catch (InvalidDataException | InvalidCodeException $ex){
            $response = $this->buildErrorMessage($ex);
            $response->setStatusCode(400);
            return $response;
        } catch(CountryNotFoundException $ex){
            $response = $this->buildErrorMessage($ex);
            $response->setStatusCode(404);
            return $response;
        } 
    }

    #[Route(path:'/{code}', name: 'app_api_country_delete', methods: ['DELETE'])]
    public function delete(string $code): JsonResponse {
        try{
            $this->countries->remove($code);
            return $this->json(data: null, status: 204);
        } catch(CountryNotFoundException $ex){
            $response = $this->buildErrorMessage($ex);
            $response->setStatusCode(404);
            return $response;
        } catch (InvalidCodeException $ex){
            $response = $this->buildErrorMessage($ex);
            $response->setStatusCode(400);
            return $response;
        }
    }

    private function buildErrorMessage(Exception $ex): JsonResponse {
        return $this->json(data: [
            'errorCode' => $ex->getCode(),
            'errorMessage' => $ex->getMessage(),
        ]);
    }
}
