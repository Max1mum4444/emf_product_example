SELECT
	j.journal_id, j.action_type, j.product_id,
	b.title, b.description, b.price, b.image_1, b.image_2, b.image_3, b.created_at, b.updated_at, b.emf_category_id
FROM emf_products_example_app.emf_products_journal j
LEFT JOIN emf_products_example_app.emf_product b ON b.id = j.product_id
WHERE j.journal_id > :sql_last_value
	AND j.action_time < NOW()
ORDER BY j.journal_id
