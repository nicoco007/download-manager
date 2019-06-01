<?php


namespace App\Controller;


use App\Entity\Download;
use App\Repository\DownloadRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class ChartApiController extends AbstractController
{
    /**
     * @Route("/api/charts/all", name="chart_all_downloads")
     * @throws \Exception
     */
    public function allDownloads()
    {
        $chart = $this->buildLineObject();

        /** @var DownloadRepository $repo */
        $repo = $this->getDoctrine()->getRepository(Download::class);

        $days = $repo->findAfter(7);

        $dataset = [
            'label' => 'Downloads',
            'data' => []
        ];

        for ($i = 6; $i >= 0; $i--) {
            $dateStr = (new \DateTime('now', new \DateTimeZone('UTC')))->sub(new \DateInterval('P' . $i . 'D'))->format('Y-m-d');

            $chart['data']['labels'][] = $dateStr;

            if (isset($days[$dateStr]))
                $dataset['data'][] = $days[$dateStr]['count'];
            else
                $dataset['data'][] = 0;
        }

        $chart['data']['datasets'][0] = $dataset;

        return new JsonResponse($chart);
    }

    /**
     * @Route("/api/charts/test")
     * @return JsonResponse
     * @throws \Exception
     */
    public function test()
    {
        /** @var DownloadRepository $repo */
        $repo = $this->getDoctrine()->getRepository(Download::class);
        return new JsonResponse($repo->findAllAfter(7));
    }

    private function buildLineObject()
    {
        return [
            'type' => 'line',
            'data' => [
                'labels' => [],
                'datasets' => []
            ],
            'options' => [
                'maintainAspectRatio' => false,
                'scales' => [
                    'yAxes' => [
                        [
                            'ticks' => [
                                'beginAtZero' => true
                            ]
                        ]
                    ]
                ],
                'elements' => [
                    'line' => [
                        'tension' => 0
                    ]
                ]
            ]
        ];
    }
}