<p>
    <label for="rating">Rating</label><br>
    <input type="number" id="rating" name="rating" value="<?php echo esc_attr($rating); ?>" min="1" max="5">
</p>
<p>
    <label for="published_date">Published Date</label><br>
    <input type="date" id="published_date" name="published_date" value="<?php echo esc_attr($published_date); ?>">
</p>