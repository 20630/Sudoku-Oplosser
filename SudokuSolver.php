<?php
namespace j3ltr\SudokuSolver;

use RuntimeException;

class SudokuType {
    public const UNSOLVED = 0;
    public const IN_SOLVE = 1;
    public const UNSOLVABLE = 2;
    public const SOLVED = 3;
}

class SudokuSolver {
    protected Sudoku $sudoku;
    protected int $sudokuType;
    protected int $solveDuration;
    protected int $solveSteps;

    public function __construct(Sudoku $sudoku) {
        $this->sudoku = $sudoku;
        $this->sudokuType = SudokuType::UNSOLVED;
    }

    public function getSudoku(): Sudoku {
        return $this->sudoku;
    }

    public function getSudokuType(): int {
        return $this->sudokuType;
    }

    public function getSolveDuration(): int {
        if ($this->sudokuType != SudokuType::SOLVED) {
            throw new RuntimeException("Sudoku is not solved!");
        }

        return $this->solveDuration;
    }

    public function getSolveSteps(): int {
        if ($this->sudokuType != SudokuType::SOLVED) {
            throw new RuntimeException("Sudoku is not solved!");
        }

        return $this->solveSteps;
    }

    public function solve(): self {
        if ($this->hasDuplicates()) {
            $this->sudokuType = SudokuType::UNSOLVABLE;
            return $this;
        }

        if ($this->isSolved()) {
            $this->sudokuType = SudokuType::SOLVED;
            $this->solveDuration = 0;
            $this->solveSteps = 0;
            return $this;
        }

        $this->sudokuType = SudokuType::IN_SOLVE;
        $solveTimeStarted = hrtime(true);
        $this->solveSteps = 0;

        $this->prepare();
        while ($this->sudokuType == SudokuType::IN_SOLVE) {
            foreach ($this->sudoku->getRows() as $y => $row) {
                foreach ($row as $x => $cell) {
                    if (!is_array($cell)) continue;

                    //Only compares to numbers (not possible numbers, which are arrays).
                    $row =  array_filter($row, 'is_int');
                    $column = array_filter($this->sudoku->getColumn($x), 'is_int');
                    $box = array_filter($this->sudoku->getBoxFromCell($x, $y), 'is_int');

                    //Compares the cell (all possible values) to all the values of the row, column and box that the cell is in,
                    //and removes any duplicates from the possibilities.
                    $diff = array_diff($cell, $row, $column, $box);

                    if (count($diff) == 1) $diff = array_values($diff)[0];
                    $this->sudoku->setCell($x, $y, $diff);
                }
            }

            $this->solveSteps++;

            if ($this->isSolved()) {
                $this->sudokuType = SudokuType::SOLVED;
            }

            if ($this->solveSteps >= 100) $this->sudokuType = SudokuType::UNSOLVABLE;
        }

        if ($this->sudokuType == SudokuType::SOLVED) {
            $solveTimeFinished = hrtime(true);
            $this->solveDuration = $solveTimeFinished - $solveTimeStarted;
        }

        return $this;
    }

    private function hasDuplicates(): bool {
        foreach ($this->sudoku->getRows() as $row) {
            $row = array_diff($row, array(0)); //Only filled cells are taken into account.
            if (count(array_unique($row)) != count($row)) {
                return true;
            }
        }

        foreach ($this->sudoku->getColumns() as $column) {
            $column = array_diff($column, array(0)); //Only filled cells are taken into account.
            if (count(array_unique($column)) != count($column)) {
                return true;
            }
        }

        foreach ($this->sudoku->getBoxes() as $box) {
            $box = array_diff($box, array(0)); //Only filled cells are taken into account.
            if (count(array_unique($box)) != count($box)) {
                return true;
            }
        }

        return false;
    }

    private function prepare(): self {
        $values = range(1, $this->sudoku->getSize() ** 2);

        foreach ($this->sudoku->getRows() as $y => $row) {
            foreach ($row as $x => $cell) {
                if ($cell == 0) {
                    $this->sudoku->setCell($x, $y, $values);
                }
            }
        }

        return $this;
    }

    private function isSolved(): bool {
        $values = range(1, $this->sudoku->getSize() ** 2);

        foreach ($this->sudoku->getRows() as $row) {
            sort($row);
            if ($row != $values) return false;
        }

        foreach ($this->sudoku->getColumns() as $column) {
            sort($column);
            if ($column != $values) return false;
        }

        foreach ($this->sudoku->getBoxes() as $box) {
            sort($box);
            if ($box != $values) return false;
        }

        return true;
    }
}