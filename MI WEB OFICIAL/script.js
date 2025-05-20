document.addEventListener('DOMContentLoaded', function() {

    let cart = JSON.parse(localStorage.getItem('cart')) || [];
    updateCartCount();
    

    document.querySelectorAll('.btn-add-cart').forEach(button => {
        button.addEventListener('click', function() {
            const productId = this.getAttribute('data-id');
            const productName = this.parentElement.querySelector('h3').textContent;
            const productPrice = parseFloat(this.parentElement.querySelector('p').textContent.replace('$', ''));
            const productImg = this.parentElement.querySelector('img').src;
            
            addToCart(productId, productName, productPrice, productImg);
        });
    });
    
    function addToCart(id, name, price, img) {
       
        const existingItem = cart.find(item => item.id === id);
        
        if (existingItem) {
            existingItem.quantity += 1;
        } else {
            cart.push({
                id,
                name,
                price,
                img,
                quantity: 1
            });
        }
        
    
        localStorage.setItem('cart', JSON.stringify(cart));
        updateCartCount();
        
      
        alert(`${name} ha sido añadido al carrito`);
    }
    
    function updateCartCount() {
        const count = cart.reduce((total, item) => total + item.quantity, 0);
        document.getElementById('cart-count').textContent = count;
    }
    
   
    if (document.querySelector('.cart-page')) {
        loadCartItems();
        
  
        document.querySelector('.cart-items').addEventListener('click', function(e) {
            if (e.target.classList.contains('remove-item')) {
                const itemId = e.target.getAttribute('data-id');
                removeFromCart(itemId);
                loadCartItems();
            }
            
            if (e.target.classList.contains('update-quantity')) {
                const itemId = e.target.getAttribute('data-id');
                const newQuantity = parseInt(e.target.value);
                updateQuantity(itemId, newQuantity);
                loadCartItems();
            }
        });
    }
    
    function loadCartItems() {
        const cartItemsContainer = document.querySelector('.cart-items');
        const cartTotalElement = document.querySelector('.cart-total');
        
        if (cart.length === 0) {
            cartItemsContainer.innerHTML = '<p>Tu carrito está vacío</p>';
            cartTotalElement.textContent = '$0.00';
            return;
        }
        
        let html = '';
        let total = 0;
        
        cart.forEach(item => {
            const itemTotal = item.price * item.quantity;
            total += itemTotal;
            
            html += `
                <div class="cart-item">
                    <img src="${item.img}" alt="${item.name}">
                    <div class="item-details">
                        <h3>${item.name}</h3>
                        <p>$${item.price.toFixed(2)}</p>
                        <div class="quantity-control">
                            <label>Cantidad:</label>
                            <input type="number" min="1" value="${item.quantity}" 
                                   class="update-quantity" data-id="${item.id}">
                            <button class="remove-item" data-id="${item.id}">Eliminar</button>
                        </div>
                        <p class="item-total">Total: $${itemTotal.toFixed(2)}</p>
                    </div>
                </div>
            `;
        });
        
        cartItemsContainer.innerHTML = html;
        cartTotalElement.textContent = `$${total.toFixed(2)}`;
        updateCartCount();
    }
    
    function removeFromCart(id) {
        cart = cart.filter(item => item.id !== id);
        localStorage.setItem('cart', JSON.stringify(cart));
        updateCartCount();
    }
    
    function updateQuantity(id, quantity) {
        if (quantity < 1) {
            removeFromCart(id);
            return;
        }
        
        const item = cart.find(item => item.id === id);
        if (item) {
            item.quantity = quantity;
            localStorage.setItem('cart', JSON.stringify(cart));
            updateCartCount();
        }
    }
});



async function saveOrderToDatabase(orderData) {
    try {
        const response = await fetch('guardar_pedido.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(orderData)
        });
        
        return await response.json();
    } catch (error) {
        console.error('Error:', error);
        return { success: false, error: 'Error de conexión' };
    }
}

function setupCheckout() {
    document.getElementById('proceed-checkout').addEventListener('click', function() {

    });
    
    document.getElementById('address-form').addEventListener('submit', async function(e) {
        e.preventDefault();
    
        
        const orderData = {
       
        };
        
       
        const result = await saveOrderToDatabase(orderData);
        
        if (result.success) {
    
        } else {
            alert('Error: ' + (result.error || 'Error desconocido'));
        }
    });
}


document.addEventListener('DOMContentLoaded', function() {
    loadCartItems();
    setupCheckout();
});