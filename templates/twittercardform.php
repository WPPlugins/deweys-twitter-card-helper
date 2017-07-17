<?php
  global $post;

  $twitter_title = get_post_meta($post->ID, 'twitter_title', true);
  if (!$twitter_title) $twitter_title = '';

  $twitter_desc = get_post_meta($post->ID, 'twitter_desc', true);
  if (!$twitter_desc) $twitter_desc = false;

  $twitter_type = get_post_meta($post->ID, 'twitter_type', true);
  if (!$twitter_type) $twitter_type = '';
?>
<fieldset class="meta-fieldset">
  <legend>Twitter Summary Card Info</legend>

  <div class="meta-item">
    <label for="twitter_title">Title (Defaults to Post Title)</label>
    <input type="text" class="widefat" name="twitter_title" value="<?= ($twitter_title) ? htmlspecialchars($twitter_title) : '' ?>">
  </div>

  <div class="meta-item">
    <label for="twitter_desc">Description (Defaults to Post Excerpt)</label>
    <textarea name="twitter_desc" class="widefat" name="twitter_desc"><?= ($twitter_desc) ? htmlspecialchars($twitter_desc) : '' ?></textarea>
  </div>

  <div class="meta-item">
    <label for="twitter_type">Type (Defaults to 'summary')</label>
    <input type="text" class="widefat" name="twitter_type" value="<?= ($twitter_type) ? htmlspecialchars($twitter_type) : '' ?>">
  </div>

  <div class="meta-item">
    <span class="note">*please ensure you've whitelisted your domain for summary cards from twitter.com</span>
  </div>
</fieldset>
