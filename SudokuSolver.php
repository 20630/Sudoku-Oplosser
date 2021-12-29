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

    public function solve(): SudokuSolver {
        $this->sudokuType = SudokuType::IN_SOLVE;
        $solveTimeStarted = hrtime(true);

        while ($this->sudokuType == SudokuType::IN_SOLVE) {
            //Solving algorithm here
        }

        $solveTimeFinished = hrtime(true);
        $this->solveDuration = $solveTimeFinished - $solveTimeStarted;

        return $this;
    }
}