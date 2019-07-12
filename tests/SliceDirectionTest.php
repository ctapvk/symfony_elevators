<?php

namespace App\Tests;

use App\Controller\ElevatorsController;
use PHPUnit\Framework\TestCase;

class SliceDirectionTest extends TestCase
{
    private $elevatorController;

    public function testSliceDirections()
    {
        $arr = [8, 5, 4, 1, 8];
        $res = $this->elevatorController->sliceDirection($arr);
        $expected = [
            '8 -> 5 -> 4 -> 1',
            '1 -> 8',
        ];
        $this->assertEquals($expected, $res);

    }

    public function testSliceDirections2()
    {
        $arr = [10, 2, 2, 2, 3];
        $res = $this->elevatorController->sliceDirection($arr);
        $expected = [
            '10 -> 2 -> 2 -> 2',
            '2 -> 3',
        ];
        $this->assertEquals($expected, $res);

    }

    public function testSliceDirections3()
    {
        $arr = [10, 2, 2, 8, 9, 2, 3];
        $res = $this->elevatorController->sliceDirection($arr);
        $expected = [
            '10 -> 2 -> 2',
            '2 -> 8 -> 9',
            '9 -> 2',
            '2 -> 3',
        ];
        $this->assertEquals($expected, $res);

    }

    public function testSliceDirectionsEmpty()
    {
        $arr = [];
        $res = $this->elevatorController->sliceDirection($arr);
        $expected = [];
        $this->assertEquals($expected, $res);

    }

    protected function setUp()
    {
        parent::setUp();
        $this->elevatorController = new ElevatorsController();
    }
}
