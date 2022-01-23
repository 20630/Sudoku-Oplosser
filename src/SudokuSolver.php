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
    private Sudoku $sudoku;
    private int $sudokuType;
    private int $solveDuration;

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

    public function solve(): self {
        if ($this->hasDuplicates()) {
            $this->sudokuType = SudokuType::UNSOLVABLE;
            return $this;
        }

        if ($this->isSolved()) {
            $this->sudokuType = SudokuType::SOLVED;
            $this->solveDuration = 0;
            return $this;
        }

        $this->solveDuration = 0;
        $this->sudokuType = SudokuType::IN_SOLVE;
        $solveTimeStarted = hrtime(true);
        $this->solveSteps = 0;

        $this->prepare();

        while ($this->sudokuType == SudokuType::IN_SOLVE) {
            //The basic algorithm begins here.
            //The algorithm will, for each cell, eliminate all possible values that are already in its row, column or box.

            $isDoneEliminating = true;
            foreach ($this->sudoku->getRows() as $y => $row) {
                foreach ($row as $x => $cell) {
                    if (!is_array($cell)) continue;

                    //Only compare to numbers (not possible numbers, which are arrays).
                    $row = array_filter($row, 'is_int');
                    $column = array_filter($this->sudoku->getColumn($x), 'is_int');
                    $box = array_filter($this->sudoku->getBoxFromCell($x, $y), 'is_int');

                    $diff = array_values(array_diff($cell, $row, $column, $box));

                    //Possible values were removed.
                    if (count($cell) != count($diff)) {
                        //One cell is solved.
                        if (count($diff) == 1) {
                            $diff = $diff[0];
                            //Because a cell is solved, each cell can eliminate this value, thus it has to check all cells again.
                            $isDoneEliminating = false;
                        }
                        $this->sudoku->setCell($x, $y, $diff);
                    }
                }
            }

            if ($this->isSolved()) {
                $this->sudokuType = SudokuType::SOLVED;
                $solveTimeFinished = hrtime(true);
                $this->solveDuration = ($solveTimeFinished - $solveTimeStarted) / 1e+6;
            }

            if ($isDoneEliminating) {
                $this->backTrack(0, 0);
            }
        }

        if ($this->sudokuType == SudokuType::SOLVED) {
            $solveTimeFinished = hrtime(true);
            $this->solveDuration = ($solveTimeFinished - $solveTimeStarted) / 1e+6;
        }
        return $this;
    }

    private function backTrack($previousX, $previousY): void {
        //This is the bruteforce algorithm.
        //It will try every possible value cell after cell.

        if ($this->sudokuType == SudokuType::SOLVED) return;

        //Getting the next empty cell.
        $nextX = -1;
        $nextY = -1;
        $nextValue = array();
        foreach ($this->sudoku->getRows() as $y => $row) {
            if ($y < $previousY) continue;
            foreach ($row as $x => $cell) {
                if ($x < $previousX) continue;
                if (!is_array($cell)) continue;
                $nextX = $x;
                $nextY = $y;
                $nextValue = $cell;
                break 2;
            }
            $previousX = 0;
        }

        //There is no next number, which means the algorithm is at the end of the sudoku.
        //This also means the sudoku is solved, because the algorithm will only get to this point if all previous cells are valid.
        if ($nextX == -1 && $nextY == -1) {
            $this->sudokuType = SudokuType::SOLVED;
            return;
        }

        //Getting the next possible value for this cell.
        for ($i = 0; $i < count($nextValue); $i++) {
            $value = $nextValue[$i];
            //Not doing this in one if-statement because if the value is in the row it doesn't have to filter the other ones.
            $row =  array_filter($this->sudoku->getRow($nextY), 'is_int');
            if (in_array($value, $row)) continue;
            $column = array_filter($this->sudoku->getColumn($nextX), 'is_int');
            if (in_array($value, $column)) continue;
            $box = array_filter($this->sudoku->getBoxFromCell($nextX, $nextY), 'is_int');
            if (in_array($value, $box)) continue;

            $this->sudoku->setCell($nextX, $nextY, $value);

            $this->backTrack($nextX + 1, $nextY);

            if ($this->sudokuType == SudokuType::SOLVED) return;
        }

        $this->sudoku->setCell($nextX, $nextY, $nextValue);
    }

    private function hasDuplicates(): bool {
        foreach ($this->sudoku->getRows() as $row) {
            $row = array_diff($row, array(0)); //Only filled cells are taken into account.
            if (count(array_unique($row)) != count($row)) {
                return true;
            }
        }

        foreach ($this->sudoku->getColumns() as $column) {
            $column = array_diff($column, array(0));
            if (count(array_unique($column)) != count($column)) {
                return true;
            }
        }

        foreach ($this->sudoku->getBoxes() as $box) {
            $box = array_diff($box, array(0));
            if (count(array_unique($box)) != count($box)) {
                return true;
            }
        }

        return false;
    }

    private function prepare(): void {
        $values = range(1, $this->sudoku->getSize() ** 2);

        foreach ($this->sudoku->getRows() as $y => $row) {
            foreach ($row as $x => $cell) {
                if ($cell == 0) {
                    $this->sudoku->setCell($x, $y, $values);
                }
            }
        }
    }

    private function isSolved(): bool {
        $values = range(1, $this->sudoku->getSize() ** 2);

        foreach ($this->sudoku->getRows() as $row) {
            $row = array_filter($row, 'is_int');
            if (count(array_diff($values, $row)) != 0) return false;
        }

        foreach ($this->sudoku->getColumns() as $column) {
            $column = array_filter($column, 'is_int');
            if (count(array_diff($values, $column)) != 0) return false;
        }

        foreach ($this->sudoku->getBoxes() as $box) {
            $box = array_filter($box, 'is_int');
            if (count(array_diff($values, $box)) != 0) return false;
        }

        return true;
    }
}