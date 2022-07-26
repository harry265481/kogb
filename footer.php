<?php mysqli_close($link); ?>
                </main>
            </div>
        </div>
        <script>
            function startTime() {
                var startDate = new Date('<?php echo $time ?> GMT-0400');
                var year = startDate.getFullYear();
                var now = new Date();

                //RL ms since time start x a for dilation
                var ct = (now - startDate) * <?php echo $a ?>;
                //ct = Math.floor(ct);
                var timestamp = new Date(ct);
                timestamp.setFullYear(<?php echo $year ?>);
                //Time started in 1750 so add 1750
                document.getElementById('date').innerHTML = ordinal_suffix_of(timestamp.getUTCDate()) + ", " + month(timestamp.getUTCMonth()) + ", " + timestamp.getUTCFullYear();
                setTimeout(startTime, 1000);
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

            function month(i) {
                let months = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];
                return months[i];
            }
        </script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0/dist/js/bootstrap.bundle.min.js" integrity="sha384-A3rJD856KowSb7dwlZdYEkO39Gagi7vIsF0jrRAoQmDKKtQBHUuLZ9AsSv4jD4Xa" crossorigin="anonymous"></script>
    </body>
</html>