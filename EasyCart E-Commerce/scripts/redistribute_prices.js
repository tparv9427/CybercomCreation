const fs = require('fs');

// Read products
const productsData = JSON.parse(fs.readFileSync('data/products.json', 'utf8'));

// Update products based on ID explicitly
Object.keys(productsData).forEach(key => {
    const product = productsData[key];
    const productId = parseInt(product.id || key); // Use explicit ID property or key

    const discountPercent = product.discount_percent;

    if (productId <= 250) {
        // IDs 1-250: Express category (price <= 299)
        if (product.price > 299) {
            // Force price down
            const newPrice = Math.floor(Math.random() * (299 - 50 + 1)) + 50;
            product.price = parseFloat(newPrice.toFixed(2));

            if (discountPercent > 0) {
                product.original_price = parseFloat((newPrice / (1 - discountPercent / 100)).toFixed(2));
            } else {
                product.original_price = product.price;
            }
        }
    } else {
        // IDs 251-500: Freight category (price >= 300)
        // Force update even if already high, just to be sure, or check condition
        if (product.price < 300) {
            // Force price up
            const newPrice = Math.floor(Math.random() * (1000 - 300 + 1)) + 300;
            product.price = parseFloat(newPrice.toFixed(2));

            if (discountPercent > 0) {
                product.original_price = parseFloat((newPrice / (1 - discountPercent / 100)).toFixed(2));
            } else {
                product.original_price = product.price;
            }
        }
    }
});

fs.writeFileSync('data/products.json', JSON.stringify(productsData, null, 4));
console.log('✓ Product prices corrected based on explicit IDs!');
console.log('✓ IDs 1-250: price <= 299');
console.log('✓ IDs 251+: price >= 300');
