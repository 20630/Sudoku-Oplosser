<?php
namespace j3ltr\SudokuSolver;

class SudokuType {
    const UNSOLVED = 0;
    const IN_SOLVE = 1;
    const UNSOLVABLE = 2;
    const SOLVED = 3;
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

    public function getSolveDuration(): int {
        return $this->solveDuration;
    }

    public function getSolveSteps(): int {
        return $this->solveSteps;
    }

    public function solve(): SudokuSolver {
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
                    @$diff = array_diff($cell, $row, $this->sudoku->getColumn($x), $this->sudoku->getBoxFromCell($x, $y));
                    if (count($diff) == 1) $diff = array_values($diff)[0];
                    $this->sudoku->setCell($x, $y, $diff);
                }
            }

            $this->solveSteps++;

            if ($this->isSolved()) {
                $this->sudokuType = SudokuType::SOLVED;
            }

            if ($this->solveSteps >= 1000) $this->sudokuType = SudokuType::UNSOLVABLE;
        }

        if ($this->sudokuType == SudokuType::SOLVED) {
            $solveTimeFinished = hrtime(true);
            $this->solveDuration = $solveTimeFinished - $solveTimeStarted;
        }

        return $this;
    }

    function prepare() {
        $values = range(1, $this->sudoku->getSize() ** 2);

        foreach ($this->sudoku->getRows() as $y => $row) {
            foreach ($row as $x => $cell) {
                if ($cell == 0) {
                    $this->sudoku->setCell($x, $y, $values);
                }
            }
        }
    }

    public function isSolved(): bool {
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