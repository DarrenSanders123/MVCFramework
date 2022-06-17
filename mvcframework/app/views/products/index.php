<?php
$data = $data ?? array();

$page = $_GET['page'] ?? 1;
$back = $page - 1;
$next = $page + 1;

$back_d = $page == 1 ? 'disabled' : '';
?>

    <a href="?page=<?= $back ?>">
        <button <?= $back_d ?>>back</button>
    </a>
    <a href="?page=<?= $next ?>">
        <button>next</button>
    </a>

    <a href="products/create">
        <button>New product</button>
    </a>
<?php echo($data['products']); ?>