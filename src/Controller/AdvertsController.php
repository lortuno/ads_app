<?php

namespace App\Controller;

use App\Entity\Advert;
use App\Entity\ComponentType;
use App\Entity\Status;
use App\Repository\AdvertRepository;
use App\Repository\ComponentRepository;
use App\Service\AdvertHelper;
use App\Service\ComponentValidation;
use Doctrine\ORM\EntityNotFoundException;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Proxies\__CG__\App\Entity\Component;
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
            'adverts'         => $adverts,
        ]);
    }

    /**
     * @Route("/advert/{id}/", name="advert_publish", methods={"GET"})
     */
    public function publishAdvert(Advert $advert)
    {
        $id = $advert->getId();
        $entityManager =
            $this->getDoctrine()
                ->getManager();
        $advert        =
            $entityManager->getRepository(Advert::class)
                ->find($id);

        if ($advert) {
            $status = $advert->getStatus()->getName();

            if (ComponentValidation::checkValidStatus($status)) {
                $components = $this->componentRepository->findByAdvertIdSerialized($id);

                if ($components) {
                    $status =
                        $this->getDoctrine()
                            ->getRepository(Status::class)
                            ->findOneBy(array('name' => Status::PUBLISHING));

                    $advert->setStatus($status);

                    $entityManager->flush();

                    return new JsonResponse(['valid' => true, 'components' => $components]);
                } else {
                    return new JsonResponse([
                        'message' => 'Can\'t publish, the ad does not contain any component',
                    ], 400);
                }
            } else {
                return new JsonResponse([
                    'message' => 'Can\'t publish, the ad is not in the right status',
                ], 503);
            }
        } else {
            return new JsonResponse([
                'message' => 'Can\'t publish, the ad does not exist',
            ], 404);
        }
    }


    /**
     * @Route("/component/{id}", name="component_publish", methods={"POST", "GET"})
     */
    public function publishComponent(ValidatorInterface $validator, Request $request, Advert $advert)
    {
        try {
            $request->request->set('advert_id', 12);
            $id = $request->request->get('advert_id');

            $entityManager =
                $this->getDoctrine()
                    ->getManager();
            $advert        =
                $entityManager->getRepository(Advert::class)
                    ->find($id);


            if ($advert) {
                $request->request->set('position', 2);
                $request->request->set('height', 2);
                $request->request->set('width', 2);
                $request->request->set('type', 'Image');

                $type =
                    $this->getDoctrine()
                        ->getRepository(ComponentType::class)
                        ->findOneBy(array('name' => $request->request->get('type')));

                $component = new \App\Entity\Component();
                $component->setType($type);

                $component->setPosition($request->request->get('position'));
                $component->setHeight($request->request->get('height'));
                $component->setWidth($request->request->get('width'));
                $component->setAdvertId($advert);

                $errors = $validator->validate($component);
                if (count($errors) > 0) {
                    return new JsonResponse((string)$errors, 400);
                }

                // tell Doctrine you want to (eventually) save the Product (no queries yet)
                $entityManager->persist($component);

                // actually executes the queries (i.e. the INSERT query)
                $entityManager->flush();

                return new JsonResponse('Saved new component with id ' . $component->getId());
            } else {
                return new JsonResponse([
                    'message' => 'Can\'t publish, the ad does not exists',
                ], 400);
            }
        } catch (\Exception $e) {
            return new JsonResponse((string)$e, 404);
        }
    }
}
