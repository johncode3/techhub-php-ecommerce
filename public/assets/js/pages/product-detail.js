/**
 * Product Detail Logic - TechHub
 * Handles Variant Selection, Quantity, and Cart Actions
 */
document.addEventListener('DOMContentLoaded', function() {
    let selectedColor = null;
    let selectedStorage = null;
    
    // 1. SELECT ELEMENTS
    const qtyInput = document.querySelector('.quantity-selector input');
    const plusBtn = document.querySelector('.qty-btn.plus');
    const minusBtn = document.querySelector('.qty-btn.minus');
    const addToCartBtn = document.getElementById('add-to-cart-btn');
    const buyNowBtn = document.getElementById('buy-now-btn');
    const mainImage = document.querySelector('.main-image img');
    const thumbImages = document.querySelectorAll('.js-lightbox-thumb');
    const lightbox = document.querySelector('.lightbox');
    const lightboxImage = document.querySelector('.lightbox__image');
    const lightboxClose = document.querySelector('.lightbox__close');

    // 2. QUANTITY LOGIC
    if (plusBtn && minusBtn && qtyInput) {
        const maxStock = parseInt(qtyInput.getAttribute('max')) || 0;

        plusBtn.addEventListener('click', () => {
            let current = parseInt(qtyInput.value);
            if (current < maxStock) qtyInput.value = current + 1;
        });

        minusBtn.addEventListener('click', () => {
            let current = parseInt(qtyInput.value);
            if (current > 1) qtyInput.value = current - 1;
        });
    }

    // 3. COLOR SELECTION
    const colorBtns = document.querySelectorAll('.color-option');
    colorBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            colorBtns.forEach(b => b.classList.remove('active'));
            this.classList.add('active');
            selectedColor = this.getAttribute('data-value');
        });
    });

    // 4. STORAGE SELECTION
    const storageBtns = document.querySelectorAll('.storage-option');
    storageBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            storageBtns.forEach(b => b.classList.remove('active'));
            this.classList.add('active');
            selectedStorage = this.getAttribute('data-value');
        });
    });

    // 4.5 IMAGE SWAP + LIGHTBOX
    function openLightbox(src, alt) {
        if (!lightbox || !lightboxImage) return;
        lightboxImage.src = src;
        lightboxImage.alt = alt || 'Product image';
        lightbox.classList.add('active');
        lightbox.setAttribute('aria-hidden', 'false');
        document.body.style.overflow = 'hidden';
    }

    function closeLightbox() {
        if (!lightbox || !lightboxImage) return;
        lightbox.classList.remove('active');
        lightbox.setAttribute('aria-hidden', 'true');
        lightboxImage.src = '';
        document.body.style.overflow = '';
    }

    if (mainImage) {
        mainImage.addEventListener('click', () => {
            openLightbox(mainImage.src, mainImage.alt);
        });
    }

    thumbImages.forEach((thumb) => {
        thumb.addEventListener('click', () => {
            if (mainImage) {
                mainImage.src = thumb.src;
                mainImage.alt = thumb.alt || mainImage.alt;
            }
            thumbImages.forEach(t => t.classList.remove('active'));
            thumb.classList.add('active');
        });
    });

    if (lightbox) {
        lightbox.addEventListener('click', (event) => {
            if (event.target === lightbox) {
                closeLightbox();
            }
        });
    }

    if (lightboxClose) {
        lightboxClose.addEventListener('click', closeLightbox);
    }

    document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape') {
            closeLightbox();
        }
    });

    // 5. THE ADD TO CART FUNCTION (Now with Redirect!)
    async function addToCart(redirect = false) {
        if (!addToCartBtn) return;
        
        const productId = addToCartBtn.getAttribute('data-id');
        const qty = qtyInput ? qtyInput.value : 1;

        // Validation
        if (document.querySelector('.color-options') && !selectedColor) {
            alert("Please choose a color first!");
            return;
        }
        if (document.querySelector('.storage-options') && !selectedStorage) {
            alert("Please choose storage capacity!");
            return;
        }

        const formData = new FormData();
        formData.append('product_id', productId);
        formData.append('qty', qty);
        formData.append('color', selectedColor);
        formData.append('storage', selectedStorage);

        try {
            const response = await fetch('index.php?page=cart-add', {
                method: 'POST',
                body: formData
            });
            const data = await response.json();

            if (data.success) {
                // Update the Red Circle in Header
                const badges = document.querySelectorAll('.cart-count');
                badges.forEach(badge => {
                    badge.innerText = data.newCount;
                    badge.style.transform = "scale(1.5)";
                    setTimeout(() => { badge.style.transform = "scale(1)"; }, 200);
                });

                // THE BUY NOW REDIRECT LOGIC 🚨
                if (redirect) {
                    window.location.href = "index.php?page=checkout";
                } else {
                    alert("Added to bag! 🛒");
                }
            }
        } catch (error) {
            console.error("Fetch Error:", error);
        }
    }

    // 6. SINGLE EVENT LISTENERS (Inside the DOM block)
    if (addToCartBtn) {
        addToCartBtn.addEventListener('click', () => addToCart(false));
    }
    if (buyNowBtn) {
        buyNowBtn.addEventListener('click', () => addToCart(true));
    }
});