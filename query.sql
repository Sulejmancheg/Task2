-- a.   Для заданного списка товаров получить названия всех категорий, в которых представлены товары
    SELECT DISTINCT c.name FROM category c
        JOIN goods_category gc ON c.id = gc.id_category
        JOIN goods g ON gc.id_goods = g.id
        WHERE g.name IN ('Product 005', 'Product 006', 'Product 056')
        ORDER BY c.name

-- b.   Для заданной категории получить список предложений всех товаров из этой категории и ее дочерних категорий
    SELECT DISTINCT g.name, g.price FROM goods g
        JOIN goods_category gc ON g.id = gc.id_goods
        JOIN category_closure cc ON gc.id_category = cc.descendant
        JOIN category c ON cc.ancestor = c.id
        WHERE c.name = 'Category 15'
        ORDER BY g.name

-- c.   Для заданного списка категорий получить количество предложений товаров в каждой категории
    SELECT c.name, COUNT(*) AS Quantity FROM category c
        JOIN goods_category gc ON c.id = gc.id_category
        WHERE c.name IN ('Category 08', 'Category 09', 'Category 17', 'Category 21')
        GROUP BY c.name
        ORDER BY c.name
		
-- d.   Для заданного списка категорий получить общее количество уникальных предложений товара;
    SELECT COUNT(DISTINCT gc.id_goods) FROM goods_category gc
        JOIN category c ON gc.id_category = c.id
        WHERE c.name IN ('Category 08', 'Category 09', 'Category 11', 'Category 16', 'Category 24')
		
-- e.   Для заданной категории получить ее полный путь в дереве (breadcrumb, «хлебные крошки»)
    SELECT c.name FROM category c
        JOIN category_closure cc ON c.id = cc.ancestor
        WHERE cc.descendant IN (
            SELECT c.id FROM category c
            WHERE c.name = 'Category 31')
        ORDER BY cc.depth DESC
			
