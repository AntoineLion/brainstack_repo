<?php

namespace App\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Hit;

class HomeController extends AbstractController
{

    /**
     * @var EntityManagerInterface
     */
    protected $entityManager;

    /**
     * HomeController constructor.
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
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
            'hits' => $this->getHits($request->server->get('HTTP_HOST'))
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
            'hits' => $this->getHits($request->server->get('HTTP_HOST'))
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


    /**
     * @param string $shop
     * @return string
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function getHits(string $shop): string
    {
        $repository = $this->getDoctrine()->getRepository(Hit::class);
        return $repository->createQueryBuilder('hits')
            ->select('count(hits.id)')
            ->where('hits.shop = :shop')
            ->setParameter('shop', $shop)
            ->getQuery()
            ->getSingleScalarResult();
    }

}
