<?php
namespace j3ltr\SudokuSolver;

class Sudoku {
    protected array $grid;
    protected int $size;

    public function __construct(array $grid, int $size) {
        if ($size ** 2 != count($grid) || $size ** 2 != count(array_column($grid))) {
            return new InvalidArgumentException('Grid does not have the provided size.');
        }

        $this->grid = $grid;
        $this->size = $size;
    }

    public function getRow(int $row): array {
        if ($row > $this->size) {
            throw new InvalidArgumentException('Row is out of range.');
        }

        return $this->grid[$row - 1];
    }

    public function getColumn(int $column): array {
        if ($column > $this->size) {
            throw new InvalidArgumentException('Column is out of range.');
        }

        return array_column($this->grid, $column - 1);
    }

    //Questionable code, might leave this out... I'll see if/when I actually have to use this.
    public function getBox(int $box): array {
        if ($box > $this->size ** 2) {
            throw new InvalidArgumentException('Box is out of range.');
        }

        $startRow = (ceil($box / $this->size) - 1) * 3;
        $startColumn = ($this->size - ($box % $this->size) - 1) * 3;

        $boxArray = array();
        for ($row = $startRow; $row < $startRow + 3; $row++) {
            for ($column = $startColumn; $column < $startColumn + 3; $column++) {
                $boxArray[] = $this->getCell($row, $column);
            }
        }

        return $boxArray;
    }

    public function getCell(int $row, int $column): int {
        return $this->grid[$row - 1][$column - 1];
    }
}