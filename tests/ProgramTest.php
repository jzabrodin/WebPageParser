<?php

//require_once __DIR__ . '/../Parser.php';
require_once __DIR__ . '/../src/Report.php';


class RunnerTest extends PHPUnit\Framework\TestCase
{

    /** @test
     * @return void
     * report
     */
    public function report()
    {
        $report = new Report('netpeak.ua');
        $report->printReport();
    }


}