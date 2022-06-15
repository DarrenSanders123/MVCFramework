<a href="?page=<?php echo ($_GET['page'] ?? 1) - 1?>"><button <?php if (!isset($_GET['page']) or $_GET['page'] == 1) echo 'disabled' ?>>back</button></a>
<a href="?page=<?php echo ($_GET['page'] ?? 1) + 1 ?>"><button>next</button></a>

<?php print_r($data['products']); ?>