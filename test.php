<?php
include("../../lib/inc/chartphp_dist.php");
$p = new chartphp();
$p->data = array(
    array(
        array("2010/12",48.25),
        array("2011/01",238.75),
        array("2011/02",95.50),
        array("2011/03",300.50),
        array("2011/04",286.80),
        array("2011/05",148.25),
        array("2011/06",128.75),
        array("2011/07",95.50)
        )
    );
$p->chart_type = "bar";
$out = $p->render("c1");
?>

<!DOCTYPE html>
<html>
    <head>
        <link rel="stylesheet" href="../../lib/js/chartphp.css">
        <script src="../../lib/js/jquery.min.js"></script>
        <script src="../../lib/js/chartphp.js"></script>
    </head>
    <body>
        <div>
            <?php echo $out; ?>
        </div>
    </body>
</html>

 