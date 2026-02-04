<?php
// Load CSV questions
$questions = array_map('str_getcsv', file('8-1-questions.csv'));
$headers = array_shift($questions); // remove column headers
?>

<?php foreach ($questions as $row): ?>
    <?php
        list($section, $num, $text, $a, $b, $c, $correct) = $row;
        $qname = "q" . $num;
    ?>

    <div class="question-block">
        <h3><?php echo $num . ". " . $text; ?></h3>

        <label>
            <input type="radio" name="<?php echo $qname; ?>" value="a">
            <?php echo $a; ?>
        </label><br>

        <label>
            <input type="radio" name="<?php echo $qname; ?>" value="b">
            <?php echo $b; ?>
        </label><br>

        <label>
            <input type="radio" name="<?php echo $qname; ?>" value="c">
            <?php echo $c; ?>
        </label><br>
    </div>

<?php endforeach; ?>