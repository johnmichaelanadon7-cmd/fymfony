# Product image upload guide

The Products form now supports JPG, PNG, and WebP images up to 5 MB.

1. Run `php bin/console doctrine:migrations:migrate` to add the `image_filename` column.
2. Open `/admin/products` and choose **New product**, or edit an existing product.
3. Find **Product image**, choose a JPG, PNG, or WebP photo, then save the form.
4. The uploaded file is stored in `public/uploads/products/` and its filename is saved in MySQL.
5. The product list and product detail page display the uploaded image. When you replace an image or delete a product, the previous image file is removed.

Use optimized product photos and keep each file at or below 5 MB.
