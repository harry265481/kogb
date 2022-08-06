<?php
include_once 'header/header.php';
?>
<head><style>@font-face{font-family: newspaper; src: url("assets/OldNewspaperTypes.ttf")} #bill-output{font-family: newspaper}</style></head>
<form id="bill-form" action="newbill.php" method="post">
    <div class="row mt-3">
        <div class="col-sm-0 col-md-2 col-xl-3 mt-3">
        </div>
        <div class="col-sm-12 col-md-8 col-xl-6 mt-3" id="form-body">
            <label for="LongTitle" class="form-label">Long Title</label>
            <div class="input-group">
                <span class="input-group-text">An act</span><input id="LongTitle" class="form-control" type="text" name="long-title" oninput="update()">
            </div>
            <div class="form-text">This should explain the aim of the bill in a sentence</div>
            <div class="form-text">Example: "An act to encourage the importation of pig and bar iron from His Majesty's colonies in America"</div>
        
            <label for="Justification" class="form-label">Justification</label>
            <div class="input-group">
                <span class="input-group-text">WHEREAS</span><input id="Justification" class="form-control" type="text" name="justification" oninput="update()">
            </div>
            <div class="form-text">This should explain why it's needed.</div>
            <div class="form-text">Example: "WHEREAS the importation of bar iron from His Majesty's colonies in America into the port of London and the importation of pig iron from the said colonies would be advantageous to the colonies and the people of Great Britain"</div>
            
            <label class="form-label">Enacting Clause</label>
            <div class="form-text">This is automatically inserted between the justification and the first clause</div>
            <div class="input-group">
                <span id="enacting" class="border border-light">be it therefore enacted by the King's most excellent Majesty, by and with the advice and consent of the lords spiritual and temporal, and commons, in this present parliament assembled, and by the authority of the same, </span>
            </div>
            <div id="paragraphs">
                <label class="form-label">1st Paragraph</label>
                <textarea class="form-control bill-paragraph" oninput="update()"></textarea>
                <div class="form-text">Each paragraph should explain one concept or item of the bill</div>
            </div>

            <a href="#" class="btn btn-primary mt-2" width="30%" onclick="addParagraph()">Add Paragraph</a>
            <h3 class="mt-3">Final Text</h3>
            <div id="bill-output">
                <p id="oLongAct"></p>
                <hr>
                <p id="oBody"></p>
                <p id="p2"></p>
            </div>
            <button class="btn btn-primary mt-2" type="submit" width="30%">Submit</button>
        <div class="col-sm-0 col-md-2 col-xl-3 mt-3">
        </div>
    </div>
</form>
<script>
    function update() {
        var longTitle = document.getElementById("LongTitle").value;
        document.getElementById("oLongAct").innerHTML = "An act " + longTitle;

        var justification = document.getElementById("Justification").value;
        var enacting = document.getElementById("enacting").innerHTML;
        
        var ps = document.querySelectorAll(".bill-paragraph");
        var i = ps.length;
        for (var j = 1; j <= i; j++) {
            var k = ps.item(j - 1);
            var num = "p" + (j + 1);
            if(j == 1) {
                document.getElementById("oBody").innerHTML = "<i>WHEREAS " + justification + ";</i> " + enacting + " " + k.value;
                //document.getElementById(num).innerHTML = k.value;
            } else {
                document.getElementById(num).innerHTML = convertToRoman(j) + ". And be it further enacted " + k.value;
            }
        }
    }

    function addParagraph() {
        var form = document.getElementById("paragraphs");
        
        var label = document.createElement("label");
        form.appendChild(label);
        
        var textArea = document.createElement("textarea");
        form.appendChild(textArea);
        textArea.className = "form-control bill-paragraph";
        textArea.setAttribute("oninput", "update()");
        
        var i = document.querySelectorAll(".bill-paragraph").length;
        label.innerHTML = ordinal_suffix_of(i) + " Paragaph";
        textArea.className = "form-control bill-paragraph";
        
        var output = document.getElementById("bill-output");
        var p = document.createElement("p");
        output.appendChild(p);
        p.id = "p" + (i + 1);

    }

    function ordinal_suffix_of(i) {
        var j = i % 10,
            k = i % 100;
        if (j == 1 && k != 11) {
            return i + "st";
        }
        if (j == 2 && k != 12) {
            return i + "nd";
        }
        if (j == 3 && k != 13) {
            return i + "rd";
        }
        return i + "th";
    }

    function convertToRoman(num) {
  var roman = {
    M: 1000,
    CM: 900,
    D: 500,
    CD: 400,
    C: 100,
    XC: 90,
    L: 50,
    XL: 40,
    X: 10,
    IX: 9,
    V: 5,
    IV: 4,
    I: 1
  };
  var str = '';

  for (var i of Object.keys(roman)) {
    var q = Math.floor(num / roman[i]);
    num -= q * roman[i];
    str += i.repeat(q);
  }

  return str;
}
</script>
<?php include_once "footer.php"; ?>