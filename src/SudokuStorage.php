<?php
namespace j3ltr\SudokuSolver;

class SudokuStorage {

    public static function getSavedSudokus() {
        $sudokusString = file_get_contents('sudokus.json');
        $json = json_decode($sudokusString, true);
        return $json;
    }

    public static function getSavedSudokuFromId(int $id) {
        foreach (SudokuStorage::getSavedSudokus()['sudokus'] as $sudoku) {
            if ($sudoku['id'] == $id) return $sudoku;
        }
        return null;
    }

    public static function getSavedSudokuFromOriginalSudoku(string $originalSudoku) {
        foreach (SudokuStorage::getSavedSudokus()['sudokus'] as $sudoku) {
            if ($sudoku['originalSudoku'] === $originalSudoku) return $sudoku;
        }
        return null;
    }

    public static function hasSavedSudokuFromId(int $id): bool {
        foreach (SudokuStorage::getSavedSudokus()['sudokus'] as $sudoku) {
            if ($sudoku['id'] == $id) return true;
        }
        return false;
    }

    public static function hasSavedSudokuFromOriginalSudoku(string $original): bool {
        foreach (SudokuStorage::getSavedSudokus()['sudokus'] as $sudoku) {
            if ($sudoku['originalSudoku'] === $original) return true;
        }
        return false;
    }

    public static function saveSudoku(Sudoku $originalSudoku, Sudoku $solvedSudoku) {
        $id = end(self::getSavedSudokus()['sudokus']) ? end(self::getSavedSudokus()['sudokus'])['id'] + 1 : 1;
        $size = $originalSudoku->getSize();
        $sudoku = array(
            'id' => $id,
            'size' => $size,
            'originalSudoku' => $originalSudoku->__toString(),
            'solvedSudoku' => $solvedSudoku->__toString()
        );
        $json = SudokuStorage::getSavedSudokus();
        $json['sudokus'][] = $sudoku;
        file_put_contents('sudokus.json', json_encode($json, JSON_PRETTY_PRINT));
        return $id;
    }
}