<?php

$terms = $data['terms'];
$selected_term_id = $data['selected_term_id'];
$taxonomy_name = $data['taxonomy_name'];
$label = $data['label'];
?>

<select name="<?php echo esc_attr($taxonomy_name) ?>" id="<?php echo esc_attr($taxonomy_name) ?>" class="postform">
    <option value="">Select <?php echo esc_attr($label) ?> </option>

    <?php
    foreach ($terms as $term) {
        $selected = selected($selected_term_id, $term->term_id, false);
    ?>
        <option value="<?php echo esc_attr($term->term_id) ?>" <?php echo $selected; ?>><?php echo esc_html($term->name) ?></option>
    <?php
    }
    ?>

    <option value="add_new">Add New <?php echo esc_attr($label) ?> </option>
</select>

<div id="new_<?php echo esc_attr($taxonomy_name) ?>_input" style="display:none;">
    <input type="text" name="new_<?php echo esc_attr($taxonomy_name) ?>" id="new_<?php echo esc_attr($taxonomy_name) ?>" placeholder="Enter New <?php echo esc_attr($label) ?> Name" />
</div>

<script type="text/javascript">
    document.getElementById('<?php echo esc_attr($taxonomy_name) ?>').addEventListener('change', function() {
        var newTaxanomyInput = document.getElementById('new_<?php echo esc_attr($taxonomy_name) ?>_input');
        if (this.value === 'add_new') {
            newTaxanomyInput.style.display = 'block';
        } else {
            newTaxanomyInput.style.display = 'none';
        }
    });
</script>