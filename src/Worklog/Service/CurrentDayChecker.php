<?php

declare(strict_types=1);

namespace App\Worklog\Service;

class CurrentDayChecker
{
    /**
     * @param string $date
     * @return bool
     */
    public function call(string $date): bool
    {
        $currentDate = date('d/m/Y');

        return $date === $currentDate;
    }
}
