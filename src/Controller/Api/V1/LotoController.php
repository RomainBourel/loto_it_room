<?php

namespace App\Controller\Api\V1;

use App\Entity\Loto;
use App\Repository\LotoRepository;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class LotoController extends AbstractController
{

    public function __construct(private LoggerInterface $logger)
    {
    }

    #[Route('/api/v1/loto/{drawNumber}', name: 'app_api_v1_loto')]
    public function index(string $drawNumber, LotoRepository $lotoRepository, EntityManagerInterface $em): Response
    {
        $lotteryDrawing = $this->getLotteryDrawing();
        //create dateTime of last drawing
        $today = \DateTimeImmutable::createFromFormat('d/m/Y H:i:s', $lotteryDrawing['date_de_tirage'][0].' 00:00:00');
        $loto = $lotoRepository->findOneByDrawingAt($today);
        if (null === $loto) {
            $loto = (new Loto())
                ->setBallArray([
                    $lotteryDrawing['boule_1'][0],
                    $lotteryDrawing['boule_2'][0],
                    $lotteryDrawing['boule_3'][0],
                    $lotteryDrawing['boule_4'][0],
                    $lotteryDrawing['boule_5'][0],
                ])
                ->setAdditionalNumber($lotteryDrawing['numero_chance'][0])
                ->setDrawingAt($today)
                ->setCreatedAt(new \DateTimeImmutable())
                ->setUpdatedAt(new \DateTimeImmutable())
            ;
            $em->persist($loto);
            $em->flush();
        }

        $drawNumber = explode('-', $drawNumber);
        $numberCorrectBall = 0;
        foreach ($loto->getBallArray() as $key => $ball) {
            if ($ball === $drawNumber[$key]) {
                $numberCorrectBall++;
            }
        }
        $isWin = 2 <= $numberCorrectBall || $loto->getAdditionalNumber() === (int) $drawNumber[5];

        return $this->json(['isWin' => $isWin]);
    }

    private function getLotteryDrawing(): array
    {
        $url = 'https://media.fdj.fr/static/csv/loto/loto_201911.zip';
        $path = 'lotoFiles/';
        $zip = new \ZipArchive();
        try {
            $contents = file_get_contents($url);
            file_put_contents($path.'loto_201911.zip', $contents);
        } catch (\Exception $e) {
            $this->logger->error('Error while downloading the file');
            throw new \Exception('Error while downloading the file');
        }

        try {
            $zip->open($path.'loto_201911.zip');
            $zip->extractTo($path);
            $csv = $zip->getFromName('loto_201911.csv');
        } catch (\Exception $e) {
            $this->logger->error('Error while extracting the file');
            throw new \Exception('Error while extracting the file');
        }

        $csvLine = explode(PHP_EOL, $csv);
        $csvArray = $arrayKey = [];
        for ($i = 0; $i < 2; $i++) {
            if ($i === 0) {
                $arrayKey = explode(';',$csvLine[$i]);
                foreach ($arrayKey as $value) {
                    $csvArray[$value] = [];
                }
                continue;
            }
            $line = explode(';',$csvLine[$i]);
            foreach ($line as $key => $value) {
                array_push($csvArray[$arrayKey[$key]], $value);
            }
        }
        return $csvArray;
    }
}
