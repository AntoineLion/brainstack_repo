<?php

namespace App\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Hit;
use App\Repository\HitRepository;

class HomeController extends AbstractController
{

    /**
     * @var EntityManagerInterface
     */
    private EntityManagerInterface $entityManager;

    /**
     * @var HitRepository|\Doctrine\Persistence\ObjectRepository
     */
    private HitRepository $hitRepository;


    /**
     * HomeController constructor.
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
        $this->hitRepository = $entityManager->getRepository(Hit::class);
    }

    /**
     * @param Request $request
     * @return Response
     */
    #[Route('/', name: 'home')]
    public function index(Request $request): Response
    {
        $this->save($request);
        return $this->render('home/index.html.twig', [
            'hits' => $this->hitRepository->countCurrentShopHits($request->server->get('HTTP_HOST'))
        ]);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    #[Route('/hit', name: 'hit', methods:['POST'])]
    public function hit(Request $request): JsonResponse
    {
        $this->save($request);
        return new JsonResponse([
            'hits' => $this->hitRepository->countCurrentShopHits($request->server->get('HTTP_HOST'))
        ]);
    }


    /**
     * @param Request $request
     * @return bool
     */
    public function save(Request $request): bool
    {
        try {

            $browserInfo = get_browser(null, true);

            $hit = new Hit();

            $hit->setIp($request->server->get('REMOTE_ADDR'));
            $hit->setBrowser($browserInfo['browser']);
            $hit->setDevice($browserInfo['platform']);
            $hit->setReferrer($request->server->get('HTTP_REFERER') ?? $request->server->get('HTTP_HOST'));
            $hit->setCreatedAt(new \DateTime());
            $hit->setShop($request->server->get('HTTP_HOST'));

            $this->entityManager->persist($hit);
            $this->entityManager->flush();

            return true;

        } catch (\Exception $e) {

            // Can do logging
            return false;

        }
    }

}
