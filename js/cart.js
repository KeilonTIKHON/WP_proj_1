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


    function sendAjax(action, data, callback) {
        fetch('/wp-admin/admin-ajax.php', {
            method: "POST",
            headers: {
                "Content-Type": "application/x-www-form-urlencoded",
            },
            body: new URLSearchParams({
                action: action,
                ...data
            })
        })
            .then(res => res.json())
            .then(response => {
                if (response.success) {
                    Cookies.set("cart", JSON.stringify(response.data), { expires: 7 });
                    updateCartDisplay();
                    if (callback) callback(response.data);
                } else {
                    console.error("Ошибка AJAX:", response);
                }
            });
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
        sendAjax("add_to_cart", {
            id: id,
            qty: 1
        }
        );
    };


    cartIcon.addEventListener("click", () => {
        cartPopup.style.display = cartPopup.style.display === "none" ? "block" : "none";
        updateCartDisplay();
    });


    cartItemsContainer.addEventListener("click", function (e) {
        if (e.target.classList.contains("remove-btn")) {
            const id = e.target.dataset.id;
            sendAjax("remove_from_cart", { id: id });
        }
    });

    cartItemsContainer.addEventListener("change", function (e) {
        if (e.target.classList.contains("qty-input")) {
            const id = e.target.dataset.id;
            const qty = parseInt(e.target.value);
            
            if (qty>0) {
                sendAjax("update_cart_qty", { id: id, qty: qty });
            }
        }
    });

    updateCartDisplay();
});