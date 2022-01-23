<?php
namespace j3ltr\SudokuSolver;

use Exception;

set_time_limit(0);

require 'Sudoku.php';
require 'SudokuSolver.php';
require 'SudokuStorage.php';

class ResponseCode {
    public const SOLVED = "100";
    public const SOLVED_VIA_STORAGE = "101";

    public static function getMessage(string $code) {
        switch ($code) {
            case self::SOLVED:
                return 'Sudoku solved via the algorithm.';
            case self::SOLVED_VIA_STORAGE:
                return 'Sudoku solved via found solution from storage.';
        }
    }
}

if (!validateRequest()) return;

//Response variables.
$code = null;
$id = null;
$originalSudoku = null;
$solvedSudoku = null;
$solveDuration = null;

if (isset($_GET['id'])) {
    if (!validateIdParam()) return;
    $id = $_GET['id'];
    $savedSudoku = SudokuStorage::getSavedSudokuFromId($id);

    $code = ResponseCode::SOLVED_VIA_STORAGE;
    $originalSudoku = Sudoku::fromString($savedSudoku['originalSudoku'], $savedSudoku['size']);
    $solvedSudoku = Sudoku::fromString($savedSudoku['solvedSudoku'], $savedSudoku['size']);
} else {
    if (!validateSizeParam() || !validateGridParam()) return;
    $grid = $_GET['grid'];
    $size = $_GET['size'];

    $originalSudoku = Sudoku::fromString($grid, $size);
    if (SudokuStorage::hasSavedSudokuFromOriginalSudoku($originalSudoku)) {
        $savedSudoku = SudokuStorage::getSavedSudokuFromOriginalSudoku($originalSudoku);

        $code = ResponseCode::SOLVED_VIA_STORAGE;
        $id = $savedSudoku['id'];
        $solvedSudoku = Sudoku::fromString($savedSudoku['solvedSudoku'], $savedSudoku['size']);
    } else {
        $sudokuSolver = new SudokuSolver(clone $originalSudoku);
        $sudokuSolver->solve();
        if ($sudokuSolver->getSudokuType() == SudokuType::SOLVED) {
            $code = ResponseCode::SOLVED;
            $solvedSudoku = $sudokuSolver->getSudoku();
            $id = SudokuStorage::saveSudoku($originalSudoku, $solvedSudoku);
            $solveDuration = $sudokuSolver->getSolveDuration();
        } else {
            $response = array(
                'error' => array(
                    'code' => 400,
                    'message' => 'Sudoku is unsolvable.'
                )
            );
            http_response_code(400);
            echo json_encode($response, JSON_PRETTY_PRINT);
            return;
        }
    }
}

$response = array(
    'success' => array(
        'code' => $code,
        'message' => ResponseCode::getMessage($code)
    ),
    'data' => array()
);

if ($id != null) $response['data']['id'] = $id;
$response['data']['originalSudoku'] = $originalSudoku->__toString();
$response['data']['solvedSudoku'] = $solvedSudoku->__toString();
if ($solveDuration !== null) $response['data']['solveDuration'] = $solveDuration;

http_response_code(200);
echo json_encode($response, JSON_PRETTY_PRINT);

function validateRequest(): bool {
    if ($_SERVER['REQUEST_METHOD'] != 'GET') {
        $response = array(
            'error' => array(
                'code' => 405,
                'message' => 'Allowed method: GET.'
            )
        );
        http_response_code(405);
        echo json_encode($response, JSON_PRETTY_PRINT);
        return false;
    }

    if (!isset($_GET['id']) && !isset($_GET['grid'], $_GET['size'])) {
        $missingParameters = array();
        if (!isset($_GET['grid'])) $missingParameters[] = '\'grid\'';
        if (!isset($_GET['size'])) $missingParameters[] = '\'size\'';

        $response = array(
            'error' => array(
                'code' => 400,
                'message' => 'Missing parameter(s): ' . implode(', ', $missingParameters) . '.'
            )
        );
        http_response_code(400);
        echo json_encode($response, JSON_PRETTY_PRINT);
        return false;
    }

    return true;
}

function validateIdParam(): bool {
    $id = $_GET['id'];
    if (!is_numeric($id)) {
        $response = array(
            'error' => array(
                'code' => 400,
                'message' => 'Parameter \'id\' must be a number.'
            )
        );
        http_response_code(400);
        echo json_encode($response, JSON_PRETTY_PRINT);
        return false;
    }
    if (!SudokuStorage::hasSavedSudokuFromId($id)) {
        $response = array(
            'error' => array(
                'code' => 404,
                'message' => 'The requested sudoku was not found.'
            )
        );
        http_response_code(404);
        echo json_encode($response, JSON_PRETTY_PRINT);
        return false;
    }
    return true;
}

function validateGridParam(): bool {
    $grid = $_GET['grid'];
    $size = $_GET['size'];
    try {
        Sudoku::fromString($grid, $size);
        return true;
    } catch (Exception $e) {
        $response = array(
            'error' => array(
                'code' => 400,
                'message' => 'Parameter \'grid\' is invalid.'
            )
        );
        http_response_code(400);
        echo json_encode($response, JSON_PRETTY_PRINT);
        return false;
    }
}

function validateSizeParam(): bool {
    $size = $_GET['size'];
    if (!($size == 3 || $size == 2)) {
        $response = array(
            'error' => array(
                'code' => 400,
                'message' => 'Parameter \'size\' must be \'2\' or \'3\'.'
            )
        );
        http_response_code(400);
        echo json_encode($response, JSON_PRETTY_PRINT);
        return false;
    }
    return true;
}