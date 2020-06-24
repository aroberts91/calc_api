<?php

namespace App\Controller;

use App\Entity\Calculation;
use DateTime;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class CalculatorController extends AbstractController
{
    public function calc(Request $request): Response
    {
        $sum_str = $request->request->get('sum_str');

        eval('$res = (' . $sum_str . ');');

        $this->recordCalc($sum_str, $res);

        return new Response(
            json_encode(['sum' => $res])
        );
    }

    public function recordCalc(string $sum, string $res)
    {
        $entityManager = $this->getDoctrine()->getManager();

        $calculation = new Calculation();
        $calculation->setCalculation($sum);
        $calculation->setResult($res);

        $datetime = new DateTime();
        $datetime->format('Y-m-d h:i:s');
        $calculation->setDatetime($datetime);


        $entityManager->persist($calculation);
        $entityManager->flush();

        return true;
    }

    public function getResults($limit): Response
    {
        $calculations = $this->getDoctrine()
            ->getRepository(Calculation::class)
            ->getAllCalculations($limit);

        return new Response(
            json_encode(['res' => $calculations])
        );
    }
}
