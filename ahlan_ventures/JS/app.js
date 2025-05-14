//script>
    document.addEventListener('DOMContentLoaded', function () {
        const cartButtons = document.querySelectorAll('.add-to-cart-btn');
        const cartCount = document.getElementById('cart-count');

         cartButtons.forEach(button => {
            button.addEventListener('click', function () {
                const form = this.closest('.add-to-cart-form');
                const productId = form.getAttribute('data-product-id');
                const productName = form.getAttribute('data-product-name');
                const productPrice = form.getAttribute('data-product-price');
                const productImage = form.getAttribute('data-product-image');
                const productDescription = form.getAttribute('data-product-description');

                // Simulate adding to cart (you can replace this with an AJAX request)
                console.log(`Added to cart: ${productName} (ID: ${productId}, Price: ₦${productPrice})`);
                alert(`${productName} has been added to your cart!`);

                // Update cart count (simulate increment)
                const currentCount = parseInt(cartCount.textContent) || 0;
                cartCount.textContent = currentCount + 1;
            });
        });
    });
//</script>