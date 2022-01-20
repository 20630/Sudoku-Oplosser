<!doctype html>
<html lang="nl">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Sudoku oplosser</title>
    <link href="assets/style.css" rel="stylesheet">
    <script src="https://kit.fontawesome.com/a940295847.js" crossorigin="anonymous"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>

</head>

<body>
<div class="wrapper">
    <header><h1>Sudoku oplosser</h1></header>
    <div class="content">
        <div class="left">
            <table class="sudoku size-3">
                <tbody>
                    <tr>
                        <td><input type="text" name="c0" autocomplete="off">
                        <td><input type="text" name="c1" autocomplete="off">
                        <td><input type="text" name="c2" autocomplete="off">
                        <td><input type="text" name="c3" autocomplete="off">
                        <td><input type="text" name="c4" autocomplete="off">
                        <td><input type="text" name="c5" autocomplete="off">
                        <td><input type="text" name="c6" autocomplete="off">
                        <td><input type="text" name="c7" autocomplete="off">
                        <td><input type="text" name="c8" autocomplete="off">
                    </tr>
                    <tr>
                        <td><input type="text" name="c9" autocomplete="off">
                        <td><input type="text" name="c10" autocomplete="off">
                        <td><input type="text" name="c11" autocomplete="off">
                        <td><input type="text" name="c12" autocomplete="off">
                        <td><input type="text" name="c13" autocomplete="off">
                        <td><input type="text" name="c14" autocomplete="off">
                        <td><input type="text" name="c15" autocomplete="off">
                        <td><input type="text" name="c16" autocomplete="off">
                        <td><input type="text" name="c17" autocomplete="off">
                    </tr>
                    <tr>
                        <td><input type="text" name="c18" autocomplete="off">
                        <td><input type="text" name="c19" autocomplete="off">
                        <td><input type="text" name="c20" autocomplete="off">
                        <td><input type="text" name="c21" autocomplete="off">
                        <td><input type="text" name="c22" autocomplete="off">
                        <td><input type="text" name="c23" autocomplete="off">
                        <td><input type="text" name="c24" autocomplete="off">
                        <td><input type="text" name="c25" autocomplete="off">
                        <td><input type="text" name="c26" autocomplete="off">
                    </tr>
                    <tr>
                        <td><input type="text" name="c27" autocomplete="off">
                        <td><input type="text" name="c28" autocomplete="off">
                        <td><input type="text" name="c29" autocomplete="off">
                        <td><input type="text" name="c30" autocomplete="off">
                        <td><input type="text" name="c31" autocomplete="off">
                        <td><input type="text" name="c32" autocomplete="off">
                        <td><input type="text" name="c33" autocomplete="off">
                        <td><input type="text" name="c34" autocomplete="off">
                        <td><input type="text" name="c35" autocomplete="off">
                    </tr>
                    <tr>
                        <td><input type="text" name="c36" autocomplete="off">
                        <td><input type="text" name="c37" autocomplete="off">
                        <td><input type="text" name="c38" autocomplete="off">
                        <td><input type="text" name="c39" autocomplete="off">
                        <td><input type="text" name="c40" autocomplete="off">
                        <td><input type="text" name="c41" autocomplete="off">
                        <td><input type="text" name="c42" autocomplete="off">
                        <td><input type="text" name="c43" autocomplete="off">
                        <td><input type="text" name="c44" autocomplete="off">
                    </tr>
                    <tr>
                        <td><input type="text" name="c45" autocomplete="off">
                        <td><input type="text" name="c46" autocomplete="off">
                        <td><input type="text" name="c47" autocomplete="off">
                        <td><input type="text" name="c48" autocomplete="off">
                        <td><input type="text" name="c49" autocomplete="off">
                        <td><input type="text" name="c50" autocomplete="off">
                        <td><input type="text" name="c51" autocomplete="off">
                        <td><input type="text" name="c52" autocomplete="off">
                        <td><input type="text" name="c53" autocomplete="off">
                    </tr>
                    <tr>
                        <td><input type="text" name="c54" autocomplete="off">
                        <td><input type="text" name="c55" autocomplete="off">
                        <td><input type="text" name="c56" autocomplete="off">
                        <td><input type="text" name="c57" autocomplete="off">
                        <td><input type="text" name="c58" autocomplete="off">
                        <td><input type="text" name="c59" autocomplete="off">
                        <td><input type="text" name="c60" autocomplete="off">
                        <td><input type="text" name="c61" autocomplete="off">
                        <td><input type="text" name="c62" autocomplete="off">
                    </tr>
                    <tr>
                        <td><input type="text" name="c63" autocomplete="off">
                        <td><input type="text" name="c64" autocomplete="off">
                        <td><input type="text" name="c65" autocomplete="off">
                        <td><input type="text" name="c66" autocomplete="off">
                        <td><input type="text" name="c67" autocomplete="off">
                        <td><input type="text" name="c68" autocomplete="off">
                        <td><input type="text" name="c69" autocomplete="off">
                        <td><input type="text" name="c70" autocomplete="off">
                        <td><input type="text" name="c71" autocomplete="off">
                    </tr>
                    <tr>
                        <td><input type="text" name="c72" autocomplete="off">
                        <td><input type="text" name="c73" autocomplete="off">
                        <td><input type="text" name="c74" autocomplete="off">
                        <td><input type="text" name="c75" autocomplete="off">
                        <td><input type="text" name="c76" autocomplete="off">
                        <td><input type="text" name="c77" autocomplete="off">
                        <td><input type="text" name="c78" autocomplete="off">
                        <td><input type="text" name="c79" autocomplete="off">
                        <td><input type="text" name="c80" autocomplete="off">
                    </tr>
                </tbody>
            </table>
        </div>
        <span class="divider"></span>
        <div class="right">
            <h2>Configuratie</h2>
            <div class="options">
                <div class="option size-option">
                    <label class="option-label">Sudoku grootte</label>
                    <ul class="option-choices">
                        <li>
                            <input type="radio" id="4x4" name="size"/>
                            <label for="4x4">4x4</label>
                        </li>
                        <li>
                            <input type="radio" id="9x9" name="size" checked/>
                            <label for="9x9">9x9</label>
                        </li>
                    </ul>
                </div>
                <div class="option import-option">
                    <label class="option-label">Importeer</label>
                    <ul class="option-choices">
                        <li>
                            <input class="input" type="radio" id="fill-in" name="import" checked/>
                            <label for="fill-in">Invullen</label>
                        </li>
                        <li>
                            <input type="radio" id="id" name="import"/>
                            <label for="id">ID</label>
                        </li>
                        <li>
                            <input type="radio" id="text" name="import"/>
                            <label for="text">Tekst</label>
                        </li>
                    </ul>
                </div>
            </div>
            <button class="solve" id="solve">Los op!</button>
        </div>
    </div>
</div>
</body>
</html>