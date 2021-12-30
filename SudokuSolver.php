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

    public function solve(): SudokuSolver {
        if ($this->isSolved()) {
            $this->sudokuType = SudokuType::SOLVED;
            $this->solveDuration = 0;
            return $this;
        }

        $this->sudokuType = SudokuType::IN_SOLVE;
        $solveTimeStarted = hrtime(true);

        while ($this->sudokuType == SudokuType::IN_SOLVE) {
            //Solving algorithm here
        }

        if ($this->sudokuType == SudokuType::SOLVED) {
            $solveTimeFinished = hrtime(true);
            $this->solveDuration = $solveTimeFinished - $solveTimeStarted;
        }

        return $this;
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