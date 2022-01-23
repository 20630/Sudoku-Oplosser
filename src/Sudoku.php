<?php
namespace j3ltr\SudokuSolver;

use RuntimeException;
use InvalidArgumentException;
use OutOfRangeException;

class Sudoku {
    private array $grid;

    //The size as in the width/height of one block (4x4: 2, 9x9: 3).
    private int $size;

    public function __construct(array $grid, int $size) {
        if (count($grid) != $size ** 2) {
            throw new RuntimeException('Grid (' . count($grid) . ') does not have the provided size (' . $size ** 2 . ')');
        }

        foreach ($grid as $y => $row) {
            if (count($row) != $size ** 2) {
                throw new RuntimeException('Grid (' . count($grid) . ') does not have the provided size (' . $size ** 2 . ')');
            }
            foreach ($row as $x => $cell) {
                if (!is_numeric($cell)) {
                    throw new RuntimeException('Grid contains an invalid symbol (' . $cell . ') at x = ' . $x . ', y = ' . $y . ')');
                }
            }
        }

        $this->grid = $grid;
        $this->size = $size;
    }

    public static function fromString(string $grid, int $size): self {
        $grid = str_split($grid);

        if (count($grid) != ($size ** 2) ** 2) {
            throw new RuntimeException('Grid (' . count($grid) . ') does not have the provided size (' . ($size ** 2) ** 2 . ')');
        }

        $values = range(0, $size ** 2);
        foreach ($grid as $i => $symbol) {
            if (!in_array($symbol, $values)) {
                throw new RuntimeException('Grid contains an invalid symbol (' . $symbol . ') at index: ' . $i);
            }

            $grid[$i] = (int) $symbol;
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
            throw new OutOfRangeException('Row (' . $row . ') is out of range (' . $this->size ** 2 .')');
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
            throw new OutOfRangeException('Column (' . $column . ') is out of range (' . $this->size ** 2 .')');
        }

        return array_column($this->grid, $column);
    }

    public function getBoxes(): array {
        $boxes = array();
        foreach ($this->getRows() as $y => $row) {
            foreach ($row as $x => $value) {
                //I have to add 1 (and later subtract 1) to the $x and $y to prevent division by zero.
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
            throw new OutOfRangeException('Box (' . $box . ') is out of range (' . $this->size ** 2 .')');
        }

        return $this->getBoxes()[$box];
    }

    public function getBoxFromCell(int $x, int $y): array {
        if ($x > $this->size ** 2 || $y > $this->size ** 2) {
            throw new OutOfRangeException('Cell (x = ' . $x . ', y = ' . $y . ') is out of range (' . $this->size ** 2 .')');
        }

        $boxes = array();

        $a = (ceil(($y + 1) / $this->size) - 1) * $this->size;
        $b = ceil(($x + 1) / $this->size) - 1;
        $i = $a + $b;

        foreach ($this->getRows() as $yy => $row) {
            foreach ($row as $xx => $value) {
                $aa = (ceil(($yy + 1) / $this->size) - 1) * $this->size;
                $bb = ceil(($xx + 1) / $this->size) - 1;
                $ii = $aa + $bb;
                if ($ii != $i) continue;
                $boxes[] = $value;
            }
        }

        return $boxes;
    }

    public function getCell(int $x, int $y) {
        return $this->grid[$y][$x];
    }

    public function setCell(int $x, int $y, $value): self {
        if (!is_int($value) && !is_array($value)) {
            throw new InvalidArgumentException('Value (' . $value . ') is neither an int nor an array');
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