<?php namespace App\models;

use app\common\models\ModelApp;
use DateTime;
use DateTimeImmutable;

class Discounts extends ModelApp
{
    const PENSION_AGE_MAN = 63;
    const PENSION_AGE_WOMAN = 58;
    const ONE_WEEK = 7;
    const MIN_QUANTITY_GOODS_FOR_DISCOUNT = 11;

    public bool $forPensioners = false;
    public float $discountPensioners = 0.05;
    public bool $forEarlyBird = false;
    public float $discountEarlyBird = 0.04;
    public bool $forQuantityGoods = false;
    public float $discountQuantityGoods = 0.03;

    private int $countDiscount = 0;

    /**
     * @throws \Exception
     */
    public function __construct(array $params)
    {
        $this->isPensioner($params['gender_buyer'], $params['date_birth_buyer']);

        $this->isEarlyBird($params['delivery_date']);

        $this->isManyQuantityGoods($params['quantity']);
    }

    /**
     * @throws \Exception
     */
    private function isPensioner(string $gender, string $dateBirth): void
    {
        $diff = (new DateTime())->diff(new DateTimeImmutable($dateBirth));
        $realAge = $diff->y;

        switch ($gender) {
            case 'м':
                ($realAge >= self::PENSION_AGE_MAN) ? $this->forPensioners = true : $this->forPensioners = false;
                break;
            case 'ж':
                ($realAge >= self::PENSION_AGE_WOMAN) ? $this->forPensioners = true : $this->forPensioners = false;
                break;
            default:
                $this->forPensioners = false;
        }
        if ($this->forPensioners) {
            $this->countDiscount++;
        }
    }

    /**
     * @throws \Exception
     */
    private function isEarlyBird(string $deliveryDate): void
    {
        $diff = (new DateTime())->diff(new DateTimeImmutable($deliveryDate));

        if ($diff->d >= self::ONE_WEEK) {
            $this->forEarlyBird = true;

            $this->countDiscount++;
        }

    }

    private function isManyQuantityGoods(int $quantity): void
    {
        if ($quantity >= self::MIN_QUANTITY_GOODS_FOR_DISCOUNT) {
            $this->forQuantityGoods = true;

            $this->countDiscount++;
        }
    }

    public function calculateDiscount(float $baseCost)
    {
//        $costOrder = $baseCost - ()
//
//        for ($i = $this->countDiscount; $i > 0; --$i) {
//            $costOrder = $baseCost - ($baseCost * )
//        }
//
//        return ;
    }
}