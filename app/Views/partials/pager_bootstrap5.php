<?php

/**
 * Custom Bootstrap 5 Pagination Template
 * @var \CodeIgniter\Pager\PagerRenderer $pager
 */
$pager->setSurroundCount(2);
$pageCount = $pager->getPageCount();

// Don't show pagination if only 1 page
if ($pageCount <= 1) return;
?>

<nav aria-label="Pagination">
    <ul class="pagination pagination-sm justify-content-center mb-0">
        
        <?php if ($pager->hasPrevious()): ?>
            <li class="page-item">
                <a href="<?= $pager->getFirst() ?>" class="page-link" aria-label="First" title="Halaman Pertama">
                    <i class="bi bi-chevron-double-left"></i>
                </a>
            </li>
            <li class="page-item">
                <a href="<?= $pager->getPrevious() ?>" class="page-link" aria-label="Previous" title="Sebelumnya">
                    <i class="bi bi-chevron-left"></i>
                </a>
            </li>
        <?php else: ?>
            <li class="page-item disabled">
                <span class="page-link"><i class="bi bi-chevron-double-left"></i></span>
            </li>
            <li class="page-item disabled">
                <span class="page-link"><i class="bi bi-chevron-left"></i></span>
            </li>
        <?php endif; ?>

        <?php foreach ($pager->links() as $link): ?>
            <li class="page-item <?= $link['active'] ? 'active' : '' ?>">
                <a href="<?= $link['uri'] ?>" class="page-link">
                    <?= $link['title'] ?>
                </a>
            </li>
        <?php endforeach; ?>

        <?php if ($pager->hasNext()): ?>
            <li class="page-item">
                <a href="<?= $pager->getNext() ?>" class="page-link" aria-label="Next" title="Selanjutnya">
                    <i class="bi bi-chevron-right"></i>
                </a>
            </li>
            <li class="page-item">
                <a href="<?= $pager->getLast() ?>" class="page-link" aria-label="Last" title="Halaman Terakhir">
                    <i class="bi bi-chevron-double-right"></i>
                </a>
            </li>
        <?php else: ?>
            <li class="page-item disabled">
                <span class="page-link"><i class="bi bi-chevron-right"></i></span>
            </li>
            <li class="page-item disabled">
                <span class="page-link"><i class="bi bi-chevron-double-right"></i></span>
            </li>
        <?php endif; ?>
        
    </ul>
</nav>

<?php 
    $currentPage = 1;
    foreach ($pager->links() as $link) {
        if ($link['active']) { $currentPage = $link['title']; break; }
    }
?>
<div class="text-center mt-2">
    <small class="text-muted">
        Halaman <?= $currentPage ?> dari <?= $pageCount ?>
    </small>
</div>
