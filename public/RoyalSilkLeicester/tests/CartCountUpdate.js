const { Builder, By, until } = require('selenium-webdriver');

(async function testAddToCart() {
    let driver = await new Builder().forBrowser('chrome').build();

    try {
        // 1. Open the product page
        await driver.get('http://localhost/RoyalSilkLeicester/?route=product&id=1');

        // 2. Wait up to 15 seconds for the product details to appear
        let productDiv = await driver.wait(
            until.elementLocated(By.css('.product-details')),
            15000
        );

        console.log('Product details loaded.');

        // 3. Find the Add to Cart button and click it
        let addToCartBtn = await driver.findElement(By.css('button[name="add-to-cart"]'));
        await addToCartBtn.click();
        console.log('Clicked Add To Cart.');

        // 4. Wait a moment for the cart to update
        await driver.sleep(2000);

        // 5. Verify cart count
        let cartCountSpan = await driver.findElement(By.css('.cart span'));
        let cartCount = await cartCountSpan.getText();

        if (parseInt(cartCount) > 0) {
            console.log(`Test passed: Cart count is ${cartCount}`);
        } else {
            console.log('Test failed: Cart count did not update.');
        }

    } catch (err) {
        console.error('Error during test:', err);
    } finally {
        // Close the browser
        await driver.quit();
    }
})();