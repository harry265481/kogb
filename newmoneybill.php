<?php
include_once 'header/header.php';   
?>
<head><style>@font-face{font-family: newspaper; src: url("assets/OldNewspaperTypes.ttf")} #bill-output{font-family: newspaper} .drop-letter {float:left; font-size:250%; line-height:80%;} </style></head>
<form id="bill-form" action="newmoneybill.php" method="post">
    <div class="row mt-3">
        <div class="col-sm-0 col-md-2 col-lg-3 mt-3">
        </div>
        <div class="col-sm-12 col-md-8 col-lg-6 mt-3" id="form-body">
            <p>What is the bill for?</p>
            <select id="purpose" name="type" onchange="update()">
                <option>Select...</option>
                <option value="1">To pay off national debt</option>
                <option value="2">To supply the military</option>
            </select><br>
            <h6 class="mt-3">Short Title</h6>
            <div class="input-group">
                <p id="shortTitle"></p>
            </div>

            <label id="amount-label" for="amount" class="form-label"></label>
            <div class="input-group">
                <input id="amount" class="form-control" type="number" value="0" name="amount" oninput="update()">
            </div>

            <label id="method-label" for="method" class="form-label"></label>
            <div id="method-group" class="input-group"></div>

            <label id="source-label" for="source" class="form-label"></label>
            <div id="source-group" class="input-group"></div>
            <div id="source-help" class="form-text"></div>

            <h3 class="mt-3">Final Text</h3>
            <div id="bill-output">
                <p class="text-center" id="oShortAct"></p>
                <p class="text-center" id="oLongAct"></p>
                <hr>
                <span id="oBody"></span>
            </div>
            <input id="outputText" name="text" type="hidden">
            <button class="btn btn-primary mt-2" type="submit" width="30%">Submit</button>
        <div class="col-sm-0 col-md-2 col-lg-3 mt-3">
        </div>
    </div>
</form>
<script>
    function update() {
        var select = document.getElementById("purpose");
        var p = document.getElementById("shortTitle");

        var shortTitle = document.getElementById("shortTitle").innerHTML;
        document.getElementById("oShortAct").innerHTML = shortTitle;
        var amount = document.getElementById("amount").value;
        var amountWords = toWords(amount)
        var lt = "";
        var opening = "";
        var methodWords = "";
        var methodBody = "";
        var sourceWords = "";
        var sourceBody = "";
        if(select.value == 1) {
            p.innerHTML = "National Debt Act"
            document.getElementById("amount-label").innerHTML = "Amount to be paid off";
            var m = document.getElementById("method-group");
            if(document.getElementById("method") == null) {
                document.getElementById("method-label").innerHTML = "How will the money be paid";
                var methodSelect = document.createElement("select");
                m.appendChild(methodSelect);
                methodSelect.setAttribute("id", "method");
                methodSelect.setAttribute("name", "method");
                methodSelect.setAttribute("onchange", "update()");
                var optionSel = document.createElement("option");
                methodSelect.appendChild(optionSel);
                optionSel.innerHTML = "Select...";

                var option1 = document.createElement("option");
                methodSelect.appendChild(option1);
                option1.setAttribute("value", 1);
                option1.innerHTML = "Out of a government account";
            } else {
                var method = document.getElementById("method");
                var sourceGroup = document.getElementById("source-group");
                if(method.value == 1) {
                    methodWords = "out of the";
                    if(document.getElementById("source") == null) {
                        var sourceSel = document.createElement("select");
                        sourceGroup.appendChild(sourceSel);
                        sourceSel.setAttribute("id", "source");
                        sourceSel.setAttribute("name", "source");
                        sourceSel.setAttribute("onchange", "update()");

                        var sourceOptionsSel = document.createElement("option");
                        sourceSel.appendChild(sourceOptionsSel);
                        sourceOptionsSel.innerHTML = "Select...";

                        var sourceOption1 = document.createElement("option");
                        sourceSel.appendChild(sourceOption1);
                        sourceOption1.setAttribute("value", 1);
                        sourceOption1.innerHTML = "Sinking Fund";
                    } else {
                        if(document.getElementById("source").value == 1) {
                            sourceWords = "sinking fund, transferrable at the Bank of England";
                            sourceBody = "That by or out of such monies as now are, or shall from time to time be and remain in the receipt of the exchequer, of the said surplusses, excesses, or overplus monies, commonly called <i>The Sinking Fund</i> (after paying or reserving sufficient to pay all such sum and sums of money as have been directed by any former act or acts of parliament to be paid out of the same) there shall and may be issued, and applied, a sum not exceeding " + amountWords + " pounds, for and towards the debt that is currently held in His Majesty's name by the Bank of England ";
                        }
                    }
                }
            }

            lt = "An act for granting to his Majesty the sum of " + amountWords + " " + methodWords + " " + sourceWords;
            opening = "<span class=\"drop-letter\">W</span>e, your Majesty's most dutiful and loyal subjects, the commons of Great Britain in parliament assembled, being desirous to raise the necessary supplies which we have cheerfully granted to your Majesty, in the easiest manner we are able for the benefit of your subjects, have freely and voluntarily given and granted, and by this act do give and grant unto your Majesty the sum of " + amountWords + ", to be raised in such manner and form as is herein after directed; and to that end do most humbly beseech your Majefty, that it may be enacted; and be it enacted by the King's most excellent Majesty, by and with the advice and consent of the lords spiritual and temporal, and commons, in this present parliament assembled, and by the authority of the same, ";
        } else if(select.value == 2) {
            p.innerHTML = "Supply Act"
            document.getElementById("amount-label").innerHTML = "Amount supplied";
            var m = document.getElementById("method-group");
            if(document.getElementById("method") == null) {
                document.getElementById("method-label").innerHTML = "How will the money be paid";
                var methodSelect = document.createElement("select");
                m.appendChild(methodSelect);
                methodSelect.setAttribute("id", "method");
                methodSelect.setAttribute("name", "method");
                methodSelect.setAttribute("onchange", "update()");
                var optionSel = document.createElement("option");
                methodSelect.appendChild(optionSel);
                optionSel.innerHTML = "Select...";

                var option1 = document.createElement("option");
                methodSelect.appendChild(option1);
                option1.setAttribute("value", 1);
                option1.innerHTML = "Out of a government account";
            } else {
                var method = document.getElementById("method");
                var sourceGroup = document.getElementById("source-group");
                if(method.value == 1) {
                    methodWords = "out of the";
                    if(document.getElementById("source") == null) {
                        var sourceSel = document.createElement("select");
                        sourceGroup.appendChild(sourceSel);
                        sourceSel.setAttribute("id", "source");
                        sourceSel.setAttribute("name", "source");
                        sourceSel.setAttribute("onchange", "update()");

                        var sourceOptionsSel = document.createElement("option");
                        sourceSel.appendChild(sourceOptionsSel);
                        sourceOptionsSel.innerHTML = "Select...";

                        var sourceOption1 = document.createElement("option");
                        sourceSel.appendChild(sourceOption1);
                        sourceOption1.setAttribute("value", 1);
                        sourceOption1.innerHTML = "Sinking Fund";
                    } else {
                        if(document.getElementById("source").value == 1) {
                            var year = <?php echo $year ?>;
                            sourceWords = "sinking fund, for the service of the year " + toWords(year);
                            sourceBody = "That by or out of such monies as now are, or shall from time to time be and remain in the receipt of the exchequer, of the said surplusses, excesses, or overplus monies, commonly called <i>The Sinking Fund</i> (after paying or reserving sufficient to pay all such sum and sums of money as have been directed by any former act or acts of parliament to be paid out of the same) there shall and may be issued, and applied, a sum not exceeding " + amountWords + " pounds, for and towards the debt that is currently held in His Majesty's name by the Bank of England ";
                        }
                    }
                }
            }
            opening = "<span class=\"drop-letter\">W</span>e, your Majesty's most dutiful and loyal subjects, the commons of Great Britain in parliament assembled, being desirous to raise the necessary supplies which we have cheerfully granted to your Majesty in this session of parliament, for the service of the year " + toWords(year) + ", in the easiest manner we are able, for the benefit of your Majesty's subjects, and also to use such ways and means therein, as that your Majest may have the better and more speedy effect of the said supplies, have resolved to give and grant unto your Majesty the sum of " + amountWords + " pounds, out of the surplus, excesses, and overp[lus monies, commonly called the sinking fund and to that end do most humbly beseech your Majesty, that it may be enacted; and be it enacted by the King's most excellent Majesty, by and with the advice and consent of the lords spiritual and temporal, and commons, in this present parliament assembled, and by the authority of the same, ";
            lt = "An act for granting to his Majesty the sum of " + amountWords + " " + methodWords + " " + sourceWords;
        }
        
        document.getElementById("oLongAct").innerHTML = lt;
        document.getElementById("oBody").innerHTML = opening + sourceBody;
        //document.getElementById("outputText").value = fulloutput;
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

    var TEN = 10;
    var ONE_HUNDRED = 100;
    var ONE_THOUSAND = 1000;
    var ONE_MILLION = 1000000;
    var ONE_BILLION = 1000000000;           //         1.000.000.000 (9)
    var ONE_TRILLION = 1000000000000;       //     1.000.000.000.000 (12)
    var ONE_QUADRILLION = 1000000000000000; // 1.000.000.000.000.000 (15)
    var MAX = 9007199254740992;             // 9.007.199.254.740.992 (15)

    var LESS_THAN_TWENTY = [
        'zero', 'one', 'two', 'three', 'four', 'five', 'six', 'seven', 'eight', 'nine', 'ten',
        'eleven', 'twelve', 'thirteen', 'fourteen', 'fifteen', 'sixteen', 'seventeen', 'eighteen', 'nineteen'
    ];

    var TENTHS_LESS_THAN_HUNDRED = [
        'zero', 'ten', 'twenty', 'thirty', 'forty', 'fifty', 'sixty', 'seventy', 'eighty', 'ninety'
    ];
    function toWords(number) {
        var words;
        var num = parseInt(number, 10);
        words = generateWords(num);
        return words;
    }

    function generateWords(number) {
        var remainder, word,
            words = arguments[1];

        // We’re done
        if (number === 0) {
            return !words ? 'zero' : words.join(' ').replace(/,$/, '');
        }
        // First run
        if (!words) {
            words = [];
        }
        // If negative, prepend “minus”
        if (number < 0) {
            words.push('minus');
            number = Math.abs(number);
        }

        if (number < 20) {
            remainder = 0;
            word = LESS_THAN_TWENTY[number];

        } else if (number < ONE_HUNDRED) {
            word = 'and ';
            remainder = number % TEN;
            word += TENTHS_LESS_THAN_HUNDRED[Math.floor(number / TEN)];
            // In case of remainder, we need to handle it here to be able to add the “-”
            if (remainder) {
                word += '-' + LESS_THAN_TWENTY[remainder];
                remainder = 0;
            }

        } else if (number < ONE_THOUSAND) {
            remainder = number % ONE_HUNDRED;
            word = generateWords(Math.floor(number / ONE_HUNDRED)) + ' hundred';

        } else if (number < ONE_MILLION) {
            remainder = number % ONE_THOUSAND;
            word = generateWords(Math.floor(number / ONE_THOUSAND)) + ' thousand,';

        } else if (number < ONE_BILLION) {
            remainder = number % ONE_MILLION;
            word = generateWords(Math.floor(number / ONE_MILLION)) + ' million,';

        } else if (number < ONE_TRILLION) {
            remainder = number % ONE_BILLION;
            word = generateWords(Math.floor(number / ONE_BILLION)) + ' billion,';

        } else if (number < ONE_QUADRILLION) {
            remainder = number % ONE_TRILLION;
            word = generateWords(Math.floor(number / ONE_TRILLION)) + ' trillion,';

        } else if (number <= MAX) {
            remainder = number % ONE_QUADRILLION;
            word = generateWords(Math.floor(number / ONE_QUADRILLION)) +
            ' quadrillion,';
        }

        words.push(word);
        return generateWords(remainder, words);
    }
</script>
<?php include_once "footer.php"; ?>