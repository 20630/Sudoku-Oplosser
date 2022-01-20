<?php
namespace j3ltr\SudokuSolver;

use RuntimeException;
use InvalidArgumentException;
use OutOfRangeException;

class Sudoku {
    protected array $grid;
    protected int $size;

    public function __construct(array $grid, int $size) {
        if (count($grid) != $size ** 2) {
            throw new RuntimeException('Grid does not have the provided size.');
        }

        foreach ($grid as $row) {
            if (count($row) != $size ** 2) {
                throw new RuntimeException('Grid does not have the provided size.');
            }
            foreach ($row as $cell) {
                if (!is_numeric($cell)) {
                    throw new RuntimeException('Only numbers allowed.');
                }
            }
        }

        $this->grid = $grid;
        $this->size = $size;
    }

    public static function fromString(string $grid, int $size): self {
        $grid = str_split($grid);

        if (count($grid) != ($size ** 2) ** 2) {
            throw new RuntimeException('Grid does not have the provided size.');
        }

        $values = range(0, ($size ** 2));
        foreach ($grid as $i => $symbol) {
            if (!in_array($symbol, $values)) {
                throw new RuntimeException('Grid contains invalid symbols.');
            }

            $grid[$i] = (int) $symbol; //MAKE INT PLS FIX
        }

        $grid = array_chunk($grid, $size ** 2);

        return new Sudoku($grid, $size);
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
            throw new OutOfRangeException('Row is out of range.');
        }

        return $this->grid[$row];
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
            throw new OutOfRangeException('Column is out of range.');
        }

        return array_column($this->grid, $column);
    }

    public function getBoxes(): array {
        $boxes = array();
        foreach ($this->getRows() as $y => $row) {
            foreach ($row as $x => $value) {
                $a = (ceil(($y + 1) / $this->size) - 1) * $this->size;
                $b = ceil(($x + 1) / $this->size) - 1;
                $i = $a + $b;
                $boxes[$i][] = $value;
            }
        }
        return $boxes;
    }

    public function getBox(int $box): array {
        if ($box > $this->size ** 2) {
            throw new OutOfRangeException('Box is out of range.');
        }

        return $this->getBoxes()[$box];
    }

    public function getBoxFromCell(int $x, int $y): array {
        if ($x > $this->size ** 2 || $y > $this->size ** 2) {
            throw new OutOfRangeException('Cell is out of range.');
        }
        $a = (ceil(($y + 1) / $this->size) - 1) * $this->size;
        $b = ceil(($x + 1) / $this->size) - 1;
        $i = $a + $b;

        return $this->getBox($i);
    }

    public function getCell(int $x, int $y): int {
        return $this->grid[$y][$x];
    }

    public function setCell(int $x, int $y, $value): self {
        if (!is_int($value) && !is_array($value)) {
            throw new InvalidArgumentException('Value is neither an int nor an array.');
        }

        $this->grid[$y][$x] = $value;
        return $this;
    }

    public function __toString(): string {
        $out = '';

        foreach ($this->grid as $row) {
            foreach ($row as $value) {
                $out .= is_array($value) ? implode(', ', $value) : $value;
            }
        }

        return $out;
    }
}