const fourByFourSudokuHTML = createSudoku(2);
const nineByNineSudokuHTML = $(".sudoku.size-3").html();
const configurationHTML = $(".right").html();

function getSudoku() {
    let sudokuString = "";
    switch ($("input[name=import]:checked").attr("id")) {
        case "fill-in":
            for (let i = 0; i < ((getSudokuSize() ** 2) ** 2); i++) {
                let val = $("input[name=c" + i + "]").val();
                sudokuString += val == "" ? "0" : val;
            }
            break;
        case "id":
            sudokuString = $("input#import-id").val() == null ? "" : $("input#import-id").val();
            break;
        case "text":
            sudokuString = $("input#import-text").val() == null ? "" : $("input#import-text").val();
            let remaining = ((getSudokuSize() ** 2) ** 2) - sudokuString.length;
            for (let i = 0; i < remaining; i++) {
                sudokuString += "0";
            }
            break;
    }
    return sudokuString;
}

function getSudokuSize() {
    let size;
    if ($(".sudoku").hasClass("size-2")) size = 2;
    if ($(".sudoku").hasClass("size-3")) size = 3;
    return size;
}

function changeSudokuSize(size) {
    if (size == 3) {
        $(".sudoku").html(nineByNineSudokuHTML);
        $(".sudoku").removeClass("size-2");
        $(".sudoku").addClass("size-3");
    } else {
        $(".sudoku").html(fourByFourSudokuHTML);
        $(".sudoku").removeClass("size-3");
        $(".sudoku").addClass("size-2");
    }
}

function createSudoku(size) {
    let sudoku = "<tbody>";
    let k = 0;
    for (let i = 0; i < size ** 2; i++) {
        sudoku += "<tr>";
        for (let j = 0; j < size ** 2; j++) {
            sudoku += "<td><input type=\"text\" name=\"c" + k + "\" autocomplete=\"off\">";
            k++;
        }
        sudoku += "</tr>";
    }
    sudoku += "</tbody>";
    return sudoku;
}

function clearSudoku() {
    for (let i = 0; i < ((getSudokuSize() ** 2) ** 2); i++) {
        $("input[name=c" + i + "]").val("");
    }
}

function fillSudoku(grid) {
    for (let i = 0; i < grid.length; i++) {
        $("input[name=c" + i + "]").val(grid[i]).attr("readonly", true);
    }
}

function handleResponse(json) {
    $(".right").empty();

    let isSuccess = json.success != undefined;
    if (isSuccess) {
        $(".right").append("<h2>Oplossing gevonden!</h2>");

        sudokuId = json.data.id;

        let message;
        switch (json.success.code) {
            case "100":
                message = "<div class=\"description\"><p>Er is een oplossing gevonden voor deze sudoku!<br>Het algoritme heeft er <b>"
                    + json.data.solveDuration + " ms</b> over gedaan.</p></div><div class=\"description\">" +
                    "<p>Opgeloste sudokus worden opgeslagen zodat de oplossing de volgende keer sneller vekregen kan worden.<br>" +
                    "Deze sudoku is opgeslagen met id <b>" + json.data.id + "</b>.<br><a id=\"copy-id\">Kopieer id</a><br></p></div>";
                break;
            case "101":
                message = "<div class=\"description\"><p>Er is een oplossing gevonden voor deze sudoku! Deze sudoku is al eerder een keer opgelost," +
                    " dus heeft het algoritme hier geen werk gedaan.</p></div><div class=\"description\"><p>Deze oplossing staat in de database met id <b>"
                    + json.data.id + "</b>.<br><a id=\"copy-id\">Kopieer id</a><br></p></div>";
                break;
        }

        $(".right").append(message + "<button class=\"cta-button\" id=\"another-solve\">Nog een sudoku oplossen!</button>");

        let grid = json.data.solvedSudoku.split("");
        if (getSudokuSize() == 2 && grid.length != 16) changeSudokuSize(3);
        if (getSudokuSize() == 3 && grid.length != 81) changeSudokuSize(2);
        fillSudoku(grid);
    } else {
        $(".right").append("<h2>Er is iets misgegaan!</h2>");

        let message;
        switch (json.error.message) {
            case "Sudoku is unsolvable.":
                message = "Deze sudoku is onoplosbaar!";
                break;
            case "The requested sudoku was not found.":
                message = "Er is geen sudoku gevonden met het ingevulde id!";
                break;
            case "Parameter 'id' must be a number.":
                message = "Het ingevulde id moet een nummer zijn!";
                break;
            case "Parameter 'grid' is invalid.":
                message = "De ingevulde sudoku is niet geldig!";
                break;
            default:
                message = "Er is een onbekende fout opgetreden:<br>\"" + json.error.message + "\"";
        }

        $(".right").append("<div class=\"description\"><p>" + message + "</p></div>" +
            "<button class=\"cta-button\" id=\"another-solve\">Ga terug</button>");
    }
}

const sizeInput = "input[name=size]";
$(document).on("change", sizeInput, function() {
    $("input[name=size]#9x9").attr("checked", false);
    switch (this.id) {
        case "4x4":
            changeSudokuSize(2);
            break;
        case "9x9":
            changeSudokuSize(3);
            break;
    }
});

const importInput = "input[name=import]";
$(document).on("change", importInput, function() {
    $("input[name=import]#fill-in").attr("checked", false);
    $(".import-text-input").remove();
    switch (this.id) {
        case "fill-in":
            break;
        case "id":
            $(".import-option").append("<div class=\"import-text-input\"><input class=\"input\" type=\"text\" id=\"import-id\"" +
                "placeholder=\"ID\"/><a>Een sudoku ID</a></div>");
            break;
        case "text":
            $(".import-option").append("<div class=\"import-text-input\"><input class=\"input\" type=\"text\" id=\"import-text\"" +
                "placeholder=\"Tekst\"/><a>Sudoku cijfers achter elkaar geschreven met als lege vakjes een 0</a></div>");
            break;
    }
});

const sudokuInput = "td input[type=text]";
$(document).on("input", sudokuInput, function(event) {
    let value = event.target.value;

    if (isNaN(parseInt(value)) || value.length > 1 || (getSudokuSize() == 2 && parseInt(value) > 4)) {
        event.target.value = value.length > 1 ? value.charAt(0) : "";
    }
});

const solveButton = "#solve";
$(document).on("click", solveButton, function() {
    if ($("input[name=import]:checked").attr("id") == "id") {
        data = {
            id: getSudoku()
        };
    } else {
        data = {
            grid: getSudoku(),
            size: getSudokuSize()
        };
    }

    $.ajax({
        url: "/src/solve.php",
        type: "get",
        data: data,
        success: function (response) {
            let json = JSON.parse(response);
            handleResponse(json);
        },
        error: function(jqXHR) {
            let json = JSON.parse(jqXHR.responseText);
            handleResponse(json);
        }
    });
});

const anotherSolveButton = "#another-solve";
$(document).on("click", anotherSolveButton, function() {
    clearSudoku();
    $(".right").html(configurationHTML);
    changeSudokuSize(3);
});

const copyIdButton = "#copy-id";
let sudokuId;
$(document).on("click", copyIdButton, function() {
    navigator.clipboard.writeText(sudokuId);
    $("#copy-id").text("Id gekopieerd!");
});