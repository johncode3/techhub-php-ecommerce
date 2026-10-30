<?php require_once '../resources/views/admin/layouts/header.view.php'; ?>
<?php
/** @var array $categories */
/** @var array $sub_categories */
/** @var array $brands */
$categories = $categories ?? [];
$sub_categories = $sub_categories ?? [];
$brands = $brands ?? [];
?>

<main>
    <h1>Add New Product</h1>
    <form class="admin-form" action="index.php?page=admin-product-create" method="post" enctype="multipart/form-data">
        <div class="form-row">
            <div class="form-group" style="margin-top: 15px;">
                <label>Product Name</label>
                <input type="text" id="product-name" name="name" required class="filter-input">
            </div>
            <div class="form-group" style="margin-top: 15px;">
                <label>Brand</label>
                <select id="product-brand" name="brand_id" required class="filter-input">
                    <option value="">Select Brand</option>
                    <?php foreach ($brands as $brand): ?>
                        <option value="<?= $brand['id'] ?>"><?= htmlspecialchars($brand['name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group" style="margin-top: 15px;">
                <label>Category</label>
                <select id="product-category" name="category_id" required class="filter-input">
                    <option value="">Select Category</option>
                    <?php foreach ($categories as $cat): ?>
                        <option value="<?= $cat['id'] ?>"><?= htmlspecialchars($cat['name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group" style="margin-top: 15px;">
                <label>Subcategory</label>
                <select id="product-subcategory" name="sub_category_id" class="filter-input">
                    <option value="">Select Subcategory</option>
                    <?php foreach ($sub_categories as $sub): ?>
                        <option value="<?= $sub['id'] ?>" data-parent="<?= $sub['category_id'] ?>" style="display:none;">
                            <?= htmlspecialchars($sub['name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>
        <div class="form-row">
            <div class="form-group" style="margin-top: 15px;">
                <label for="product-price">Price ($)</label>
                <input type="number" id="product-price" name="price" min="0" step="0.01" required class="filter-input">
            </div>
            <div class="form-group" style="margin-top: 15px;">
                <label for="product-original-price">Original Price ($)</label>
                <input type="number" id="product-original-price" name="original_price" min="0" step="0.01" class="filter-input">
            </div>
            <div class="form-group" style="margin-top: 15px;">
                <label for="product-stock">Stock</label>
                <input type="number" id="product-stock" name="stock" min="0" required class="filter-input">
            </div>
            <div class="form-group" style="margin-top: 15px;">
                <label for="product-status">Status</label>
                <select id="product-status" name="status" required class="filter-input">
                    <option value="1">Active</option>
                    <option value="0">Inactive</option>
                </select>
            </div>
        </div>
        <div class="form-row">
            <div class="form-group" style="margin-top: 15px;">
                <label for="product-rating">Fake Rating (0 - 5)</label>
                <input type="number" id="product-rating" name="rating" 
                    min="0" max="5" step="0.1" class="filter-input" 
                    value="0.0">
            </div>
            <div class="form-group" style="margin-top: 15px;">
                <label for="product-reviews">Review Count</label>
                <input type="number" id="product-reviews" name="review_count" 
                    min="0" class="filter-input" 
                    value="0">
            </div>
        </div>
        <div class="form-row">
            <div class="form-group" style="margin-top: 15px;">
                <label for="product-image">Main Image</label>
                <input type="file" id="product-image" name="main_image" class="filter-input" accept="image/*">
                <div id="main-preview" style="display:flex;gap:8px;flex-wrap:wrap;margin-top:8px;"></div>
                <button type="button" id="main-clear" class="btn btn-delete" style="margin-top:6px;">Clear main image</button>
            </div>
            <div class="form-group" style="margin-top: 15px;">
                <label for="product-thumbnails">Thumbnails (up to 3)</label>
                <input type="file" id="product-thumbnails" name="thumbnails[]" class="filter-input" accept="image/*" multiple>
                <div id="thumb-preview" style="display:flex;gap:8px;flex-wrap:wrap;margin-top:8px;"></div>
                <button type="button" id="thumb-clear" class="btn btn-delete" style="margin-top:6px;">Clear thumbnails</button>
                <small style="color:#666;">You can pick thumbnails one by one; they will be kept (max 3).</small>
            </div>
            <div class="form-group" style="margin-top: 15px; flex:2;">
                <label for="product-description">Description</label>
                <textarea id="product-description" name="description" rows="4" class="filter-input"></textarea>
            </div>
        </div>
        <div class="form-row">
            <div class="form-group" style="margin-top: 15px;">
                <label for="product-specifications">Specifications</label>
                <textarea id="product-specifications" name="specifications" rows="3" class="filter-input" placeholder="Display: 6.1-inch | Chip: A17"></textarea>
            </div>
            <div class="form-group" style="margin-top: 15px;">
                <label for="product-color">Color</label>
                <input type="text" id="product-color" name="color" class="filter-input">
            </div>
            <div class="form-group" style="margin-top: 15px;">
                <label for="product-storage">Storage</label>
                <input type="text" id="product-storage" name="storage" class="filter-input">
            </div>
        </div>
        <div class="form-row">

        </div>
        <div class="form-row">
            <div class="form-group" style="margin-top: 15px;">
                <label>
                    <input type="checkbox" name="is_new" value="1"> Mark as New
                </label>
            </div>
        </div>
        <div class="form-actions">
            <button type="submit" name="add_product" class="btn btn-add">Add Product</button>
            <a href="index.php?page=admin-products" class="btn btn-delete">Cancel</a>
        </div>
    </form>
</main>
<script>
const thumbInput = document.getElementById('product-thumbnails');
const thumbPreview = document.getElementById('thumb-preview');
const mainInput = document.getElementById('product-image');
const mainPreview = document.getElementById('main-preview');
const mainClear = document.getElementById('main-clear');
const thumbClear = document.getElementById('thumb-clear');
let selectedThumbs = [];

function renderThumbs(files) {
    if (!thumbPreview) return;
    thumbPreview.innerHTML = '';
    const list = Array.from(files).slice(0, 3);
    list.forEach(file => {
        const img = document.createElement('img');
        img.style.width = '56px';
        img.style.height = '56px';
        img.style.objectFit = 'cover';
        img.style.borderRadius = '6px';
        img.alt = file.name;
        const reader = new FileReader();
        reader.onload = e => { img.src = e.target.result; };
        reader.readAsDataURL(file);
        thumbPreview.appendChild(img);
    });
}

if (thumbInput) {
    thumbInput.addEventListener('change', () => {
        const incoming = Array.from(thumbInput.files);
        selectedThumbs = selectedThumbs.concat(incoming).slice(0, 3);
        const dt = new DataTransfer();
        selectedThumbs.forEach(file => dt.items.add(file));
        thumbInput.files = dt.files;
        renderThumbs(thumbInput.files);
    });
}

function renderMain(file) {
    if (!mainPreview) return;
    mainPreview.innerHTML = '';
    if (!file) return;
    const img = document.createElement('img');
    img.style.width = '80px';
    img.style.height = '80px';
    img.style.objectFit = 'cover';
    img.style.borderRadius = '8px';
    img.alt = file.name;
    const reader = new FileReader();
    reader.onload = e => { img.src = e.target.result; };
    reader.readAsDataURL(file);
    mainPreview.appendChild(img);
}

if (mainInput) {
    mainInput.addEventListener('change', () => renderMain(mainInput.files[0]));
}

if (mainClear) {
    mainClear.addEventListener('click', () => {
        if (mainInput) {
            mainInput.value = '';
        }
        renderMain(null);
    });
}

if (thumbClear) {
    thumbClear.addEventListener('click', () => {
        selectedThumbs = [];
        if (thumbInput) {
            thumbInput.value = '';
        }
        renderThumbs([]);
    });
}
</script>
<script>
document.getElementById('product-category').addEventListener('change', function() {
    const selectedCategoryId = this.value;
    const subcatSelect = document.getElementById('product-subcategory');
    const options = subcatSelect.querySelectorAll('option');

    // Reset subcategory selection
    subcatSelect.value = "";

    options.forEach(option => {
        if (option.value === "") return; 
        if (option.getAttribute('data-parent') === selectedCategoryId) {
            option.style.display = 'block';
        } else {
            option.style.display = 'none';
        }
    });
});
</script>