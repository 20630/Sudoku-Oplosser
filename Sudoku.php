<?php
namespace j3ltr\SudokuSolver;

use InvalidArgumentException;

class Sudoku {
    protected array $grid;
    protected int $size;

    public function __construct(array $grid, int $size) {
        if ($size ** 2 != count($grid) || $size ** 2 != count(array_column($grid, 0))) {
            throw new InvalidArgumentException('Grid does not have the provided size.');
        }

        $this->grid = $grid;
        $this->size = $size;
    }

    public function getGrid(): array {
        return $this->grid;
    }

    public function getSize(): int {
        return $this->size;
    }

    public function getRows(): array {
        return $this->grid;
    }

    public function getRow(int $row): array {
        if ($row > $this->size ** 2) {
            throw new InvalidArgumentException('Row is out of range.');
        }

        return $this->grid[$row - 1];
    }

    public function getColumns(): array {
        $columns = array();

        for ($i = 0; $i < count($this->grid); $i++) {
            $columns[] = array_column($this->grid, $i);
        }

        return $columns;
    }

    public function getColumn(int $column): array {
        if ($column > $this->size ** 2) {
            throw new InvalidArgumentException('Column is out of range.');
        }

        return array_column($this->grid, $column - 1);
    }

    public function getBoxes(): array {
        $boxes = array();
        foreach ($this->getRows() as $y => $row) {
            foreach ($row as $x => $value) {
                $a = (ceil(($y + 1) / $this->getSize()) - 1) * 3;
                $b = ceil(($x + 1) / $this->getSize()) - 1;
                $i = $a + $b;
                $boxes[$i][] = $value;
            }
        }
        return $boxes;
    }

    public function getBox(int $box): array {
        if ($box > $this->size ** 2) {
            throw new InvalidArgumentException('Box is out of range.');
        }

        return $this->getBoxes()[$box - 1];
    }

    public function getBoxFromCell(int $x, int $y): array {
        if ($x > $this->size ** 2 || $y > $this->size ** 2) {
            throw new InvalidArgumentException('Cell is out of range.');
        }

        $a = (ceil(($y + 1) / $this->getSize()) - 1) * 3;
        $b = ceil(($x + 1) / $this->getSize()) - 1;
        $i = $a + $b;

        return $this->getBox($i);
    }

    public function getCell(int $x, int $y): int {
        return $this->grid[$y - 1][$x - 1];
    }

    public function setCell(int $x, int $y, int $value): self {
        $this->grid[$y - 1][$x - 1] = $value;
        return $this;
    }
}