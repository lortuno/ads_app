<?php

namespace App\Controller;

use App\Entity\Advert;
use App\Repository\AdvertRepository;
use App\Repository\ComponentRepository;
use App\Service\AdvertHelper;
use App\Service\ComponentValidation;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class AdvertsController extends Controller
{
    private $adRepository;
    private $componentRepository;

    public function __construct(AdvertRepository $adRepository, ComponentRepository $componentRepository)
    {
        $this->adRepository        = $adRepository;
        $this->componentRepository = $componentRepository;
    }

    /**
     * Lista los artículos actuales.
     * @Route("/", name="adverts")
     */
    public function index(AdvertRepository $advertRepository)
    {
        $adverts = $advertRepository->findAllAdvertInfo();

        return $this->render('adverts/adverts.html.twig', [
            'controller_name' => 'AdvertsController',
            'adverts' => $adverts,
        ]);
    }

    /**
     * @Route("/advert/{id}/", name="advert_publish", methods={"GET"})
     */
    public function publishAdvert(Advert $advert)
    {
        $id = $advert->getId();
        $advert = $this->adRepository->findByIdSerialized($id);

        if ($advert) {
            $components = $this->componentRepository->findByAdvertIdSerialized($id);

            return new JsonResponse(['valid' => true]);
            if($components) {
                foreach ($components as $key => $component) {
                    switch ($component['type']) {
                        case 'image':
                        default:

                            break;
                    }
                }
            } else {
                return new JsonResponse([
                    'message' => 'Can\'t publish, the ad does not contain any component',
                ], 404);
            }

            return new JsonResponse(['valid' => true, 'components' => $components]);
        } else {
            return new JsonResponse([
                'message' => 'Can\'t publish, the ad does not exist',
            ], 404);
        }
    }

    public function publishComponent()
    {

    }
}
