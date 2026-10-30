<?php
/** @var array $shop_categories */
/** @var array $shop_brands */
/** @var array $shop_products */
/** @var array $all_products */
/** @var array $shop_sub_categories */

$all_products = $all_products ?? [];
$shop_categories = $shop_categories ?? [];
$shop_brands = $shop_brands ?? [];
$displayProducts = !empty($all_products) ? $all_products : []; 
$shop_sub_categories = $shop_sub_categories ?? [];
?> 
<div class="product-header">
		<div class="container">
			<h1 class="page-title">Welcome To Our Shop</h1>
			<p class="page-description">
				Discover the latest technology gadgets and devices at unbeatable prices. From smartphones to gaming gear, find everything you need at TechHub.
			</p>
		</div>
	</div>

	<div class="container">
		<div class="product-container">       
			<aside class="filter-sidebar">
				<!-- Category Widget -->
				<div class="filter-widget">
					<h3 class="filter-title">Category</h3>
					<ul class="category-list">
						<?php foreach ($shop_categories as $cat): ?>
							<li class="category-item">
								<a href="index.php?page=product&category_id=<?= $cat['id'] ?>" 
								class="<?= (isset($_GET['category_id']) && $_GET['category_id'] == $cat['id']) ? 'active-filter' : '' ?>">
									<?= htmlspecialchars($cat['name']) ?>
									<span class="item-count">(<?= $cat['product_count'] ?>)</span>
								</a>
							</li>
						<?php endforeach; ?>
					</ul>
				</div>

				<!-- Sub Category Widget -->
				<div class="filter-widget">
					<h3 class="filter-title">Sub Category</h3>
					<ul class="category-list">
						<?php foreach ($shop_sub_categories as $sub): ?>
							<li class="category-item">
								<a href="index.php?page=product&sub_category_id=<?= $sub['id'] ?>" 
								class="<?= (isset($_GET['sub_category_id']) && $_GET['sub_category_id'] == $sub['id']) ? 'active-filter' : '' ?>">
									<?= htmlspecialchars($sub['name']) ?>
									<span class="item-count">(<?= $sub['product_count'] ?>)</span>
								</a>
							</li>
						<?php endforeach; ?>
					</ul>
				</div>

				<!-- Brand Widget -->
				<div class="filter-widget">
					<h3 class="filter-title">Brand</h3>
					<ul class="category-list">
						<?php foreach ($shop_brands as $brand): ?>
							<li class="category-item">
								<a href="index.php?page=product&brand_id=<?= $brand['id'] ?>"
								class="<?= (isset($_GET['brand_id']) && $_GET['brand_id'] == $brand['id']) ? 'active-filter' : '' ?>">
									<?= htmlspecialchars($brand['name']) ?>
									<!-- REAL COUNT HERE -->
									<span class="item-count">(<?= $brand['product_count'] ?>)</span>
								</a>
							</li>
						<?php endforeach; ?>
					</ul>
				</div>
				</aside>

			<main class="product-main">
						<?php
						function buildFilterUrl(array $params): string
						{
							return 'index.php?' . http_build_query($params);
						}

						$activeFilters = [];
						$baseParams = ['page' => 'product'];
						$selectedCategoryId = isset($_GET['category_id']) ? (int)$_GET['category_id'] : 0;
						$selectedSubCategoryId = isset($_GET['sub_category_id']) ? (int)$_GET['sub_category_id'] : 0;
						$selectedBrandId = isset($_GET['brand_id']) ? (int)$_GET['brand_id'] : 0;
						$selectedSearch = isset($_GET['search']) ? trim((string)$_GET['search']) : '';
						$selectedSort = isset($_GET['sort']) ? (string)$_GET['sort'] : 'featured';

						$categoryName = null;
						if ($selectedCategoryId) {
							foreach ($shop_categories as $cat) {
								if ((int)$cat['id'] === $selectedCategoryId) {
									$categoryName = $cat['name'];
									break;
								}
							}
						}

						$subCategoryName = null;
						if ($selectedSubCategoryId) {
							foreach ($shop_sub_categories as $sub) {
								if ((int)$sub['id'] === $selectedSubCategoryId) {
									$subCategoryName = $sub['name'];
									break;
								}
							}
						}

						$brandName = null;
						if ($selectedBrandId) {
							foreach ($shop_brands as $brand) {
								if ((int)$brand['id'] === $selectedBrandId) {
									$brandName = $brand['name'];
									break;
								}
							}
						}

						if ($selectedCategoryId) {
							$params = $baseParams;
							if ($selectedBrandId) {
								$params['brand_id'] = $selectedBrandId;
							}
							if ($selectedSearch !== '') {
								$params['search'] = $selectedSearch;
							}
							$activeFilters[] = [
								'label' => $categoryName ?? 'Category',
								'removeUrl' => buildFilterUrl($params),
							];
						}

						if ($selectedSubCategoryId) {
							$params = $baseParams;
							if ($selectedBrandId) {
								$params['brand_id'] = $selectedBrandId;
							}
							if ($selectedSearch !== '') {
								$params['search'] = $selectedSearch;
							}
							$activeFilters[] = [
								'label' => $subCategoryName ?? 'Sub Category',
								'removeUrl' => buildFilterUrl($params),
							];
						}

						if ($selectedBrandId) {
							$params = $baseParams;
							if ($selectedCategoryId) {
								$params['category_id'] = $selectedCategoryId;
							}
							if ($selectedSearch !== '') {
								$params['search'] = $selectedSearch;
							}
							$activeFilters[] = [
								'label' => $brandName ?? 'Brand',
								'removeUrl' => buildFilterUrl($params),
							];
						}

						if ($selectedSearch !== '') {
							$params = $baseParams;
							if ($selectedCategoryId) {
								$params['category_id'] = $selectedCategoryId;
							}
							if ($selectedBrandId) {
								$params['brand_id'] = $selectedBrandId;
							}
							$activeFilters[] = [
								'label' => 'Search: ' . $selectedSearch,
								'removeUrl' => buildFilterUrl($params),
							];
						}
						?>
				<div class="product-toolbar">
					<div class="toolbar-top">
						<div class="products-count">
							<?php
							$total_products = $total_products ?? count($displayProducts);
							$limit = $limit ?? 12;
							$current_page = $current_page ?? 1;
							if ($total_products > 0) {
								$start = ($current_page - 1) * $limit + 1;
								$end = min($current_page * $limit, $total_products);
								echo 'Showing ' . $start . '-' . $end . ' of ' . $total_products . ' products';
							} else {
								echo 'Showing 0 products';
							}
							?>
						</div>
					</div>
                    
					<div class="toolbar-bottom">
						<div class="toolbar-left">
							<div class="active-filters">
								<?php if (!empty($activeFilters)): ?>
									<?php foreach ($activeFilters as $filter): ?>
										<span class="filter-tag">
											<?= htmlspecialchars($filter['label']) ?>
											<button type="button" onclick="location.href='<?= htmlspecialchars($filter['removeUrl']) ?>'">&times;</button>
										</span>
									<?php endforeach; ?>
									<button class="clear-all" type="button" onclick="location.href='index.php?page=product'">Clear All</button>
								<?php endif; ?>
							</div>
						</div>

						<div class="toolbar-right">
							<form class="search-within" action="index.php" method="GET">
								<input type="hidden" name="page" value="product">
								<?php if ($selectedCategoryId): ?>
									<input type="hidden" name="category_id" value="<?= $selectedCategoryId ?>">
								<?php endif; ?>
								<?php if ($selectedBrandId): ?>
									<input type="hidden" name="brand_id" value="<?= $selectedBrandId ?>">
								<?php endif; ?>
								<?php if ($selectedSort): ?>
									<input type="hidden" name="sort" value="<?= htmlspecialchars($selectedSort) ?>">
								<?php endif; ?>
								<input type="text" name="search" placeholder="Search in results..." value="<?= htmlspecialchars($selectedSearch) ?>" aria-label="Search products">
								<button class="search-btn" type="submit" aria-label="Search">
									<i class="fas fa-search"></i>
								</button>
							</form>

							<div class="control-group">
								<form class="sort-options" action="index.php" method="GET">
									<input type="hidden" name="page" value="product">
									<?php if ($selectedCategoryId): ?>
										<input type="hidden" name="category_id" value="<?= $selectedCategoryId ?>">
									<?php endif; ?>
									<?php if ($selectedSubCategoryId): ?>
										<input type="hidden" name="sub_category_id" value="<?= $selectedSubCategoryId ?>">
									<?php endif; ?>
									<?php if ($selectedBrandId): ?>
										<input type="hidden" name="brand_id" value="<?= $selectedBrandId ?>">
									<?php endif; ?>
									<?php if ($selectedSearch !== ''): ?>
										<input type="hidden" name="search" value="<?= htmlspecialchars($selectedSearch) ?>">
									<?php endif; ?>
									<select class="sort-select" name="sort" aria-label="Sort products by" onchange="this.form.submit()">
										<option value="featured" <?= $selectedSort === 'featured' ? 'selected' : '' ?>>Featured</option>
										<option value="price-low" <?= $selectedSort === 'price-low' ? 'selected' : '' ?>>Price: Low to High</option>
										<option value="price-high" <?= $selectedSort === 'price-high' ? 'selected' : '' ?>>Price: High to Low</option>
										<option value="newest" <?= $selectedSort === 'newest' ? 'selected' : '' ?>>Newest Arrivals</option>
										<option value="best-selling" <?= $selectedSort === 'best-selling' ? 'selected' : '' ?>>Best Selling</option>
										<option value="rating" <?= $selectedSort === 'rating' ? 'selected' : '' ?>>Customer Rating</option>
									</select>
								</form>

								<div class="view-option">
									<button class="view-btn active" title="Grid View">
										<i class="fas fa-th"></i>
									</button>
									<button class="view-btn" title="List View">
										<i class="fas fa-list"></i>
									</button>
								</div>
							</div>
						</div>
					</div>
				</div>

				<div class="product-grid">
					<?php if (!empty($displayProducts)): ?>  
						<?php foreach ($displayProducts as $p): ?>
							<?php include __DIR__ . '/../admin/partials/product_card.view.php'; ?>
						<?php endforeach; ?>
					<?php else: ?>
						<p style="text-align: center; font-size: 1.2rem; color: #777;">
							No products found matching these filters.
						</p>
					<?php endif; ?>
				</div>

				<?php if (!empty($total_pages) && $total_pages > 1): ?>
					<?php
					$currentPage = $current_page ?? 1;
					$totalPages = $total_pages;
					include __DIR__ . '/../admin/partials/pagination.view.php';
					?>
				<?php endif; ?>
			</main>
		</div>
	</div>
<script src="assets/js/pages/products.js"></script>
