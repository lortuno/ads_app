<?php

namespace App\Controller;

use App\Entity\Advert;
use App\Entity\ComponentType;
use App\Entity\Image;
use App\Entity\Status;
use App\Entity\Text;
use App\Entity\Video;
use App\Repository\AdvertRepository;
use App\Repository\ComponentRepository;
use App\Service\AdvertHelper;
use App\Service\ComponentValidation;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Validator\Validator\ValidatorInterface;
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
        $id            = $advert->getId();
        $entityManager =
            $this->getDoctrine()
                ->getManager();
        $advert        =
            $entityManager->getRepository(Advert::class)
                ->find($id);

        if ($advert) {
            $status =
                $advert->getStatus()
                    ->getName();

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
           /* var_dump($request->query->all());die;
            $id = $request->request->get('id', 1);*/

            $entityManager =
                $this->getDoctrine()
                    ->getManager();

            if (!$id = $advert->getId()) {
                throw new NotFoundHttpException('Can\'t publish, the ad does not exists', null, 404);
            }

            $advert = $entityManager->getRepository(Advert::class)->find($id);

            $data = $this->getComponentParams($request);

            var_dump($data);die;

            $type =
                $this->getDoctrine()
                    ->getRepository(ComponentType::class)
                    ->findOneBy(array('name' => $data['type']));

            $component = new \App\Entity\Component();
            $component->setType($type);

            $component->setPosition($data['position']);
            $component->setHeight($data['height']);
            $component->setWidth($data['width']);
            $component->setAdvertId($advert);

            $errors = $validator->validate($component);
            if (count($errors) > 0) {
                return new JsonResponse((string)$errors, 400);
            }

            $entityManager->persist($component);

           // $entityManager->flush();

            $componentTypeData = $this->buildComponentDataByType($type, $data, $component);

            $entityManager->persist($componentTypeData);

            $entityManager->flush();

            return new JsonResponse('Saved new component with id ' . $component->getId());
        } catch (\Exception $e) {
            return new JsonResponse((string)$e, 404);
        }
    }

    private function getComponentParams($request)
    {
        // Data de prueba
        $request->request->set('position', '2,2,2');
        $request->request->set('height', 2);
        $request->request->set('width', 2);
        $request->request->set('type', ComponentType::IMAGE);
        $request->request->set('link', 'https://www.marca.es/hola.jpg');

        $type = $this->getComponentType($request->request->get('type'));
        $link = $request->request->get('link');
        $format = ComponentValidation::getExtension($link);
        $validLink = $this->checkValidType($type, $request);

        $data = array(
            'position' => $request->request->get('position'),
            'height'   => $request->request->get('height'),
            'width'    => $request->request->get('width'),
            'type'     => $type,
            'link'     => $validLink,
            'format'   => $format,
            'weight'   => $request->request->get('weight'),
            'value'    => ComponentValidation::checkValidText($request->request->get('text')),
        );

        foreach ($data as $param => $value) {
            if (!$this->validateComponentParam($value)) {
                return new JsonResponse('Error: El parámetro ' . $param . ' no es válido', 400);
            }
        }

        return $data;

    }

    private function checkValidType($type, $request)
    {
        switch ($type) {
            case ComponentType::IMAGE:
                return ComponentValidation::checkValidImage($request->request->get('link'));
                break;
            case ComponentType::VIDEO:
                return ComponentValidation::checkValidVideo($request);
                break;
        }

        return false;
    }

    private function getComponentType($type)
    {
        switch ($type) {
            case ComponentType::IMAGE:
            case ComponentType::VIDEO:
            case ComponentType::TEXT:
                return $type;
                break;
            default:
                return new JsonResponse('Error. Type not valid ', 400);
                break;
        }
    }

    private function buildComponentDataByType($type, $data, $component)
    {
        switch ($type) {
            case ComponentType::IMAGE:
                $componentType = new Image();
                $componentType->setComponentId($component);
                $componentType->setFormat($data['format']);
                $componentType->setLink($data['link']);
                $componentType->setWeight($data['weight']);
                break;
            case ComponentType::TEXT:
                $componentType = new Text();
                $componentType->setComponentId($component);
                $componentType->setValue($data['value']);
                break;
            case ComponentType::VIDEO:
                $componentType = new Video();
                $componentType->setComponentId($component);
                $componentType->setFormat($data['format']);
                $componentType->setLink($data['link']);
                $componentType->setWeight($data['weight']);
                break;
            default:
                $componentType = null;
                break;
        }

        return $componentType;
    }

    private function validateComponentParam($param)
    {
        switch ($param) {
            case 'height':
            case 'width':
            case 'weight':
                return is_numeric($param);
                break;
            case 'link':
                return filter_var($param, FILTER_SANITIZE_URL) === $param;
                break;
            case 'format':
            case 'position':
            case 'text':
                return is_string($param);
                break;
        }
    }
}
