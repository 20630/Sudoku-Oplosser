<?php
set_time_limit(0);

require 'SudokuSolver.php';
require 'Sudoku.php';

use j3ltr\SudokuSolver\Sudoku;
use j3ltr\SudokuSolver\SudokuSolver;
use j3ltr\SudokuSolver\SudokuType;

if (!isset($_POST)) return;

$size = $_POST['size'] == '9x9' ? 3 : 2;

$originalSudoku = Sudoku::fromString($_POST['sudoku'], $size);
$sudoku = Sudoku::fromString($_POST['sudoku'], $size);
$sudokuSolver = new SudokuSolver($sudoku);
$solvedSudoku = $sudokuSolver->solve()->getSudoku();

echo '{
"originalSudoku": "' . $originalSudoku->__toString() . '"' . ($sudokuSolver->getSudokuType() == SudokuType::SOLVED ? ',
"solvedSudoku": "' . $solvedSudoku->__toString() . '"' : '') . ',
"sudokuSize": '. $solvedSudoku->getSize() . ',
"sudokuType": '. $sudokuSolver->getSudokuType() . ($sudokuSolver->getSudokuType() == SudokuType::SOLVED ? ',
"solveDuration": ' . $sudokuSolver->getSolveDuration() : '') .
'}';