flow of data for a product:
If user logged in ? (check if user has any old cart ? fetch old cart : create new cart) : (create a temporary cart)

how to fetch data from user's device(){
add products to cart in db's sales_cart table?
- Create a cookie in user's device and fetch it every 10 seconds for products.
- until a new fetch is made store cart's data in cookie.

cookie has (is_edited Boolean variable (true by default), session id, data of products in the cart, temp data of cookie until last fetch(empty if no fetch is made)) store it for 10 days in user's system


what happens to cookie when it has been fetched?
- check if is_edited variable 
	true:
		update cart with cookie's data
		is_edited = false
	false:
		don't fire query of updating db

what happens when cookie is edited?
- transfer old data except is_edited to temp data of cookie until last fetch
- add products to cookie
- is_edited = true

in case there is some error storing data to cookie or at user's end, revert cookie to old state using temp data of cookie until last fetch and set is_edited = false.
}





