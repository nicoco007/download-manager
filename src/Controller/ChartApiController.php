<?php


namespace App\Controller;


use App\Entity\Download;
use App\Repository\DownloadRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
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

        $downloads_by_project = [];

        foreach ($days as $day) {
            $downloads_by_project[$day['project_name']][$day['date']->format('Y-m-d')] = $day['count'];
        }

        $chart['options']['scales']['xAxes'] = [[
            'type' => 'time',
            'time' => [
                'unit' => 'day'
            ]
        ]];

        foreach ($downloads_by_project as $project_name => $days) {
            $dataset = [
                'label' => $project_name,
                'data' => [],
                'fill' => false
            ];

            for ($i = 6; $i >= 0; $i--) {
                $dateStr = (new \DateTime('now', new \DateTimeZone('UTC')))->sub(new \DateInterval('P' . $i . 'D'))->format('Y-m-d');

                $dataset['data'][] = [
                    'x' => $dateStr,
                    'y' => $days[$dateStr] ?? 0
                ];
            }

            $chart['data']['datasets'][] = $dataset;
        }

        return new JsonResponse($chart);
    }

    /**
     * @Route("/api/charts/project")
     * @return JsonResponse
     * @throws \Exception
     */
    public function project(Request $request)
    {
        $slug = $request->query->get('slug');

        /** @var DownloadRepository $repo */
        $repo = $this->getDoctrine()->getRepository(Download::class);
        $days = $repo->findAfterForProject($slug, 7);

        $downloads_by_file = [];

        foreach ($days as $day) {
            $downloads_by_file[$day['file_name']][$day['date']->format('Y-m-d')] = $day['count'];
        }

        $chart = $this->buildLineObject();

        $chart['options']['scales']['xAxes'] = [[
            'type' => 'time',
            'time' => [
                'unit' => 'day'
            ]
        ]];

        foreach ($downloads_by_file as $file_name => $days) {
            $dataset = [
                'label' => $file_name,
                'data' => [],
                'fill' => false
            ];

            for ($i = 6; $i >= 0; $i--) {
                $dateStr = (new \DateTime('now', new \DateTimeZone('UTC')))->sub(new \DateInterval('P' . $i . 'D'))->format('Y-m-d');

                $dataset['data'][] = [
                    'x' => $dateStr,
                    'y' => $days[$dateStr] ?? 0
                ];
            }

            $chart['data']['datasets'][] = $dataset;
        }

        return new JsonResponse($chart);
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
                                'beginAtZero' => true,
                                'precision' => 0
                            ]
                        ]
                    ]
                ],
                'plugins' => [
                    'colorschemes' => [
                        'scheme' => 'brewer.SetOne9'
                    ]
                ]
            ]
        ];
    }
}