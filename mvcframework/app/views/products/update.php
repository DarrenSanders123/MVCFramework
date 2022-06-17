<form method="post">
    <input hidden type="number" value="<?= $data['productId']; ?>" name="product_update_productId">
    <label>Product name<br>
        <input type="text" name="product_update_productName" value="<?= $data['productName'] ?? "" ?>"
               placeholder="Enter a product name."/><br>
        <span style="color: red; font-size: small">
            <?php echo getVal()->getMessageId('productName')->firstErrorOrWarning(); ?>
        </span>
    </label>
    <br>
    <label>Product price<br>
        <input type="number" step="0.01" name="product_update_price" value="<?= $data['price'] ?? "" ?>"
               placeholder="Enter a price."><br>
        <span style="color: red; font-size: small">
            <?php echo getVal()->getMessageId('price')->firstErrorOrWarning(); ?>
        </span>
    </label>
    <br>
    <label>Category<br>
        <select name="product_update_category">
            <option selected disabled>Select a category.</option>
            <?php $data = $data ?? array();

            foreach ($data['categories'] as $category): ?>
                <option value="<?php echo $category->CategoryId ?>" <?= $category->CategoryId == ($data['category'] ?? "") ? 'selected' : '' ?> ><?php echo $category->CategoryName ?></option>
            <?php endforeach; ?>

        </select>
    </label><br>
    <span style="color: red; font-size: small"><?php echo getVal()->getMessageId('category')->firstErrorOrWarning(); ?></span>
    <br>
    <button type="submit">Update</button>
</form>