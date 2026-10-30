document.addEventListener('DOMContentLoaded', function() {
	var buttons = document.querySelectorAll('.add-to-cart[data-id]');
	if (!buttons.length) return;

	function updateCartCount(newCount) {
		document.querySelectorAll('.cart-count').forEach(function(badge) {
			badge.innerText = newCount;
			badge.style.transform = 'scale(1.2)';
			setTimeout(function(){ badge.style.transform = 'scale(1)'; }, 200);
		});
	}

	buttons.forEach(function(btn) {
		btn.addEventListener('click', async function() {
			if (btn.disabled) return;

			var productId = btn.getAttribute('data-id');
			if (!productId) return;

			var formData = new FormData();
			formData.append('product_id', productId);
			formData.append('qty', 1);

			try {
				var response = await fetch('index.php?page=cart-add', {
					method: 'POST',
					body: formData
				});
				var data = await response.json();
				if (data && data.success) {
					updateCartCount(data.newCount || 0);
					alert('Added to cart!');
				}
			} catch (error) {
				console.error('Add to cart error:', error);
			}
		});
	});
});
