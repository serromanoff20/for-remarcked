<?php namespace App\models\Discounts;

require_once "app/models/Discounts/Discounts.php";

use App\models\Discounts\Discounts;
use DateTime;
use DateTimeImmutable;

class DiscountPensioners extends Discounts
{

    protected bool $isPensioner = false;
    protected float $discount = 0.05;

    /**
     * @throws \Exception
     */
    protected function isPensioner(string $gender, string $dateBirth): void
    {
        $diff = (new DateTime())->diff(new DateTimeImmutable($dateBirth));
        $realAge = $diff->y;

        switch ($gender) {
            case 'м':
                ($realAge >= self::PENSION_AGE_MAN) ? $this->isPensioner = true : $this->forPensioners = false;
                break;
            case 'ж':
                ($realAge >= self::PENSION_AGE_WOMAN) ? $this->isPensioner = true : $this->forPensioners = false;
                break;
            default:
                $this->isPensioner = false;
        }
//        if ($this->isPensioner) {
//            $this->countDiscount++;
//        }
    }
}