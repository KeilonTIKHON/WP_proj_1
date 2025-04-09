document.addEventListener("DOMContentLoaded", function () {
    const cartIcon = document.getElementById("cart-icon");
    const cartPopup = document.getElementById("cart-popup");
    const cartItemsContainer = document.getElementById("cart-items");
    const cartTotal = document.getElementById("cart-total");
    const cartCount = document.getElementById("cart-count");

    
    function getCart() {
        let cart = Cookies.get("cart");
        return cart ? JSON.parse(cart) : [];
    }

    
    function saveCart(cart) {
        Cookies.set("cart", JSON.stringify(cart), { expires: 7 });
        updateCartDisplay();
    }

    
    function updateCartDisplay() {
        const cart = getCart();
        cartItemsContainer.innerHTML = "";
        let total = 0;
        let count = 0;

        cart.forEach(item => {
            total += item.price * item.qty;
            count += item.qty;

            const itemEl = document.createElement("div");
            itemEl.innerHTML = `
                <strong>${item.name}</strong> - ${item.price} $ x 
                <input type="number" value="${item.qty}" min="1" data-id="${item.id}" class="qty-input" style="width:40px;"> 
                <button class="remove-btn" data-id="${item.id}">Delete</button>
            `;
            cartItemsContainer.appendChild(itemEl);
        });

        cartTotal.textContent = total + " $";
        cartCount.textContent = count;
    }

    
    window.addToCart = function (id, name, price) {
        const cart = getCart();
        const itemIndex = cart.findIndex(item => item.id === id);

        if (itemIndex > -1) {
            cart[itemIndex].qty += 1;
        } else {
            cart.push({ id, name, price, qty: 1 });
        }

        saveCart(cart);
    };

    
    cartIcon.addEventListener("click", () => {
        cartPopup.style.display = cartPopup.style.display === "none" ? "block" : "none";
        updateCartDisplay();
    });

    
    cartItemsContainer.addEventListener("click", function (e) {
        if (e.target.classList.contains("remove-btn")) {
            const id = e.target.dataset.id;
            let cart = getCart();
            cart = cart.filter(item => item.id !== id);
            saveCart(cart);
        }
    });

    cartItemsContainer.addEventListener("change", function (e) {
        if (e.target.classList.contains("qty-input")) {
            const id = e.target.dataset.id;
            const qty = parseInt(e.target.value);
            let cart = getCart();
            const item = cart.find(item => item.id === id);
            if (item) {
                item.qty = qty > 0 ? qty : 1;
                saveCart(cart);
            }
        }
    });

    updateCartDisplay();
});