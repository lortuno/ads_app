<?php
/**
 * Created by PhpStorm.
 * User: SH
 * Date: 23/06/2019
 * Time: 21:20
 */

namespace App\Service;

use App\Repository\AdvertRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class AdvertHelper
{
    private $adRepository;

    public function __construct(AdvertRepository $adRepository)
    {
        $this->adRepository = $adRepository;
    }

    public function publishAdvert($id = 1) {
        $advert = $this->adRepository->findOneBy(array('id' => $id));

        if ($advert) {
            return 'hola';
        } else {
            return new JsonResponse([
                'message' => 'Can\'t publish the add'
            ], 404);
        }
    }
}
