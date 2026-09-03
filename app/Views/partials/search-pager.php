<?php
/**
 * Renders $searchPager (see build_search_pager() in seo_helper.php) with the
 * same <nav><ul class="pagination"> markup CI4's own default_full pager
 * template uses, so it picks up the site's existing pagination CSS without
 * any new styling.
 */
?>
<?php if (!empty($searchPager) && $searchPager['totalPages'] > 1): ?>
<nav aria-label="Pagination">
    <ul class="pagination">
        <?php if ($searchPager['previous']): ?>
            <li><a href="<?= esc($searchPager['first'], 'attr') ?>" aria-label="First">First</a></li>
            <li><a href="<?= esc($searchPager['previous'], 'attr') ?>" aria-label="Previous">Previous</a></li>
        <?php endif; ?>

        <?php foreach ($searchPager['links'] as $link): ?>
            <li <?= $link['active'] ? 'class="active"' : '' ?>>
                <a href="<?= esc($link['url'], 'attr') ?>"><?= (int) $link['page'] ?></a>
            </li>
        <?php endforeach; ?>

        <?php if ($searchPager['next']): ?>
            <li><a href="<?= esc($searchPager['next'], 'attr') ?>" aria-label="Next">Next</a></li>
            <li><a href="<?= esc($searchPager['last'], 'attr') ?>" aria-label="Last">Last</a></li>
        <?php endif; ?>
    </ul>
</nav>
<?php endif; ?>
