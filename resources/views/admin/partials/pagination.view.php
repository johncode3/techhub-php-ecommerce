<?php
/** @var int $currentPage */
/** @var int $totalPages */
?>
<?php
$params = $_GET;
unset($params['p']);
$baseUrl = "index.php?" . http_build_query($params);
$window = 2;
?>

<?php if ($totalPages > 1): ?>
<div class="admin-table-footer" style="display: flex; flex-direction: column; gap: 15px; align-items: center;">
    
    <div class="admin-pagination" style="display: flex; align-items: center; gap: 8px;">
        
        <?php if ($currentPage > 1): ?>
            <a href="<?= $baseUrl ?>&p=<?= $currentPage - 1 ?>" class="pagination-link"><i class="fas fa-chevron-left"></i></a>
        <?php endif; ?>

        <a href="<?= $baseUrl ?>&p=1" class="pagination-link <?= ($currentPage == 1) ? 'active' : '' ?>">1</a>

        <?php if ($currentPage > $window + 2): ?>
            <span class="pagination-dots">...</span>
        <?php endif; ?>

        <?php
        $start = max(2, $currentPage - $window);
        $end = min($totalPages - 1, $currentPage + $window);

        for ($i = $start; $i <= $end; $i++): ?>
            <a href="<?= $baseUrl ?>&p=<?= $i ?>" class="pagination-link <?= ($i == $currentPage) ? 'active' : '' ?>">
                <?= $i ?>
            </a>
        <?php endfor; ?>

        <?php if ($currentPage < $totalPages - ($window + 1)): ?>
            <span class="pagination-dots">...</span>
        <?php endif; ?>

        <?php if ($totalPages > 1): ?>
            <a href="<?= $baseUrl ?>&p=<?= $totalPages ?>" class="pagination-link <?= ($currentPage == $totalPages) ? 'active' : '' ?>">
                <?= $totalPages ?>
            </a>
        <?php endif; ?>

        <?php if ($currentPage < $totalPages): ?>
            <a href="<?= $baseUrl ?>&p=<?= $currentPage + 1 ?>" class="pagination-link"><i class="fas fa-chevron-right"></i></a>
        <?php endif; ?>
    </div>

    <div class="page-jump" style="display: flex; align-items: center; gap: 10px; font-size: 14px;">
        <label for="jump-input">Jump to page:</label>
        <input type="number" id="jump-input" min="1" max="<?= $totalPages ?>" value="<?= $currentPage ?>" 
               style="width: 60px; padding: 5px; border: 1px solid #ddd; border-radius: 4px;">
        <button type="button" class="btn btn-edit" onclick="jumpToPage()" style="padding: 5px 15px;">Go</button>
    </div>
</div>

<script>
function jumpToPage() {
    const input = document.getElementById('jump-input');
    const pageNum = input.value;
    const maxPage = <?= $totalPages ?>;
    
    if (pageNum >= 1 && pageNum <= maxPage) {
        window.location.href = "<?= $baseUrl ?>&p=" + pageNum;
    } else {
        alert("Please enter a page between 1 and " + maxPage);
    }
}
</script>
<?php endif; ?>