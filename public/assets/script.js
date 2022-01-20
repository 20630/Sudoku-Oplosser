let sudokuSize = 3;

const fourByFourSudokuHTML = createSudoku(2);
const nineByNineSudokuHTML = $(".sudoku.size-3").html();
const configurationHTML = $(".right").html();

function getSudoku() {
    let sudokuString = "";
    switch ($("input[name=import]:checked").attr("id")) {
        case "fill-in":
            for (let i = 0; i < ((sudokuSize ** 2) ** 2); i++) {
                let val = $("input[name=c" + i + "]").val();
                sudokuString += val == "" ? "0" : val;
            }
            break;
        case "id":
            break;
        case "text":
            sudokuString = $("input#import-text").val() == null ? "" : $("input#import-text").val();
            let remaining = ((sudokuSize ** 2) ** 2) - sudokuString.length;
            for (let i = 0; i < remaining; i++) {
                sudokuString += "0";
            }
            break;
    }
    return sudokuString;
}

function changeSudoku(size) {
    sudokuSize = size;
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
            sudoku += "<td><input type='text' name='c" + k + "' autocomplete='off'>";
            k++;
        }
        sudoku += "</tr>";
    }
    sudoku += "</tbody>";
    return sudoku;
}

function clearSudoku() {
    for (let i = 0; i < ((sudokuSize ** 2) ** 2); i++) {
        $("input[name=c" + i + "]").val("");
    }
}

const sizeInput = "input[name=size]";
$(document).on("change", sizeInput, function() {
    switch (this.id) {
        case "4x4":
            changeSudoku(2);
            break;
        case "9x9":
            changeSudoku(3);
            break;
    }
});

const importInput = "input[name=import]";
$(document).on("change", importInput, function() {
    switch (this.id) {
        case "fill-in":
            $(".import-text-input").remove();
            break;
        case "id":
            $(".import-text-input").remove();
            $(".import-option").append("<div class=\"import-text-input\"><input class=\"input\" type=\"text\" id=\"import-id\" placeholder=\"ID\"/><a>Een sudoku ID</a></div>");
            break;
        case "text":
            $(".import-text-input").remove();
            $(".import-option").append("<div class=\"import-text-input\"><input class=\"input\" type=\"text\" id=\"import-text\" placeholder=\"Tekst\"/><a>Sudoku cijfers achter elkaar geschreven met als lege vakjes een 0</a></div>");
            break;
    }
});

const sudokuInput = "td input[type=text]";
$(document).on("input", sudokuInput, function(event) {
    let value = event.target.value;

    if (isNaN(parseInt(value)) || value.length > 1) {
        event.target.value = value.length > 1 ? value.charAt(0) : "";
    }
});

$(document).on("click", "#solve", function() {
    $.ajax({
        url: "/src/solve.php",
        type: "post",
        data: {
            sudoku: getSudoku(),
            type: $("input[name=import]").attr("id"),
            size: $("input[name=size]:checked").attr("id")
        },
        success: function (response) {
            let json = JSON.parse(response);

            $(".right .options").remove();

            if (json.sudokuType == 3) {
                $(".right h2").text("Oplossing gevonden!");
                let sudoku = json.solvedSudoku.split("");
                for (let i = 0; i < sudoku.length; i++) {
                    let s = "input[name=c" + i + "]";
                    $(s).val(sudoku[i]).attr("readonly", true);
                }
            } else if (json.sudokuType == 2) {
                $(".right h2").text("Geen oplossing mogelijk!");
            }

            $("#solve").attr("id", "another-solve").html("Nog een sudoku oplossen!");

        },
        error: function(jqXHR, textStatus, errorThrown) {
            console.log(textStatus, errorThrown);
        }
    });
});

$(document).on("click", "#another-solve", function() {
    clearSudoku();
    $(".right").html(configurationHTML);
    changeSudoku(3);
});